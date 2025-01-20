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
        Schema::table('ics', function (Blueprint $table) {
            $table->unsignedBigInteger('datasheet_file_id')->nullable()->after('blog_diagram');
            $table->foreign('datasheet_file_id')->references('id')->on('files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ics', function (Blueprint $table) {
            //
        });
    }
};
