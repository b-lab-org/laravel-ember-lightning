<?php

Route::get('{page?}', 'FrontController@index')->where(['page' => '[a-zA-Z0-9\S]+']);
