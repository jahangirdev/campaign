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
        Schema::create('email_sent_trackings', function (Blueprint $table) {
            $table->id();
            $table->integer('campaign_id');
            $table->string('batch_id');
            $table->integer('total_sent')->unsigned()->default(0);
            $table->integer('invalid')->unsigned()->default(0);
            $table->integer('failed')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_sent_trackings');
    }
};
