<?php

use App\Http\Controllers\{DashboardController, ProjectController, EnquiryController, QuoteController, ComplianceController, CpdController, TimeTrackingController, SubcontractorController, AuditController, AuthController, NotificationController, DocumentTemplateController};
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Enquiries
    Route::get('/enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/enquiries/create', [EnquiryController::class, 'create'])->name('enquiries.create');
        Route::post('/enquiries', [EnquiryController::class, 'store'])->name('enquiries.store');
        Route::get('/enquiries/{enquiry}/edit', [EnquiryController::class, 'edit'])->name('enquiries.edit');
        Route::put('/enquiries/{enquiry}', [EnquiryController::class, 'update'])->name('enquiries.update');
        Route::delete('/enquiries/{enquiry}', [EnquiryController::class, 'destroy'])->name('enquiries.destroy');
    });
    Route::get('/enquiries/{enquiry}', [EnquiryController::class, 'show'])->name('enquiries.show');

    // Quotes
    Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/quotes/create', [QuoteController::class, 'create'])->name('quotes.create');
        Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');
        Route::get('/quotes/{quote}/edit', [QuoteController::class, 'edit'])->name('quotes.edit');
        Route::put('/quotes/{quote}', [QuoteController::class, 'update'])->name('quotes.update');
        Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy'])->name('quotes.destroy');
        Route::post('/quotes/{quote}/convert', [QuoteController::class, 'convertToProject'])->name('quotes.convert');
        Route::post('/quotes/{quote}/send', [QuoteController::class, 'markSent'])->name('quotes.send');
    });
    Route::get('/quotes/{quote}', [QuoteController::class, 'show'])->name('quotes.show');
    Route::get('/quotes/{quote}/pdf', [QuoteController::class, 'pdf'])->name('quotes.pdf');

    // Projects
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        Route::post('/projects/{project}/deliverables', [ProjectController::class, 'storeDeliverable'])->name('projects.deliverables.store');
        Route::patch('/projects/{project}/deliverables/{deliverable}', [ProjectController::class, 'updateDeliverable'])->name('projects.deliverables.update');
        Route::post('/projects/{project}/members', [ProjectController::class, 'storeMember'])->name('projects.members.store');
        Route::delete('/projects/{project}/members/{projectMember}', [ProjectController::class, 'destroyMember'])->name('projects.members.destroy');
    });
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    // Time Tracking — all can view/create/edit own entries, managers+ can approve
    Route::get('/time-tracking', [TimeTrackingController::class, 'index'])->name('time-tracking.index');
    Route::get('/time-tracking/create', [TimeTrackingController::class, 'create'])->name('time-tracking.create');
    Route::post('/time-tracking', [TimeTrackingController::class, 'store'])->name('time-tracking.store');
    Route::get('/time-tracking/{timeEntry}/edit', [TimeTrackingController::class, 'edit'])->name('time-tracking.edit');
    Route::patch('/time-tracking/{timeEntry}', [TimeTrackingController::class, 'update'])->name('time-tracking.update');
    Route::delete('/time-tracking/{timeEntry}', [TimeTrackingController::class, 'destroy'])->name('time-tracking.destroy');
    Route::middleware('role:admin,manager')->group(function () {
        Route::post('/time-tracking/{timeEntry}/approve', [TimeTrackingController::class, 'approve'])->name('time-tracking.approve');
        Route::post('/time-tracking/bulk-approve', [TimeTrackingController::class, 'bulkApprove'])->name('time-tracking.bulk-approve');
    });

    // Compliance
    Route::get('/compliance', [ComplianceController::class, 'index'])->name('compliance.index');
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/compliance/create', [ComplianceController::class, 'create'])->name('compliance.create');
        Route::post('/compliance', [ComplianceController::class, 'store'])->name('compliance.store');
        Route::get('/compliance/{complianceDocument}/edit', [ComplianceController::class, 'edit'])->name('compliance.edit');
        Route::patch('/compliance/{complianceDocument}', [ComplianceController::class, 'update'])->name('compliance.update');
        Route::delete('/compliance/{complianceDocument}', [ComplianceController::class, 'destroy'])->name('compliance.destroy');
    });
    Route::get('/compliance/{complianceDocument}', [ComplianceController::class, 'show'])->name('compliance.show');

    // CPD — all can manage their own records, managers+ can verify
    Route::get('/cpd', [CpdController::class, 'index'])->name('cpd.index');
    Route::get('/cpd/create', [CpdController::class, 'create'])->name('cpd.create');
    Route::post('/cpd', [CpdController::class, 'store'])->name('cpd.store');
    Route::get('/cpd/{cpdRecord}/edit', [CpdController::class, 'edit'])->name('cpd.edit');
    Route::patch('/cpd/{cpdRecord}', [CpdController::class, 'update'])->name('cpd.update');
    Route::delete('/cpd/{cpdRecord}', [CpdController::class, 'destroy'])->name('cpd.destroy');
    Route::post('/cpd/{cpdRecord}/verify', [CpdController::class, 'verify'])->name('cpd.verify')->middleware('role:admin,manager');

    // Subcontractors
    Route::get('/subcontractors', [SubcontractorController::class, 'index'])->name('subcontractors.index');
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/subcontractors/create', [SubcontractorController::class, 'create'])->name('subcontractors.create');
        Route::post('/subcontractors', [SubcontractorController::class, 'store'])->name('subcontractors.store');
        Route::get('/subcontractors/{subcontractor}/edit', [SubcontractorController::class, 'edit'])->name('subcontractors.edit');
        Route::put('/subcontractors/{subcontractor}', [SubcontractorController::class, 'update'])->name('subcontractors.update');
        Route::delete('/subcontractors/{subcontractor}', [SubcontractorController::class, 'destroy'])->name('subcontractors.destroy');
    });
    Route::get('/subcontractors/{subcontractor}', [SubcontractorController::class, 'show'])->name('subcontractors.show');

    // Document Templates
    Route::get('/documents', [DocumentTemplateController::class, 'index'])->name('documents.index');
    Route::get('/documents/generated/{generatedDocument}/preview', [DocumentTemplateController::class, 'preview'])->name('documents.preview');
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/documents/create', [DocumentTemplateController::class, 'create'])->name('documents.create');
        Route::post('/documents', [DocumentTemplateController::class, 'store'])->name('documents.store');
        Route::get('/documents/{documentTemplate}/edit', [DocumentTemplateController::class, 'edit'])->name('documents.edit');
        Route::put('/documents/{documentTemplate}', [DocumentTemplateController::class, 'update'])->name('documents.update');
        Route::delete('/documents/{documentTemplate}', [DocumentTemplateController::class, 'destroy'])->name('documents.destroy');
    });
    Route::get('/documents/{documentTemplate}', [DocumentTemplateController::class, 'show'])->name('documents.show');
    Route::post('/documents/{documentTemplate}/generate', [DocumentTemplateController::class, 'generate'])->name('documents.generate');

    // Notifications
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // AI Suggestions API
    Route::get('/api/suggestions/clients', [DashboardController::class, 'suggestClients'])->name('api.suggestions.clients');
    Route::get('/api/suggestions/scope', [DashboardController::class, 'suggestScope'])->name('api.suggestions.scope');

    // Audit — admin only
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index')->middleware('role:admin');
});
