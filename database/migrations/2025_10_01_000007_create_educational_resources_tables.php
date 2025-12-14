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
        // جدول نوع آزمون (نظام مهندسی، کنکور ارشد، ...)
        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // نظام مهندسی
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // آیکون
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // جدول دسته‌بندی منابع (ویدیو آموزشی، جزوات آموزشی)
        Schema::create('resource_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_type_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['video', 'document']); // video = ویدیو، document = جزوه
            $table->string('title'); // ویدیوهای آموزشی، جزوات آموزشی
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['exam_type_id', 'type']);
        });

        // جدول پست‌های آموزشی
        Schema::create('educational_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_category_id')->constrained()->onDelete('cascade');
            $table->string('title'); // عنوان پست
            $table->string('slug')->unique();
            $table->text('description')->nullable(); // توضیحات کوتاه
            $table->longText('content')->nullable(); // محتوای کامل
            
            // برای ویدیو
            $table->text('video_embed_code')->nullable(); // کد embed آپارات
            
            // برای جزوه
            $table->string('pdf_file')->nullable(); // مسیر فایل PDF
            $table->integer('file_size')->nullable(); // حجم فایل (کیلوبایت)
            
            $table->string('thumbnail')->nullable(); // تصویر شاخص
            $table->integer('view_count')->default(0); // تعداد بازدید
            $table->integer('download_count')->default(0); // تعداد دانلود (برای PDF)
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false); // پست ویژه
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index('published_at');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educational_posts');
        Schema::dropIfExists('resource_categories');
        Schema::dropIfExists('exam_types');
    }
};
