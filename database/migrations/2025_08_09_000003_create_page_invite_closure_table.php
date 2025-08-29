<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_invite_closure', function (Blueprint $table) {
            $table->foreignId('ancestor_invite_id')->constrained('page_invites')->cascadeOnDelete();
            $table->foreignId('descendant_invite_id')->constrained('page_invites')->cascadeOnDelete();
            $table->unsignedInteger('depth'); // 0=self, 1=direct, 2=indirect

            $table->primary(['ancestor_invite_id', 'descendant_invite_id']);
            $table->index(['descendant_invite_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_invite_closure');
    }
};