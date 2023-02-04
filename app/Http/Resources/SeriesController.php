<?php

namespace App\Http\Resources;

use App\Events\CreateSeriesEvent;
use App\Http\Requests\SeriesApiRequest;
use App\Models\Series;
use App\Repositories\SeriesRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

class SeriesController
{
    public function __construct(private SeriesRepository $repository)
    {
    }

    public function index(): SeriesCollection
    {
        return new SeriesCollection(Series::all());
    }

    /**
     * GET de série com suas temporadas e episódios
     *
     * @param int $id
     * @return JsonResponse|JsonResource
     */
    public function show(int $id): JsonResponse | JsonResource
    {
        $seriesCollection = Series::whereId($id)->with('seasons.episodes')->get();

        if (!$seriesCollection->isNotEmpty()) {
            return response()->json(['success' => 'false', 'error' => 'serie not found'], 404);
        }

        return new SeriesResource($seriesCollection);
    }

    /**
     * POST de séries via api, é possível criar um série sem imagem
     * mas não sem os paramêtros name, seasons e episodes
     *
     * @param SeriesApiRequest $request
     * @return JsonResponse
     */
    public function store(SeriesApiRequest $request): JsonResponse
    {
        $data = $request->only(['seasons', 'episodes']);

        $validator = Validator::make($data, [
            'seasons' => 'required',
            'episodes' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => 'false', 'message' => 'Invalid parameters'], 422);
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

    /**
     * Método de update via API, recebe como parametros seasons e episodes
     * diferentes do método update no form
     *
     * @param Series $series
     * @param SeriesApiRequest $request
     * @return JsonResponse
     */
    public function update(Series $series, SeriesApiRequest $request): JsonResponse
    {
        $data = [
            'name' => $request->name,
            'seasonsQuantity' => $request->seasons,
            'episodesQuantity' => $request->episodes,
        ];

        $this->repository->update($data, $series);

        return response()->json(['success' => 'true', 'serie' => $series]);
    }

    /**
     * @param Series $series
     * @return Response
     */
    public function destroy(Series $series): Response
    {
        $this->repository->delete($series);

        return response()->noContent();
    }

    public function upload(SeriesApiRequest $request)
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
