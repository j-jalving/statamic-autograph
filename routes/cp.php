<?php

use Illuminate\Support\Facades\Route;
use JJalving\Autograph\Http\Controllers;

Route::match(['get', 'post'], 'autograph/generate', [Controllers\GenerateController::class, 'index'])->name('autograph.generate.index');
