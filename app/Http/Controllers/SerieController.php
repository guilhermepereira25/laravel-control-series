<?php

namespace App\Http\Controllers;

use App\Events\CreateSeriesEvent;
use App\Events\SeriesCreated;
use App\Http\Interfaces\IFlashMessages;
use App\Http\Middleware\AuthenticateSeries;
use App\Http\Requests\SeriesFormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Series;
use App\Models\Episodes;
use App\Repositories\SeriesRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

class SerieController extends Controller implements IFlashMessages
{
    /**
     * @param SeriesRepository $repository
     * @param UserRepository $user
     */
    public function __construct(private SeriesRepository $repository, protected UserRepository $user)
    {
        $this->middleware(AuthenticateSeries::class)->except('index');
    }

    public function index(Request $request)
    {
        $series = Series::getAllSeries();

        $successMessage = $this->manipulateFlashMessages($request, 'success.message');

        return view('series.index')
                ->with('series', $series)
                ->with('successMessage', $successMessage);
    }

    public function create()
    {
        return view('series.create');
    }

    /**
     * @param SeriesFormRequest $request
     * @return RedirectResponse
     */
    public function store(SeriesFormRequest $request): RedirectResponse
    {
        $coverPath = null;

        if ($request->hasFile('cover')) {
            if ($this->validateImage($request->file())) {
                $coverPath = $request->file('cover')->store('cover_series', 'public');
            }
        }

        CreateSeriesEvent::dispatch(
            $request->name,
            $coverPath,
            $request->seasonsQuantity,
            $request->episodesQuantity
        );

        $serie = Series::getLastSerie($request->name);

        if (is_null($serie)) {
            return redirect()->back()->withErrors("Ocorreu um erro ao adicionar a série, tente novamente");
        }

        SeriesCreated::dispatch(
            $serie->name,
            $serie->id,
            $request->seasonsQuantity,
            $request->episodesQuantity,
            $request->user()->name
        );

        $this->setFlashMessages($request, 'success.message', "Serie '$serie->name' inserida com sucesso");

        return to_route('series.index');
    }

    public function destroy(Series $series, Request $request)
    {
        $this->repository->delete($series);

        $this->setFlashMessages($request, 'success.message', "Serie {$series->name} removida com sucesso");

        return to_route('series.index');
    }

    public function edit(Series $series)
    {
        $seasons = $series->seasons()->count('id');

        $seasonObj = $series->seasons();
        $seasonId = $seasonObj->first('id');

        $episodes = Episodes::getEpisodesPerSeason($seasonId->id);

        return view('series.edit')
            ->with('series', $series)
            ->with('seasons', $seasons)
            ->with('episodes', $episodes);
    }

    public function update(SeriesFormRequest $request, Series $series)
    {
        $rows = $this->repository->update($request, $series);

        if ($rows) {
            $this->setFlashMessages($request, 'success.message', "Serie {$series->name} atualizada com sucesso");
        }

        return to_route('series.index');
    }

    /**
     * Função que seta as mensagens
     * @param Request $request
     * @param string $key
     * @param string $message
     * @return void
     */
    public function setFlashMessages(Request $request, string $key, string $message): void
    {
        $request->session()->put($key, $message);
    }

    public function manipulateFlashMessages(Request $request, string $key)
    {
        $successMessage = $request->session()->get($key);
        $request->session()->forget($key);

        return $successMessage;
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
