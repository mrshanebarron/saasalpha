<?php

use App\Http\Controllers\{DashboardController, ProjectController, EnquiryController, QuoteController, ComplianceController, CpdController, TimeTrackingController, SubcontractorController, AuditController, AuthController, NotificationController};
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Enquiries
    Route::resource('enquiries', EnquiryController::class);

    // Quotes
    Route::resource('quotes', QuoteController::class);
    Route::post('/quotes/{quote}/convert', [QuoteController::class, 'convertToProject'])->name('quotes.convert');
    Route::post('/quotes/{quote}/send', [QuoteController::class, 'markSent'])->name('quotes.send');

    // Projects
    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/deliverables', [ProjectController::class, 'storeDeliverable'])->name('projects.deliverables.store');
    Route::patch('/projects/{project}/deliverables/{deliverable}', [ProjectController::class, 'updateDeliverable'])->name('projects.deliverables.update');
    Route::post('/projects/{project}/members', [ProjectController::class, 'storeMember'])->name('projects.members.store');
    Route::delete('/projects/{project}/members/{projectMember}', [ProjectController::class, 'destroyMember'])->name('projects.members.destroy');

    // Time Tracking
    Route::get('/time-tracking', [TimeTrackingController::class, 'index'])->name('time-tracking.index');
    Route::get('/time-tracking/create', [TimeTrackingController::class, 'create'])->name('time-tracking.create');
    Route::post('/time-tracking', [TimeTrackingController::class, 'store'])->name('time-tracking.store');
    Route::get('/time-tracking/{timeEntry}/edit', [TimeTrackingController::class, 'edit'])->name('time-tracking.edit');
    Route::patch('/time-tracking/{timeEntry}', [TimeTrackingController::class, 'update'])->name('time-tracking.update');
    Route::delete('/time-tracking/{timeEntry}', [TimeTrackingController::class, 'destroy'])->name('time-tracking.destroy');
    Route::post('/time-tracking/{timeEntry}/approve', [TimeTrackingController::class, 'approve'])->name('time-tracking.approve');
    Route::post('/time-tracking/bulk-approve', [TimeTrackingController::class, 'bulkApprove'])->name('time-tracking.bulk-approve');

    // Compliance
    Route::get('/compliance', [ComplianceController::class, 'index'])->name('compliance.index');
    Route::get('/compliance/create', [ComplianceController::class, 'create'])->name('compliance.create');
    Route::post('/compliance', [ComplianceController::class, 'store'])->name('compliance.store');
    Route::get('/compliance/{complianceDocument}', [ComplianceController::class, 'show'])->name('compliance.show');
    Route::get('/compliance/{complianceDocument}/edit', [ComplianceController::class, 'edit'])->name('compliance.edit');
    Route::patch('/compliance/{complianceDocument}', [ComplianceController::class, 'update'])->name('compliance.update');
    Route::delete('/compliance/{complianceDocument}', [ComplianceController::class, 'destroy'])->name('compliance.destroy');

    // CPD
    Route::get('/cpd', [CpdController::class, 'index'])->name('cpd.index');
    Route::get('/cpd/create', [CpdController::class, 'create'])->name('cpd.create');
    Route::post('/cpd', [CpdController::class, 'store'])->name('cpd.store');
    Route::get('/cpd/{cpdRecord}/edit', [CpdController::class, 'edit'])->name('cpd.edit');
    Route::patch('/cpd/{cpdRecord}', [CpdController::class, 'update'])->name('cpd.update');
    Route::delete('/cpd/{cpdRecord}', [CpdController::class, 'destroy'])->name('cpd.destroy');
    Route::post('/cpd/{cpdRecord}/verify', [CpdController::class, 'verify'])->name('cpd.verify');

    // Subcontractors
    Route::resource('subcontractors', SubcontractorController::class);

    // Notifications
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Quote PDF
    Route::get('/quotes/{quote}/pdf', [QuoteController::class, 'pdf'])->name('quotes.pdf');

    // Audit
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
});
