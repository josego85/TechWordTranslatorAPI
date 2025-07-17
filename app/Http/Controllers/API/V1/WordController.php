<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\WordNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWordRequest;
use App\Http\Requests\UpdateWordRequest;
use App\Http\Requests\WordIdRequest;
use App\Http\Requests\WordIndexRequest;
use App\Http\Resources\WordCollection;
use App\Http\Resources\WordResource;
use Illuminate\Http\Request;
use App\Services\WordService;

class WordController extends Controller
{
    public function __construct(private WordService $wordService){}

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
     * Display the specified resource.
     */
    public function show(WordIdRequest $request)
    {
        $id = $request->route('word');

        try {  
            $word = $this->wordService->get($id);
            return response()->json(new WordResource($word));
        } catch(WordNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Store a new english word
     */
    public function store(StoreWordRequest $request)
    {
        $data = $request->validated();

        try {
            $word = $this->wordService->create($data);
            return response()->json(new WordResource($word));
        } catch(WordNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the english word.
     */
    public function update(UpdateWordRequest $request)
    {
        $id = $request->route('word');
        $englishWord = $request->input('english_word');

        try {
            $word = $this->wordService->update($id, $englishWord);
            return response()->json(new WordResource($word));
        } catch(WordNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }  
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
