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
        Schema::create('cpus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('brand', 100);
            $table->string('name', 100);
            $table->string('architecture', 100);
            $table->integer('transistor_size');
            $table->double('clock_rate');
            $table->integer('cores');
            $table->integer('logical_processor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpus');
    }
};
