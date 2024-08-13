<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function getList(){
        $data = Category::all();
        return response()->json($data)
            ->header('Content-Type', 'application/json', 'charser=utf-8');
    }
}
