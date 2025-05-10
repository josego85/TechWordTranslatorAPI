<?php

namespace App\Http\Controllers;

use App\Http\Requests\WordIndexRequest;
use App\Http\Resources\WordCollection;
use Illuminate\Http\Request;
use App\Services\WordService;

class WordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(WordIndexRequest $request, WordService $wordService)
    {
        $paginator = $wordService->getAllWordsWithTranslations(
            perPage: $request->getPerPage(),
            cursor:  $request->getCursor(),
        );

        return new WordCollection($paginator);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, WordService $wordService)
    {
        $requestData = [
            'english_word' => $request->input('english_word'),
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
        $wordsWithTranslations = $wordService->showWordWithTranslations($id);

        if (!$wordsWithTranslations) {
            return response()->json([
                'message' => 'Word not found'
            ], 404);
        }

        return response()->json($wordsWithTranslations);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, WordService $wordService)
    {
        $englishWord = $request->input('english_word');
        $translations = $request->input('translations', []);

        $wordsWithTranslations = $wordService->updateWordWithTranslations(
            $id,
            $englishWord,
            $translations
        );

        if (!$wordsWithTranslations) {
            return response()->json([
                'message' => 'Word not found'
            ], 404);
        }

        return response()->json($wordsWithTranslations);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, WordService $wordService)
    {
        $wordsWithTranslations = $wordService->destroyWordWithTranslations(
            $id
        );
        if ($wordsWithTranslations == null) {
            return response()->json([
                'message' => 'Word not found'
            ], 404);
        } elseif ($wordsWithTranslations) {
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
