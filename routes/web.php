<?php

use App\Models\File;
use Illuminate\Support\Facades\Route;

Route::get('/storage/{path}', function ($path) {
    return ["success" => true];
})->where('path', '.*');
Route::get('/storage', function () {
    return ["success" => true];
});
