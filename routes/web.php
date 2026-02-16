<?php

use App\Http\Controllers\{DashboardController, ProjectController, EnquiryController, QuoteController, ComplianceController, CpdController, TimeTrackingController, SubcontractorController, AuditController, AuthController};
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('projects', ProjectController::class)->only(['index', 'show']);
    Route::resource('enquiries', EnquiryController::class)->only(['index', 'show']);
    Route::resource('quotes', QuoteController::class)->only(['index', 'show']);
    Route::get('/compliance', [ComplianceController::class, 'index'])->name('compliance.index');
    Route::get('/compliance/{complianceDocument}', [ComplianceController::class, 'show'])->name('compliance.show');
    Route::get('/cpd', [CpdController::class, 'index'])->name('cpd.index');
    Route::get('/time-tracking', [TimeTrackingController::class, 'index'])->name('time-tracking.index');
    Route::get('/subcontractors', [SubcontractorController::class, 'index'])->name('subcontractors.index');
    Route::get('/subcontractors/{subcontractor}', [SubcontractorController::class, 'show'])->name('subcontractors.show');
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
});
