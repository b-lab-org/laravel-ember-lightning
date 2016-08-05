<?php

Route::get('{page?}', 'FrontController@index')->where(['page' => '[-a-z0-9/]+']);
