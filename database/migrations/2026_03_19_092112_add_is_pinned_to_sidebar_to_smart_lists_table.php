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
        Schema::table('smart_lists', function (Blueprint $table) {
            $table->boolean('is_pinned_to_sidebar')->default(false)->after('kanban_view');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('smart_lists', function (Blueprint $table) {
            $table->dropColumn('is_pinned_to_sidebar');
        });
    }
};
