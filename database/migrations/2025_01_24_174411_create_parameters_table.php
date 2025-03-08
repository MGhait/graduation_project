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
        Schema::create('parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ic_details_id')->constrained('ic_details');
            $table->string('technology_family')->nullable();
            $table->double('min_voltage')->nullable();
            $table->double('max_voltage')->nullable();
            $table->integer('channels_number')->nullable();
            $table->integer('inputs_per_channel')->nullable();
            $table->double('min_temperature')->nullable();
            $table->double('max_temperature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parameters');
    }
};
