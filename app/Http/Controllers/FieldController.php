<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Research;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index()
    {
        $fields = Field::orderBy('nama')->get();
        return view('fields.index', compact('fields'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:bidang,nama',
        ]);

        Field::create($validated);

        return redirect()->route('fields.index')->with('success', 'Bidang berhasil ditambahkan.');
    }

    public function update(Request $request, Field $field)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:bidang,nama,' . $field->id,
        ]);

        $field->update($validated);

        return redirect()->route('fields.index')->with('success', 'Bidang berhasil diperbarui.');
    }

    public function destroy(Field $field)
    {
        $inUse = Research::where('bidang_id', $field->id)->exists();
        if ($inUse) {
            return redirect()->route('fields.index')->with('error', 'Bidang sedang dipakai oleh penelitian, tidak dapat dihapus.');
        }

        $field->delete();

        return redirect()->route('fields.index')->with('success', 'Bidang berhasil dihapus.');
    }
}
