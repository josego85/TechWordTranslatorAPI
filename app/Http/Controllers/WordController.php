<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Word;
use App\Http\Requests\WordTranslationRequest;

class WordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wordsWithTranslations = Word::with(['translations' => function ($query) {
            $query->select(['word_id', 'spanish_word', 'german_word']);
        }])->select(['id', 'english_word'])->get();

        return response()->json($wordsWithTranslations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WordTranslationRequest $request)
    {
        DB::beginTransaction();

        try {
            $word = Word::create($request->only('english_word'));
            $word->translations()->create($request->only(
                'spanish_word',
                'german_word'
            ));

            DB::commit();

            return response()->json([
                'message' => 'Word and translations created successfully'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error creating word and translations'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $wordWithTranslations = Word::with(['translations' => function ($query) {
            $query->select(['word_id', 'spanish_word', 'german_word']);
        }])->select(['id', 'english_word'])->find($id);

        if (!$wordWithTranslations) {
            return response()->json([
                'message' => 'Word not found'
            ], 404);
        }

        return response()->json($wordWithTranslations);
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
        DB::beginTransaction();

        try {
            $word = Word::find($id);

            if (!$word) {
                return response()->json([
                    'message' => 'Word not found'
                ], 404);
            }

            $word->translations()->delete();
            $word->delete();

            DB::commit();

            return response()->json([
                'message' => 'Word and translations deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error deleting word and translations'
            ], 500);
        }
    }
}
