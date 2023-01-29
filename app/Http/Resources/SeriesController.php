<?php

namespace App\Http\Resources;

use App\Events\CreateSeriesEvent;
use App\Models\Series;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

class SeriesController
{
    public function index()
    {
        return new SeriesCollection(Series::all());
    }

    public function show(int $id): JsonResponse | JsonResource
    {
        $serie = Series::find($id);

        if (is_null($serie)) {
            return response()->json(['success' => 'false', 'error' => 'serie not found'], 404);
        }

        return new SeriesResource($serie);
    }

    /**
     * POST de séries via api, é possível criar um série sem imagem
     * mas não sem os paramêtros name, seasons e episodes
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        if (!$request->exists(['name', 'seasons', 'episodes'])) {
            return response()->json(
                ['data' => ['success' => 'false', 'error' => 'Invalid parameters or missing parameters']],
                422
            );
        }

        $coverPath = null;

        if ($request->hasFile('cover')) {
            if ($this->validateImage($request->file())) {
                $coverPath = $request->file('cover')->store('cover_series', 'public');
            }
        }

        CreateSeriesEvent::dispatch(
            $request->name,
            $coverPath,
            $request->seasons,
            $request->episodes
        );

        $serie = Series::getLastSerie($request->name);

        //return response json com status code 201 (created)
        return response()->json(['success' => 'true', 'serie' => $serie], 201);
    }

    public function upload(Request $request)
    {

    }

    private function validateImage($file): array
    {
        return Validator::validate($file, [
            'cover' => [
                File::image()->max(2 * 1024)
            ]
        ]);
    }
}
