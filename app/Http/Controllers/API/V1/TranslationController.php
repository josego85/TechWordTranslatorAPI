<?php

namespace App\Http\Controllers\API\V1;

use App\Exceptions\TranslationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShowTranslationRequest;
use App\Http\Requests\StoreTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;
use App\Http\Resources\TranslationResource;
use App\Services\TranslationService;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function __construct(private TranslationService $translationService) {}

    public function index(Request $request)
    {
        return $this->translationService->getAll();
    }

    public function show(ShowTranslationRequest $request)
    {
        $id = $request->route('translation');

        try {
            $translation = $this->translationService->get($id);
            return response()->json(new TranslationResource($translation));
        } catch (TranslationException $e) {
            return response()->json([
                'message' => $e->getMessage()
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
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateTranslationRequest $request)
    {
        $id = $request->route('translation');
        $data = $request->validated();

        try {
            $translation = $this->translationService->update($id, $data);
            return response()->json(new TranslationResource($translation));
        } catch (TranslationException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->translationService->delete($id);
            return response()->json([
                'message' => 'Translations deleted successfully'
            ]);
        } catch(TranslationException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}