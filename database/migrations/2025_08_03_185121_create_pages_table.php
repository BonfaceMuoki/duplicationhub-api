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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('slug')->unique(); // e.g. "page1"
            $table->string('title');
            $table->string('headline');
            $table->text('summary')->nullable();
            $table->string('video_url')->nullable();
            $table->string('image_url')->nullable();
            $table->string('cta_text')->default('Join Now');
            $table->boolean('is_active')->default(true);
            $table->bigInteger('views')->default(0);

            // Publishing
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('publish_at')->nullable();
            $table->timestamp('unpublish_at')->nullable();
            $table->integer('sort_order')->default(0);

            // SEO & social
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image_url')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('is_indexable')->default(true);

            // CTA behavior
            $table->string('cta_subtext')->nullable();
            $table->string('default_join_url')->nullable();
            $table->enum('capture_mode', ['modal', 'inline'])->default('modal');
            // Redirect configuration
            $table->string('platform_base_url')->nullable();

            // Content flexibility
            // Content body (rich editor HTML/JSON string)
            $table->longText('body')->nullable();

            // Experiments
            $table->string('experiment_group')->nullable();
            $table->string('variant')->nullable();
            $table->unsignedTinyInteger('allocation_weight')->default(100);

            // Compliance
            $table->text('consent_text')->nullable();
            $table->boolean('show_consent')->default(false);

            // Abuse control
            $table->unsignedInteger('rate_limit_per_ip_per_day')->default(100);
            $table->boolean('require_https_join')->default(true);
            $table->json('allowed_join_domains')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
