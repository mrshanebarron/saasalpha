<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpd_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('category'); // course, seminar, conference, self_study, mentoring, publication
            $table->string('provider')->nullable();
            $table->decimal('hours', 5, 2);
            $table->date('completed_date');
            $table->date('expiry_date')->nullable();
            $table->string('certificate_number')->nullable();
            $table->string('file_path')->nullable();
            $table->text('description')->nullable();
            $table->boolean('verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpd_records');
    }
};
