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
        'tags' => 'array' // Accept tag names
    ]);

    // Create the translation
    $translation = Translation::create([
        'key' => $data['key'],
        'value' => $data['value'],
    ]);

    // Attach tags by name, creating new ones if they don't exist
    if (isset($data['tags'])) {
        $tagIds = collect($data['tags'])->map(function ($tagName) {
            return Tag::firstOrCreate(['name' => $tagName])->id;
        });
        $translation->tags()->sync($tagIds);
    }

    // Return translation with tags loaded
    return response()->json($translation->load('tags'), 201);
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
        'tags' => 'array' // Accept tag names
    ]);

    // Update translation key/value
    $translation->update([
        'key' => $data['key'] ?? $translation->key,
        'value' => $data['value'] ?? $translation->value,
    ]);

    // Attach tags by name, creating new ones if needed
    if (isset($data['tags'])) {
        $tagIds = collect($data['tags'])->map(function ($tagName) {
            return Tag::firstOrCreate(['name' => $tagName])->id;
        });
        $translation->tags()->sync($tagIds);
    }

    // Return translation with tags loaded
    return response()->json($translation->load('tags'));
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
            $formatted = $translations->map(function ($t) {
                return [
                    'id'    => $t->id,
                    'key'   => $t->key,
                    'value' => $t->value, // JSON of locales
                    'tags'  => $t->tags->pluck('name'), // just tag names
                ];
            });
            echo $formatted->toJson(JSON_UNESCAPED_UNICODE) . "\n";
        });
    }, 'translations.json');
}
}