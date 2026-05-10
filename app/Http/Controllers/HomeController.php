<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::take(6)->get();
        return view('welcome', compact('featuredProducts'));
    }
}
