<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use App\Exceptions\WordNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowWordRequest;
use App\Http\Requests\StoreWordRequest;
use App\Http\Requests\UpdateWordRequest;
use App\Http\Resources\WordCollection;
use App\Http\Resources\WordResource;
use App\Services\WordService;

class WordController extends Controller
{
    public function __construct(private WordService $wordService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $paginator = $this->wordService->getAll(
            perPage: $request->getPerPage(),
            cursor: $request->getCursor(),
        );

        return new WordCollection($paginator);
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowWordRequest $request)
    {
        $id = $request->getWordId();

        try {
            $word = $this->wordService->get($id);

            return response()->json(new WordResource($word));
        } catch (WordNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
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
        } catch (WordNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the english word.
     */
    public function update(UpdateWordRequest $request)
    {
        $id          = $request->getWordId();
        $englishWord = $request->input('english_word');

        try {
            $word = $this->wordService->update($id, $englishWord);

            return response()->json(new WordResource($word));
        } catch (WordNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $this->wordService->delete($id);

            return response()->json([
                'message' => 'Word deleted successfully',
            ]);
        } catch (WordNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
