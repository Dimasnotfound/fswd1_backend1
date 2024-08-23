<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\KaryawanController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('karyawans', [KaryawanController::class, 'index']);
    Route::get('karyawans/first-three', [KaryawanController::class, 'firstThreeJoined']);
    Route::post('karyawans', [KaryawanController::class, 'store']);
    Route::get('karyawans/{karyawan}', [KaryawanController::class, 'show']);
    Route::put('karyawans/{karyawan}', [KaryawanController::class, 'update']);
    Route::delete('karyawans/{karyawan}', [KaryawanController::class, 'destroy']);
});

// Rute login
Route::post('/login', [AuthController::class, 'login']);

// regis
Route::post('/register', [AuthController::class, 'register']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
