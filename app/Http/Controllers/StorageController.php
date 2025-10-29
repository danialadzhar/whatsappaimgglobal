<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    /**
     * Senarai semua storage.
     */
    public function index()
    {
        return response()->json(Storage::orderBy('name')->get());
    }

    /**
     * Cipta storage baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:storages,name'],
        ]);

        $storage = Storage::create($validated);

        return response()->json([
            'success' => true,
            'storage' => $storage,
        ]);
    }
}


