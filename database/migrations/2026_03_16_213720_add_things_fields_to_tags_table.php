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
        Schema::table('tags', function (Blueprint $table) {
            $table->string('things_id')->nullable()->unique()->after('id');
            $table->string('keyboard_shortcut')->nullable()->after('name');
            $table->unsignedBigInteger('parent_tag_id')->nullable()->after('keyboard_shortcut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn(['things_id', 'keyboard_shortcut', 'parent_tag_id']);
        });
    }
};
