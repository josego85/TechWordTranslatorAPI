<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use App\Exceptions\TranslationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowTranslationRequest;
use App\Http\Requests\StoreTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;
use App\Http\Resources\TranslationCollection;
use App\Http\Resources\TranslationResource;
use App\Services\TranslationService;

class TranslationController extends Controller
{
    public function __construct(private TranslationService $translationService) {}

    public function index(IndexRequest $request)
    {
        $paginator = $this->translationService->getAll(
            perPage: $request->getPerPage(),
            cursor: $request->getCursor(),
        );

        return new TranslationCollection($paginator);
    }

    public function show(ShowTranslationRequest $request)
    {
        $id = $request->getTranslationId();

        try {
            $translation = $this->translationService->get($id);

            return response()->json(new TranslationResource($translation));
        } catch (TranslationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function store(StoreTranslationRequest $request)
    {
        $data = $request->validated();

        try {
            $translation = $this->translationService->create($data);

            return response()->json(new TranslationResource($translation));
        } catch (TranslationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateTranslationRequest $request)
    {
        $id   = $request->getTranslationId();
        $data = $request->validated();

        try {
            $translation = $this->translationService->update($id, $data);

            return response()->json(new TranslationResource($translation));
        } catch (TranslationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->translationService->delete($id);

            return response()->json([
                'message' => 'Translations deleted successfully',
            ]);
        } catch (TranslationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
