# Laravel Ember Lightning [![Code Climate](https://codeclimate.com/github/b-lab-org/laravel-ember-lightning/badges/gpa.svg)](https://codeclimate.com/github/b-lab-org/laravel-ember-lightning)
A Laravel application to facilitate the [Ember Lightning Deploy Strategy](http://ember-cli-deploy.com/docs/v0.6.x/the-lightning-strategy/)

## Prerequisites
* [Docker Compose](https://docs.docker.com/compose/install/)

## Getting Started
* `./impact.sh init`

#### Host File
```
sudo nano /etc/hosts

# /etc/hosts
ember.impact.dev your.ip.address
```

### Required `ENV` variables
* `APP_ENV`: The `environment` of the application, e.g. `dev`, `test`, `production`, etc.
* `APP_KEY`: Used by the Illuminate encrypter service. Should be a random 32 character string.
* `REDIS_HOST`: The Redis host (default: `localhost`)
* `REDIS_PASSWORD`: The Redis password (default: `null`)
* `REDIS_PORT`: The Redis port (default: `6379`)
* `REDIS_DB`: The Redis database (default `0`)
* `EMBER_APP`: This should match the `name` in your Ember app's `package.json` file. It is the key used by Redis to store your application.

## Running Tests
* `./impact.sh test`

## Contributing
This project follows [GitHub Flow](http://scottchacon.com/2011/08/31/github-flow.html).

* Branch `master`
* Code all the things
* Submit Pull Request
    * All tests must pass
    * Code will be vetted by B Lab Team
* Branch merged in to `master` and deployed
