<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devotees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('whatsapp', 15)->unique(); // e.164 without +
            $table->date('dob')->nullable();
            $table->date('anniversary')->nullable();
            $table->string('city', 100)->default('Ludhiana');
            $table->boolean('fb_consent')->default(false);
            $table->enum('status', ['pending_otp', 'active', 'inactive'])->default('pending_otp');
            $table->string('password')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['dob']);
            $table->index(['anniversary']);
            $table->index(['status']);
            $table->index(['city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devotees');
    }
};
