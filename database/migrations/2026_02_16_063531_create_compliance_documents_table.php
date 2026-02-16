<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliance_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('type'); // license, insurance, certification, safety, permit
            $table->string('status')->default('valid'); // valid, expiring_soon, expired, pending
            $table->string('document_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('reminder_days')->default(30);
            $table->string('issuing_body')->nullable();
            $table->string('file_path')->nullable();
            $table->foreignId('holder_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('subcontractor_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('is_critical')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_documents');
    }
};
