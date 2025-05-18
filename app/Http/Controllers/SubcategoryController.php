<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Support\Facades\Log;

class SubcategoryController extends Controller
{
    public function index(int $id)
    {
        Log::info("Obteniendo subcategorías de la categoría ID: $id");
        try {
            $subcategories = Subcategory::where('category_id', $id)->get();
            Log::info("Subcategorías obtenidas correctamente. Total: " . $subcategories->count());
            return $subcategories;
        } catch (\Exception $e) {
            Log::error("Error al obtener subcategorías de la categoría ID: $id. Error: " . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }

    public function getAll()
    {
        Log::info("Obteniendo todas las subcategorías...");
        try {
            $subcategories = Subcategory::all();
            Log::info("Todas las subcategorías obtenidas correctamente. Total: " . $subcategories->count());
            return $subcategories;
        } catch (\Exception $e) {
            Log::error("Error al obtener todas las subcategorías. Error: " . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }
}

