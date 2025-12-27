<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\ExitPermitController;
use App\Http\Controllers\Api\BKController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/general/master-data', [GeneralController::class, 'getMasterData']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'getProfile']);
        Route::post('/update', [ProfileController::class, 'updateProfile']);
        Route::post('/update-password', [ProfileController::class, 'updatePassword']);
        Route::post('/update-avatar', [ProfileController::class, 'updateAvatar']);
    });

    // Siswa Routes
    Route::prefix('siswa')->group(function () {
        Route::get('/stats', [SiswaController::class, 'getStats']);
        Route::get('/riwayat', [SiswaController::class, 'getRiwayat']);
        Route::post('/izin', [SiswaController::class, 'requestIzin']);
        
        // Izin Meninggalkan Kelas (Siswa)
        Route::get('/exit-permits', [ExitPermitController::class, 'getMyHistory']);
        Route::post('/exit-permits', [ExitPermitController::class, 'request']);
        
        // BK (Siswa)
        Route::get('/bk/history', [BKController::class, 'getHistory']);
    });

    // Guru Routes
    Route::prefix('guru')->group(function () {
        Route::get('/pending-approvals', [TeacherController::class, 'getPendingApprovals']);
        Route::post('/approve/{id}', [TeacherController::class, 'approveIzin']);
        Route::get('/history', [TeacherController::class, 'getHistory']);
        
        // Izin Meninggalkan Kelas (Guru)
        Route::get('/exit-permits/pending', [ExitPermitController::class, 'getPendingForTeacher']);
        Route::post('/exit-permits/approve/{id}', [ExitPermitController::class, 'approveByTeacher']);
    });

    // BK Chat (Universal)
    Route::prefix('chat')->group(function () {
        Route::get('/rooms', [BKController::class, 'getChatRooms']);
        Route::get('/messages/{roomId}', [BKController::class, 'getMessages']);
        Route::post('/messages/{roomId}', [BKController::class, 'sendMessage']);
    });
});
