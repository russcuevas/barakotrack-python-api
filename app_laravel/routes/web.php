<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\FoundItemController as StudentFoundItemController;
use App\Http\Controllers\Student\LostReportController as StudentLostReportController;
use App\Http\Controllers\Student\ClaimController as StudentClaimController;
use App\Http\Controllers\Student\MatcherController as StudentMatcherController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ClaimVerificationController as AdminClaimVerificationController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\LostReportController as AdminLostReportController;

use App\Http\Controllers\ChatbotController;

// Home Root Redirect
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('student.dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Unified Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [LoginController::class, 'register'])->name('register');
Route::get('/verify-account/{id}', [LoginController::class, 'verifyAccount'])->name('verify.account');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Student Portal Routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/found-items', [StudentFoundItemController::class, 'index'])->name('found-items');
    Route::get('/lost-reports', [StudentLostReportController::class, 'index'])->name('lost-reports');
    Route::post('/lost-reports', [StudentLostReportController::class, 'store'])->name('lost-reports.store');
    Route::get('/lost-reports/{id}/cnn-scan', [StudentLostReportController::class, 'scanCnn'])->name('lost-reports.cnn-scan');
    Route::post('/lost-reports/{id}/resolve', [StudentLostReportController::class, 'resolve'])->name('lost-reports.resolve');
    Route::get('/matcher', [StudentMatcherController::class, 'index'])->name('matcher');
    Route::get('/claims', [StudentClaimController::class, 'index'])->name('claims');
    Route::post('/claims', [StudentClaimController::class, 'store'])->name('claims.store');
});

// SAO Admin Portal Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/claims', [AdminClaimVerificationController::class, 'index'])->name('claims');
    Route::post('/claims/{id}/approve', [AdminClaimVerificationController::class, 'approve'])->name('claims.approve');
    Route::post('/claims/{id}/reject', [AdminClaimVerificationController::class, 'reject'])->name('claims.reject');
    Route::post('/claims/{id}/mark-claimed', [AdminClaimVerificationController::class, 'markClaimed'])->name('claims.mark-claimed');
    Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory');
    Route::post('/inventory', [AdminInventoryController::class, 'store'])->name('inventory.store');
    Route::put('/inventory/{id}', [AdminInventoryController::class, 'update'])->name('inventory.update');
    Route::get('/lost-reports', [AdminLostReportController::class, 'index'])->name('lost-reports');
});

// Chatbot API Endpoint
Route::post('/api/chatbot', [ChatbotController::class, 'query'])->name('chatbot.query');
