<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::post('/upload', [UploadController::class, 'store']);
Route::get('/upload/export', [UploadController::class, 'export']);

Route::post('/upload', [UploadController::class, 'upload']);
Route::options('/upload', [UploadController::class, 'upload']);
Route::delete('/upload/delete', [UploadController::class, 'delete']);
Route::post('/upload/revert', [UploadController::class, 'revert']);
