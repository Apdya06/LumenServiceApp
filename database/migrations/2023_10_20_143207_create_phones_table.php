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
        Schema::create('phones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('brand', 100);
            $table->string('name', 100);
            $table->integer('ram');
            $table->integer('storage');
            $table->double('camera', 100);
            $table->string('dimensions', 100);
            $table->double('weight');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phones');
    }
};
