<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 1)
            ->latest()
            ->take(6)
            ->get();

        $featuredProducts = Product::with(['category', 'images'])
            ->where('status', 1)
            ->where('is_featured', 1)
            ->latest()
            ->take(8)
            ->get();

        $latestProducts = Product::with(['category', 'images'])
            ->where('status', 1)
            ->latest()
            ->take(4)
            ->get();

        return view('frontend.home', compact(
            'categories',
            'featuredProducts',
            'latestProducts'
        ));
    }
}
