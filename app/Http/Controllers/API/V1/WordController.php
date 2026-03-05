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
use Illuminate\Support\Facades\Log;

class WordController extends Controller
{
    public function __construct(private readonly WordService $wordService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $paginator = $this->wordService->getAll(
            perPage: $request->getPerPage(),
            page: $request->getPage(),
            search: $request->getSearch(),
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
     * Store a new English word.
     */
    public function store(StoreWordRequest $request)
    {
        $data = $request->validated();

        try {
            $word = $this->wordService->create($data);

            Log::info('Word created', ['word_id' => $word->id, 'english_word' => $word->english_word, 'ip' => $request->ip()]);

            return response()->json(new WordResource($word), 201);
        } catch (WordNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the English word.
     */
    public function update(UpdateWordRequest $request)
    {
        $id          = $request->getWordId();
        $englishWord = $request->input('english_word');

        try {
            $word = $this->wordService->update($id, $englishWord);

            Log::info('Word updated', ['word_id' => $word->id, 'english_word' => $word->english_word, 'ip' => $request->ip()]);

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

            Log::warning('Word deleted', ['word_id' => $id, 'ip' => request()->ip()]);

            return response()->json(null, 204);
        } catch (WordNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
