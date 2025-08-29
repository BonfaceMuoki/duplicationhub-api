<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->foreignId('referrer_invite_id')->constrained('page_invites')->cascadeOnDelete();
            $table->foreignId('submitter_invite_id')->nullable()->constrained('page_invites')->nullOnDelete();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('whatsapp_number')->nullable();

            // tracking
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->enum('status', ['new', 'contacted', 'joined', 'joining_link_shared', 'advertisement_link_shared'])->default('new');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['page_id']);
            $table->index(['referrer_invite_id']);
            $table->index(['submitter_invite_id']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};