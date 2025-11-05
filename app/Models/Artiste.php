<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Artiste extends Model
{
    use HasFactory;
    //Pour l'injection en masse 
    protected $fillable = [
        'user_id',
        'profession',
        'style',
        // 'telephone',
        'pseudo',
        'bio',
        // 'adresse',
        'instagram',
        'facebook',
        'banniereUrl',
        'vignetteUrl',
        'css',
    ];
}
