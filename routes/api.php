<?php

use App\Http\Controllers\Api\DeviceReportController;
use App\Http\Controllers\Api\AiCallController;
use Illuminate\Support\Facades\Route;

Route::post('/device/report', [DeviceReportController::class, 'store']);

// AIコール
Route::get('/ai-call/twiml', [AiCallController::class, 'twiml']);
Route::post('/ai-call/twiml', [AiCallController::class, 'twiml']);
Route::post('/ai-call/status-webhook', [AiCallController::class, 'statusWebhook']);
Route::post('/ai-call/recording-webhook', [AiCallController::class, 'recordingWebhook']);
