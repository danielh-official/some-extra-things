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
        Schema::create('items', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('type');
            $table->string('title');

            $table->string('parent')->nullable();
            $table->string('parent_id')->nullable();
            $table->string('heading_id')->nullable();

            $table->boolean('is_inbox')->default(false);

            $table->string('start')->nullable();
            $table->date('start_date')->nullable();
            $table->boolean('evening')->default(false);
            $table->dateTime('reminder_date')->nullable();
            $table->date('deadline')->nullable();

            $table->json('tags')->nullable();
            $table->json('all_matching_tags')->nullable();

            $table->string('status')->default('Open');
            $table->dateTime('completion_date')->nullable();
            $table->boolean('is_logged')->default(false);

            $table->text('notes')->nullable();
            $table->json('checklist')->nullable();

            $table->dateTime('creation_date')->nullable();
            $table->dateTime('modification_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
