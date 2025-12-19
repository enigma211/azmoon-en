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
        Schema::table('exams', function (Blueprint $table) {
            $table->string('thumbnail')->nullable()->after('description');
            $table->string('badge_text')->nullable()->after('thumbnail');
            $table->string('badge_color')->nullable()->after('badge_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['thumbnail', 'badge_text', 'badge_color']);
        });
    }
};
