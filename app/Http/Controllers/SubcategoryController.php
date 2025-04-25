<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function index(int $id)
    {
        return Subcategory::where('category_id', $id)->get();
    }
}
