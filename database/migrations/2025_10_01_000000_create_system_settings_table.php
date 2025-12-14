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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable()->index();
            $table->text('value')->nullable();
            
            // Branding
            $table->string('site_name')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('hero_title')->nullable();
            $table->text('hero_description')->nullable();
            
            // SEO
            $table->string('seo_title')->nullable();
            $table->text('site_description')->nullable();
            $table->text('seo_keywords')->nullable();
            
            // Pages Content
            $table->longText('terms_content')->nullable();
            $table->longText('about_content')->nullable();
            
            // System Config (Legacy/Other)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
