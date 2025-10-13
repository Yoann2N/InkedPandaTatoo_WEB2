<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artiste extends Model
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
