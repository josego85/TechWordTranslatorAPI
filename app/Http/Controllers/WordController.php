<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Word;
use App\Http\Requests\WordTranslationRequest;

class WordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wordsWithTranslations = Word::with('translations')->get();

        return response()->json($wordsWithTranslations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WordTranslationRequest $request)
    {
        $word = Word::create($request->only('english_word'));
        $word->translations()->create($request->only(
            'spanish_word',
            'german_word'
        ));

        return response()->json([
            'message' => 'Word and translations created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
