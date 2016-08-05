<?php namespace App\Http\Controllers;

use Request;

class FrontController extends Controller {

    public function index()
    {
        // was any specific version specified?
        $key = Request::input('key');

        // get index from redis by version
        $redis = \Illuminate\Support\Facades\Redis::connection();

        $emberApp = env('EMBER_APP');
        $index = empty($key) ? "$emberApp:" . $redis->get("$emberApp:current") : $key;
        return $redis->get($index);
    }
}
