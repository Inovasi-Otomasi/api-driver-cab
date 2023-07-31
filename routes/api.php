<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\ShiftController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('1.0.0/drivers', [DriverController::class, 'index']);
Route::get('1.0.0/drivers/{driver}', [DriverController::class, 'show']);
Route::post('1.0.0/drivers', [DriverController::class, 'store']);
Route::put('1.0.0/drivers/{driver}', [DriverController::class, 'update']);
Route::delete('1.0.0/drivers/{driver}', [DriverController::class, 'destroy']);

Route::get('1.0.0/shifts', [ShiftController::class, 'index']);
Route::get('1.0.0/shifts/{shift}', [ShiftController::class, 'show']);
Route::post('1.0.0/shifts', [ShiftController::class, 'store']);
Route::put('1.0.0/shifts/{shift}', [ShiftController::class, 'update']);
Route::delete('1.0.0/shifts/{shift}', [ShiftController::class, 'destroy']);

Route::get('1.0.0/routes', [RouteController::class, 'index']);
Route::get('1.0.0/routes/{route}', [RouteController::class, 'show']);
Route::post('1.0.0/routes', [RouteController::class, 'store']);
Route::put('1.0.0/routes/{route}', [RouteController::class, 'update']);
Route::delete('1.0.0/routes/{route}', [RouteController::class, 'destroy']);


Route::post('1.0.0/drivers_datatables', [DriverController::class, 'driverList']);
Route::post('1.0.0/shifts_datatables', [ShiftController::class, 'shiftList']);
Route::post('1.0.0/routes_datatables', [RouteController::class, 'routeList']);
