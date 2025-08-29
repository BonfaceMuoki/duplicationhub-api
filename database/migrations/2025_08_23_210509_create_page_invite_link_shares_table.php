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
        Schema::create('page_invite_link_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
            $table->foreignId('page_invite_id')->constrained('page_invites')->onDelete('cascade');
            $table->string('user_page_link');
            $table->enum('registration_status', ['pending', 'registered', 'completed', 'failed'])->default('pending');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // For storing additional data like UTM parameters, user agent, etc.
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['page_id', 'page_invite_id']);
            $table->index('registration_status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_invite_link_shares');
    }
};
