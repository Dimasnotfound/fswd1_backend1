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

Route::middleware('auth:sanctum')->group(function () {
    // Rute untuk Karyawan
    Route::get('karyawans', [KaryawanController::class, 'index']);
    Route::get('karyawans/first-three', [KaryawanController::class, 'firstThreeJoined']);
    Route::post('karyawans', [KaryawanController::class, 'store']);
    Route::get('karyawans/{karyawan}', [KaryawanController::class, 'show']);
    Route::put('karyawans/{karyawan}', [KaryawanController::class, 'update']);
    Route::delete('karyawans/{karyawan}', [KaryawanController::class, 'destroy']);

    // Rute untuk Cuti
    Route::get('karyawan/cuti', [KaryawanController::class, 'indexCuti']);
    Route::get('karyawan/cuti/pernah-cuti', [KaryawanController::class, 'karyawanYangPernahCuti']);
    Route::get('karyawan/cuti/sisa', [KaryawanController::class, 'sisaCuti']);
});

// Rute login
Route::post('/login', [AuthController::class, 'login']);

// Rute register
Route::post('/register', [AuthController::class, 'register']);
