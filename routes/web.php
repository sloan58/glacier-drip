<?php

use Illuminate\Support\Facades\Route;

Route::get('/xdebug', function () { xdebug_info(); });
