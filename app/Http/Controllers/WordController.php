<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Word;
use App\Http\Requests\WordTranslationRequest;
use App\Services\WordService;

class WordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(WordService $wordService)
    {
        $wordsWithTranslations = $wordService->getAllWordsWithTranslations();

        return response()->json($wordsWithTranslations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, WordService $wordService)
    {
        $requestData = [
            'english_word' => $request->only('english_word'),
            'translations' => $request->input('translations')
        ];

        $wordsWithTranslations = $wordService->createWordWithTranslations(
            $requestData
        );
        if ($wordsWithTranslations) {
            return response()->json([
                'message' => 'Word and translations created successfully'
            ], 201);
        } else {
            return response()->json([
                'message' => 'Error creating word and translations'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, WordService $wordService)
    {
        $wordWithTranslations = $wordService->showWordWithTranslations($id);

        if (!$wordWithTranslations) {
            return response()->json([
                'message' => 'Word not found'
            ], 404);
        }

        return response()->json($wordWithTranslations);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, WordService $wordService)
    {
        $wordWithTranslations = $wordService->destroyWordWithTranslations(
            $id
        );
        if ($wordWithTranslations == null) {
            return response()->json([
                'message' => 'Word not found'
            ], 404);
        } elseif ($wordWithTranslations) {
            return response()->json([
                'message' => 'Word and translations deleted successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'Error deleting word and translations'
            ], 500);
        }
    }
}
