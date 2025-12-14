<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Exam Domains
        Schema::create('exam_domains', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            
            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            
            $table->timestamps();
        });

        // 2. Exam Batches
        Schema::create('exam_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_domain_id')->constrained('exam_domains')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_free')->default(false);
            $table->integer('sort_order')->default(0);
            
            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            
            $table->timestamps();
        });

        // 3. Exams
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_batch_id')->constrained('exam_batches')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(0); // 0 = unlimited
            $table->integer('pass_threshold')->default(50); // percentage
            $table->boolean('is_published')->default(false);
            $table->integer('total_score')->default(100);
            $table->float('negative_score_ratio')->default(0); // e.g. 0.33 for 1/3
            $table->integer('sort_order')->default(0);
            
            // Assumptions (from additional migration)
            $table->text('assumptions_text')->nullable();
            $table->string('assumptions_image')->nullable();
            
            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            
            $table->timestamps();
        });

        // 4. Exam Sections (Optional grouping of questions)
        Schema::create('exam_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->string('title');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 5. Questions
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            // Can belong to a section if we implemented that fully, but mainly exam_id is used.
            
            $table->string('type')->default('multiple_choice'); // multiple_choice, true_false, etc.
            $table->longText('text')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_path_2')->nullable(); // Second image support
            
            $table->integer('order_column')->default(0);
            $table->string('difficulty')->default('medium'); // easy, medium, hard
            
            $table->integer('score')->default(1);
            $table->float('negative_score')->default(0);
            
            $table->longText('explanation')->nullable();
            $table->string('explanation_image_path')->nullable();
            
            $table->boolean('is_deleted')->default(false); // Soft delete flag (custom)
            
            $table->timestamps();
            
            // Fulltext index for search
            if (DB::getDriverName() !== 'sqlite') {
                $table->fullText('text');
            }
        });

        // 6. Choices
        Schema::create('choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->longText('text')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 7. Question Assets (Additional images/files)
        Schema::create('question_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->string('type')->default('image');
            $table->string('path');
            $table->string('caption')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 8. Exam Attempts
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            
            $table->float('score')->nullable();
            $table->boolean('passed')->default(false);
            $table->string('status')->default('in_progress'); // in_progress, completed, abandoned
            
            $table->timestamps();
            
            // Note: We removed the unique constraint on [exam_id, user_id] to allow multiple attempts
        });

        // 9. Attempt Answers
        Schema::create('attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('choice_id')->nullable()->constrained('choices')->onDelete('cascade');
            
            $table->boolean('selected')->default(true); // Usually true if this record exists, but good to have
            
            $table->timestamps();
        });

        // 10. Question Reports
        Schema::create('question_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            
            $table->text('report');
            $table->string('status')->default('pending'); // pending, reviewed, dismissed
            $table->text('admin_notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_reports');
        Schema::dropIfExists('attempt_answers');
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('question_assets');
        Schema::dropIfExists('choices');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('exam_sections');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('exam_batches');
        Schema::dropIfExists('exam_domains');
    }
};
