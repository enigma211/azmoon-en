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
            $table->foreignId('exam_domain_id')->nullable()->after('id')->constrained('exam_domains')->onDelete('cascade');
            $table->foreignId('exam_batch_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['exam_domain_id']);
            $table->dropColumn('exam_domain_id');
            $table->foreignId('exam_batch_id')->nullable(false)->change();
        });
    }
};
