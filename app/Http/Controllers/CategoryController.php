<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        Log::info('Obteniendo todas las categorías...');

        try {
            $categories = Category::all();
            Log::info('Categorías obtenidas correctamente. Total: ' . $categories->count());
            return $categories;
        } catch (\Exception $e) {
            Log::error('Error al obtener categorías. Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }
}

