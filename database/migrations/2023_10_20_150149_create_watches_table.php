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
        Schema::create('watches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('brand', 100);
            $table->string('name', 100);
            $table->integer('production_year');
            $table->string('material', 100);
            $table->double('weight');
            $table->string('dimensions', 100);
            $table->enum('waterproof', ['yes', 'no'])->default('no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watches');
    }
};
