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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('template');
            $table->text('lists');
            $table->dateTime('schedule')->default(now());
            $table->dateTime('last_run')->nullable();
            $table->string('run_at');
            $table->integer('total_runs')->default(0);
            $table->string('batch_id')->nullable();
            $table->string('subject');
            $table->string('from_name');
            $table->string('from_email');
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
