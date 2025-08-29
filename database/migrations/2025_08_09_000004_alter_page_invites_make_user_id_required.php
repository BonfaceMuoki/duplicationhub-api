<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_invites', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['user_id']);
        });

        // Make the column NOT NULL
        DB::statement('ALTER TABLE page_invites MODIFY user_id BIGINT UNSIGNED NOT NULL');

        Schema::table('page_invites', function (Blueprint $table) {
            // Recreate the foreign key constraint without nullOnDelete
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('page_invites', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['user_id']);
        });

        // Make the column nullable again
        DB::statement('ALTER TABLE page_invites MODIFY user_id BIGINT UNSIGNED NULL');

        Schema::table('page_invites', function (Blueprint $table) {
            // Recreate the original foreign key constraint with nullOnDelete
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }
};