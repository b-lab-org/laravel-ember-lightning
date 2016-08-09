<?php namespace App\Http\Controllers;

use Request;

class FrontController extends Controller {

    public function index()
    {
        // open redis connection
        $redis = \Illuminate\Support\Facades\Redis::connection();

        // was any specific version specified?
        $key = Request::input('key');

        // try and fetch current application or revision
        $emberApp = env('EMBER_APP');
        try {
            $index = empty($key) ? "$emberApp:" . $redis->get("$emberApp:current") : $key;

            // fetch app contents
            $application = $redis->get($index);
            if (empty($application)) {
                return trans('errors.redis.default');
            }

            return $application;

        } catch (\Exception $e) {
            return trans('errors.redis.default');
        }
    }
}
