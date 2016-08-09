#!/bin/bash

# A helper script to wrap up docker commands.
#
# Requirements:
#   - Install Docker
#   - anything else?

# compose config files per environment
BASE_CONFIG=docker-compose.yml
DEV_CONFIG=docker-compose.dev.yml
TEST_CONFIG=docker-compose.test.yml

# bash colors
START='\033[0;36m'
OK='\033[0;32m'
RESET='\033[0;0m'

# bring up the dev environment
function up {
    docker-compose -f $DEV_CONFIG stop
    docker-compose -f $DEV_CONFIG build
    docker-compose -f $DEV_CONFIG up -d
}

# stop containers
function down {
    docker-compose -f $DEV_CONFIG stop
}

# remove exited containers
function remove_exited {
    docker rm -v $(docker ps -a -q -f status=exited)
}

# remove containers
function remove_containers {
    docker rm -f $(docker ps -a -q --filter="name=laravelemberlightning-*")
}

# remove mounted volumes
function unmount_volumes {
    docker volume rm $(docker volume ls -q)
}

# remove dangling images
function remove_dangling_images {
    docker images -q --filter "dangling=true" | xargs docker rmi
}

# clear any generated logs
function truncate_logs {
    LOG_DIR=storage/logs
    > $LOG_DIR/access.nginx.log
    > $LOG_DIR/error.nginx.log
    > $LOG_DIR/redis.log
    rm $LOG_DIR/laravel*.log
}

if [[ $1 = 'init' ]]; then
    # setup local development from scratch.
    down
    remove_containers
    cp docker/env/.env.dev .env
    truncate_logs

    # build and start
    docker-compose -f $DEV_CONFIG build
    docker-compose -f $DEV_CONFIG up -d

    # get dependencies
    docker-compose -f $DEV_CONFIG run composer update

    # ready
    printf "${OK}ready for lightning deployments...\033[0;0m\n"

elif [[ $1 = 'up' ]]; then
    # setup local development
    docker-compose -f $DEV_CONFIG build
    docker-compose -f $DEV_CONFIG up -d

elif [[ $1 = 'down' ]]; then
    # stop containers
    down

elif [[ $1 = 'list' ]]; then
    # list all containers
    docker ps -a

elif [[ $1 = 'logs' ]]; then
    # show logs for environment
    docker-compose -f $DEV_CONFIG logs

elif [[ $1 = 'clean' ]]; then
    # remove all containers
    down
    remove_containers

elif [[ $1 = 'composer' ]]; then
    # run a composer command
    docker-compose -f $DEV_CONFIG run composer $2

elif [[ $1 = 'artisan' ]]; then
    # run an artisan command
    docker-compose -f $DEV_CONFIG run artisan $2 $3

elif [[ $1 = 'test' ]]; then
    # run codeception tests
    compose="docker-compose -f $TEST_CONFIG"

    # setup test environment - always start from scratch.
    # tear down dev containers - if running
    printf "${OK}tearing down...\n"
    eval $compose stop
    remove_containers
    unmount_volumes
    remove_dangling_images
    printf "${OK}done.\n"

    # cleanup report, dump file, truncate logs
    printf "${START}cleaning up misc. files and logs...\n"
    truncate_logs
    rm tests/_output/report.*
    printf "${OK}done.\n"

    # get env.test, composer update and start up test environment
    printf "${START}building test environment...\n"
    cp docker/env/.env.test .env
    eval $compose run composer update
    eval $compose up -d
    printf "${OK}done\n"

    # kick off tests
    printf "${START}running tests...\n"
    eval $compose run codeception run $2 $3 $4 --json
    printf "${OK}tests complete.\n"

    # exit with proper status based on test results
    ERRORS="$(grep -o '\"status\": \"error\"' tests/_output/report.json | wc -l)"
    FAILS="$(grep -o '\"status\": \"fail\"' tests/_output/report.json | wc -l)"
    if [[ ERRORS -eq 0 && FAILS -eq 0 ]]; then
        # found no errors
        exit 0
    else
        # found errors, exit with proper status code for CI
        exit 1
    fi

else
    # not a valid command
    echo ""
    echo "Available Commands:"
    echo "-------------------"
    echo "init - initialize local dev environment from scratch"
    echo "up - start local dev environment"
    echo "down - stop local dev environment"
    echo "list - list containers"
    echo "logs <environment> - show docker-compose logs (default: dev)"
    echo "clean - remove containers"
    echo "composer <command> - run a composer command"
    echo "artisan <command> - run an artisan command"
    echo "test <command> - run codeception tests"
    echo ""
fi
