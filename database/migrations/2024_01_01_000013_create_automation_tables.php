<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fb_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['birthday', 'anniversary', 'event', 'manual'])->default('manual');
            $table->text('content');
            $table->integer('devotee_count')->default(0);
            $table->enum('status', ['scheduled', 'sent', 'failed'])->default('scheduled');
            $table->string('fb_post_id')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->index(['scheduled_at', 'status']);
        });

        Schema::create('gaushala_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon', 10)->default('🙏');
            $table->enum('type', ['festival', 'daily', 'weekly', 'meeting', 'health', 'upcoming'])->default('upcoming');
            $table->dateTime('scheduled_at');
            $table->string('time_label', 50)->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_rule')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['scheduled_at']);
            $table->index(['type']);
        });

        Schema::create('admin_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_users');
        Schema::dropIfExists('gaushala_events');
        Schema::dropIfExists('fb_posts');
    }
};
