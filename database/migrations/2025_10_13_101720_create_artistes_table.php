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
        // $table->string('telephone')->nullable();
        $table->string('pseudo')->nullable();
        // $table->string('adresse')->nullable();
        $table->text('bio')->nullable();
        $table->string('instagram')->nullable();
        $table->string('facebook')->nullable();
        $table->string('banniereUrl')->nullable();
        $table->string('vignetteUrl')->nullable();
        $table->text('css')->nullable();
        $table->timestamp('created_at')->useCurrent();
        $table->timestamp('updated_at')->useCurrent()->usecurrentOnUpdate();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
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
