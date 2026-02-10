<?php

use App\Http\Controllers\Api\DeviceReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| デバイスからのデータ受信API
| 認証不要（device_id + ICCID で検証）
|
*/

Route::post('/device/report', [DeviceReportController::class, 'store']);
