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
    public function __construct(private TranslationService $service) {}

    public function index(Request $request)
    {
        return $this->service->getAll();
    }

    public function show(ShowTranslationRequest $request)
    {
        $id = $request->route('translation');

        try {
            $translation = $this->service->get($id);
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
            $translation = $this->service->create($data);
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
            $translation = $this->service->update($id, $data);
            return response()->json(new TranslationResource($translation));
        } catch (TranslationException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function destroy(Request $request, int $id)
    {
        return $this->service->delete($id);
    }
}