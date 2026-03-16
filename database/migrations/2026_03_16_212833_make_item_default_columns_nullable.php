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
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('is_inbox')->nullable()->default(false)->change();
            $table->boolean('evening')->nullable()->default(false)->change();
            $table->boolean('is_logged')->nullable()->default(false)->change();
            $table->string('status')->nullable()->default('Open')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('is_inbox')->nullable(false)->default(false)->change();
            $table->boolean('evening')->nullable(false)->default(false)->change();
            $table->boolean('is_logged')->nullable(false)->default(false)->change();
            $table->string('status')->nullable(false)->default('Open')->change();
        });
    }
};
