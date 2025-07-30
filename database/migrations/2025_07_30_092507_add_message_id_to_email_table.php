<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->string('message_id')->nullable()->after('id'); // Спочатку дозволимо NULL

            // Додайте індекс для швидкого пошуку
            $table->index('message_id');
        });

        // Заповнимо існуючі записи тимчасовими значеннями
        DB::statement("UPDATE emails SET message_id = CONCAT('temp-', id) WHERE message_id IS NULL");

        // Тепер зробимо поле обов'язковим
        Schema::table('emails', function (Blueprint $table) {
            $table->string('message_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn('message_id');
        });
    }
};
