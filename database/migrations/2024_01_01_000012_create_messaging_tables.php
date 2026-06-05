<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique();
            $table->string('label');
            $table->text('body');
            $table->json('variables')->nullable();
            $table->timestamps();
        });

        Schema::create('broadcasts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('template_id')->nullable();
            $table->text('message_body');
            $table->enum('recipient_mode', ['all', 'active', 'custom'])->default('all');
            $table->integer('total_count')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->enum('status', ['pending', 'sending', 'done', 'failed'])->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('broadcast_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('broadcast_id');
            $table->uuid('devotee_id');
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->string('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->index(['broadcast_id']);
            $table->index(['devotee_id']);
            $table->foreign('broadcast_id')->references('id')->on('broadcasts')->cascadeOnDelete();
            $table->foreign('devotee_id')->references('id')->on('devotees')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_logs');
        Schema::dropIfExists('broadcasts');
        Schema::dropIfExists('message_templates');
    }
};
