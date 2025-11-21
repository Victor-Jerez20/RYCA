<?php

use App\Http\Controllers\Api\ActaApiController;

Route::post('/actas/preview', [ActaApiController::class, 'preview']);
Route::get('/health', [ActaApiController::class, 'health']);
