<?php

use App\Http\Controllers\Api\ApplicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('tokens/create', function (Request $request) {
    return response()->json([
        'token' => $request->user()->createToken('api-token')->plainTextToken,
    ]);
})->middleware('auth.basic');

Route::middleware('auth:sanctum')->get('applications/{plan?}', [ApplicationController::class, 'index']);
