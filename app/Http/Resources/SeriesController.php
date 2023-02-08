<?php

namespace App\Http\Resources;

use App\Events\CreateSeriesEvent;
use App\Http\Controllers\Controller;
use App\Http\Middleware\AuthenticateSeriesResource;
use App\Http\Requests\SeriesApiRequest;
use App\Models\Series;
use App\Models\User;
use App\Repositories\SeriesRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

class SeriesController extends Controller
{
    public function __construct(private SeriesRepository $repository)
    {
        $this->middleware(AuthenticateSeriesResource::class)->except('login');
    }

    public function index(Request $request): SeriesCollection | JsonResponse
    {
        if (!$request->has('name')) {
            $query = Series::query();
            $seriesCollection = $query->paginate(5);

            return new SeriesCollection($seriesCollection);
        }

        $seriesCollection = Series::where('name', 'LIKE', '%' . $request->name . '%')->get();

        return !$seriesCollection->isNotEmpty() ? response()->json(['success' => 'false', 'error' => 'serie not found'], 404) : new SeriesCollection($seriesCollection);
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

        return !$seriesCollection->isNotEmpty() ? response()->json(['success' => 'false', 'error' => 'serie not found'], 404) : new SeriesResource($seriesCollection);
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

    private function generateManualJWT(): string
    {
        $token  = base64_encode(random_bytes(12));
        $secret = base64_encode(random_bytes(24));

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $payload = [
            'token' => $token
        ];

        $jwt = sprintf(
            '%s.%s',
            $this->manipulateJWT(json_encode($header)),
            $this->manipulateJWT(json_encode($payload))
        );

        return sprintf(
            '%s.%s',
            $jwt,
            $this->manipulateJWT(hash_hmac('SHA256', $jwt, base64_decode($secret), true))
        );
    }

    private function manipulateJWT($data): string
    {
        //codifica os dados com MIME base64
        $base_64_encode = base64_encode($data);

        if (!$base_64_encode) {
            return false;
        }

        //translate characters or replace substring
        $url = strtr($base_64_encode, '+/', '-_');

        //remove espaço em branco no final da string
        return rtrim($url, '=');
    }
}
