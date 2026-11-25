<?php

use App\Http\Controllers\DocController;
use App\Http\Controllers\PhotoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/photos');

Route::resource('photos', PhotoController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('docs', DocController::class)->only(['index', 'store', 'update', 'destroy']);
