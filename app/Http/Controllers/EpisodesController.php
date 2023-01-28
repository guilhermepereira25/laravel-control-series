<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\IFlashMessages;
use App\Models\Episodes;
use App\Models\Seasons;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EpisodesController extends Controller implements IFlashMessages
{
    public function index(Seasons $seasons, Request $request)
    {
        $episodes = $seasons->episodes()->get();

        $successMessage = $this->manipulateFlashMessages($request, 'success.message');

        $failMessage = $this->manipulateFlashMessages($request, 'fail.message');

        return view('episodes.index')
                ->with('seasons', $seasons)
                ->with('episodes', $episodes)
                ->with('successMessage', $successMessage)
                ->with('failMessage', $failMessage);
    }

    public function update(Request $request, Seasons $season): RedirectResponse
    {
        $to_route = to_route('episodes.index', $season->id);

        $watchedEpisodes = $request->episodes;

        if (!$watchedEpisodes) {
            $this->setFlashMessages($request, 'fail.message', "Nenhum episódio foi selecionado como assistido, por favor, tente novamente");

            return $to_route;
        }

        $episodeObj = new Episodes();
        $episodeObj->setWatchedEpisodes($watchedEpisodes);

        $this->setFlashMessages($request, 'success.message', "Episódios marcados como assistidos com sucesso");

        return $to_route;
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
        $message = $request->session()->get($key);
        $request->session()->forget($key);

        return $message;
    }
}
