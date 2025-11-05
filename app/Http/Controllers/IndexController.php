<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Artiste;


class IndexController extends Controller
{
    function index() {
        $artistes = Artiste::all();
        return view('index', compact('artistes'));
    }
}
