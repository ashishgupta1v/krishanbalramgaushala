<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('whatsapp', 15);
            $table->string('code', 64); // hashed
            $table->boolean('verified')->default(false);
            $table->timestamp('expires_at');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['whatsapp', 'verified']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_requests');
    }
};
