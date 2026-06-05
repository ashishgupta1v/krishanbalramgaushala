<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wish_delivery_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('devotee_id');
            $table->enum('wish_type', ['birthday', 'anniversary']);
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['devotee_id']);
            $table->index(['status']);
            $table->foreign('devotee_id')->references('id')->on('devotees')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wish_delivery_logs');
    }
};
