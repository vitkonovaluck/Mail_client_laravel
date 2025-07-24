<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('virtual_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained('virtual_domains')->onDelete('cascade');
            $table->string('email')->unique(); // user@example.com
            $table->string('password'); // {SHA512-CRYPT} або інший хеш
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_users');
    }
};
