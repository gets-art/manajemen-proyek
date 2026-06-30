<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::get('/private-image/{path}', function (string $path) {
    if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
        abort(404);
    }
    return response()->file(\Illuminate\Support\Facades\Storage::disk('local')->path($path));
})->where('path', '.*')->name('private.image');
