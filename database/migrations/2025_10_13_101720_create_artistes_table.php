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
        Schema::create('artistes', function (Blueprint $table) {
        $table->id();
        $table->string('profession');
        $table->string('style');
        $table->string('telephone')->unique();
        $table->string('pseudo')->unique();
        $table->string('adresse');
        $table->string('instagram');
        $table->string('facebook');
        $table->string('image');
        $table->timestamps();
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artistes');
    }
};
