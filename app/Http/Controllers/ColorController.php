<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    /**
     * Senarai semua color.
     */
    public function index()
    {
        return response()->json(Color::orderBy('name')->get());
    }

    /**
     * Cipta color baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:colors,name'],
        ]);

        $color = Color::create($validated);

        return response()->json([
            'success' => true,
            'color' => $color,
        ]);
    }
}


