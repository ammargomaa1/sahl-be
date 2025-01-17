<?php

namespace App\Http\Controllers\Users\Categories;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request){
        if ($request->get_all) {
            return CategoryResource::collection(
                Category::with('children')->parents()->ordered()->get()
            );
        }
        return CategoryResource::collection(
            Category::with('children')->where('is_main_page_menu' , true)->parents()->ordered()->get()
        );
    }
}
