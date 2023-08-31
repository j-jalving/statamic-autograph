<?php

use Illuminate\Support\Facades\Route;
use JJalving\Autograph\Http\Controllers;

Route::match(['get', 'post'], 'autograph', [Controllers\IndexController::class, 'index'])->name('autograph.index');
