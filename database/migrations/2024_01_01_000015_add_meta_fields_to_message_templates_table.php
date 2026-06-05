<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('message_templates', function (Blueprint $table) {
            $table->string('meta_name')->nullable()->unique();
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('approved');
            $table->string('category')->nullable();
            $table->string('is_active_for')->nullable(); // 'birthday', 'anniversary'
        });
    }

    public function down(): void
    {
        Schema::table('message_templates', function (Blueprint $table) {
            $table->dropColumn(['meta_name', 'status', 'category', 'is_active_for']);
        });
    }
};
