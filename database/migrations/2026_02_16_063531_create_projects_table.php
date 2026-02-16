<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->unique();
            $table->string('name');
            $table->string('client_name');
            $table->string('client_company')->nullable();
            $table->string('status')->default('active');
            $table->string('project_type')->nullable();
            $table->text('description')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->decimal('spent', 12, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('target_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->integer('progress')->default(0);
            $table->foreignId('project_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('member');
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_members');
        Schema::dropIfExists('projects');
    }
};
