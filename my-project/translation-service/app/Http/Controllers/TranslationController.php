<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    /**
     * Display a listing of translations with optional filters.
     */
    public function index(Request $request)
    {
        $query = Translation::with('tags');

        // Filter by key
        if ($request->has('key')) {
            $query->where('key', 'like', "%{$request->key}%");
        }

        // Filter by content in any locale
        if ($request->has('content')) {
            $query->where(function($q) use ($request) {
                foreach (['en','fr','es','de'] as $locale) {
                    $q->orWhereJsonContains("value->{$locale}", $request->content);
                }
            });
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->whereHas('tags', fn($q) => $q->where('name', $request->tag));
        }

        // Paginate for performance
        return response()->json($query->paginate(50));
    }

    /**
     * Store a newly created translation.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string|unique:translations,key',
            'value' => 'required|array',
            'tags' => 'array'
        ]);

        $translation = Translation::create($data);

        if (isset($data['tags'])) {
            $translation->tags()->sync($data['tags']);
        }

        return response()->json($translation, 201);
    }

    /**
     * Display the specified translation.
     */
    public function show(string $id)
    {
        $translation = Translation::with('tags')->findOrFail($id);
        return response()->json($translation);
    }

    /**
     * Update the specified translation.
     */
    public function update(Request $request, string $id)
    {
        $translation = Translation::findOrFail($id);

        $data = $request->validate([
            'key' => 'string|unique:translations,key,' . $translation->id,
            'value' => 'array',
            'tags' => 'array'
        ]);

        $translation->update($data);

        if (isset($data['tags'])) {
            $translation->tags()->sync($data['tags']);
        }

        return response()->json($translation);
    }

    /**
     * Remove the specified translation.
     */
    public function destroy(string $id)
    {
        $translation = Translation::findOrFail($id);
        $translation->delete();
        return response()->json(['message' => 'Translation deleted']);
    }

    /**
     * Export all translations as JSON (optimized for large datasets).
     */
    public function export()
    {
        return response()->streamDownload(function () {
            Translation::with('tags')->chunk(5000, function ($translations) {
                echo $translations->toJson(JSON_UNESCAPED_UNICODE);
            });
        }, 'translations.json');
    }
}