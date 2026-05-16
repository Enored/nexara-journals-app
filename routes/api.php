<?php

use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EditionController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/journals', [JournalController::class, 'index']);
Route::get('/journals/{journal}', [JournalController::class, 'show']);
Route::get('/journals/{journal}/editions', [EditionController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/submissions', [SubmissionController::class, 'index']);
    Route::post('/submissions', [SubmissionController::class, 'store']);
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show']);
    Route::put('/submissions/{submission}', [SubmissionController::class, 'update']);
    Route::post('/submissions/{submission}/files', [SubmissionController::class, 'uploadFile']);
    Route::post('/submissions/{submission}/assign-reviewer', [SubmissionController::class, 'assignReviewer']);
    Route::post('/submissions/{submission}/decision', [SubmissionController::class, 'decision']);

    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::get('/reviews/{assignment}', [ReviewController::class, 'show']);
    Route::post('/reviews/{assignment}/accept', [ReviewController::class, 'accept']);
    Route::post('/reviews/{assignment}/decline', [ReviewController::class, 'decline']);
    Route::post('/reviews/{assignment}/submit', [ReviewController::class, 'submit']);

    Route::post('/journals', [JournalController::class, 'store'])->middleware('platform.admin');
    Route::put('/journals/{journal}', [JournalController::class, 'update'])->middleware('platform.admin');
    Route::post('/journals/{journal}/editions', [EditionController::class, 'store']);

    Route::get('/admin/users', [AdminUserController::class, 'index'])->middleware('platform.admin');
    Route::put('/admin/users/{user}/roles', [AdminUserController::class, 'updateRoles'])->middleware('platform.admin');
});
