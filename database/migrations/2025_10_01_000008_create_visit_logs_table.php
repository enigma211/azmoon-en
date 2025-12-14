<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_logs');
    }
};
