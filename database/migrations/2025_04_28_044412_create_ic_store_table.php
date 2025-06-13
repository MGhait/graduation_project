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
//        Schema::create('ic_store', function (Blueprint $table) {
//            $table->id();
//            $table->timestamps();
//        });
        Schema::create('ic_store', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ic_id')->constrained('ics')->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['ic_id', 'store_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ic_store');
    }
};
