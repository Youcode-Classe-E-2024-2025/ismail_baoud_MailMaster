<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\NewsletterController;
use App\Http\Controllers\API\SubscriberController;
use App\Http\Controllers\API\CampaignController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('newsletters', NewsletterController::class);
    Route::apiResource('subscribers', SubscriberController::class);
    Route::apiResource('campaigns', CampaignController::class);
});
