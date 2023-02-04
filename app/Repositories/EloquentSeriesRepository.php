<?php

namespace App\Repositories;

use App\Events\CreateSeriesEvent;
use App\Models\Episodes;
use App\Models\FailMessages;
use App\Models\Series;
use App\Models\Seasons;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EloquentSeriesRepository implements SeriesRepository
{
    /**
     * Método que cria uma série, temporada e episódios
     * rever depois porque o método não deveria receber como paramêtro o SeriesFormRequest $request
     * agora está recebendo o CreateSeriesEvent
     *
     * @param CreateSeriesEvent $event
     * @return Series
     */
    public function add(CreateSeriesEvent $event): Series
    {
        try {
            DB::beginTransaction();

            $serie = Series::create([
                'name' => $event->serieName,
                'cover' => $event->coverPath,
            ]);

            $seasons = Seasons::sumNumbersOfSeasons($event->seasons, 'series_id', $serie->id);
            Seasons::insert($seasons);

            Episodes::createEpisodes($serie->seasons, $event->episodesPerSeason);

            DB::commit();
            return $serie;
        } catch (Exception $ex) {
            return FailMessages::registerFailMessage(__METHOD__, "Erro ao inserir registro do banco EX => {$ex->getMessage()}");
        }
    }

    /**
     * Este método pode retornar uma exception ou o número de rows afetadas pelo delete
     * @param Series $serie
     */
    public function delete(Series $serie)
    {
        try {
            DB::beginTransaction();

            $affected = DB::delete(
                "DELETE FROM series WHERE id = :id",
                [$serie->id]
            );

            if (!is_null($serie->cover) && $affected == 1) {
                Storage::disk('public')->delete($serie->cover);
            }

            DB::commit();

            return $affected;
        } catch (Exception $ex) {
            return FailMessages::registerFailMessage(__METHOD__, "Erro ao deletar registro do banco EX => {$ex->getMessage()}");
        }
    }

    public function update(array $data, Series $series): int
    {
        $rows = DB::update(
            "UPDATE series SET name = :name WHERE id = :id",
            [$data['name'], $series->id]
        );

        if (array_key_exists('episodesQuantity', $data) && array_key_exists('seasonsQuantity', $data)) {
            //lógica do update foi um pouco complexa, aqui, se o número de temporadas que já existem for menor que o número informado pelo
            //user no request, deletamos os registros para $series->id e depois inserimos os registros novamente
            //se for maior, (int) $countSeasons - (int) $request->seasonsQuantity = temporadas que precisamos adicionar
            $countSeasons = Seasons::getCountOfSeasonsPerSerie($series->id);

            $seasons = $data['seasonsQuantity'];
            $episodes = $data['episodesQuantity'];

            if ($countSeasons < $seasons) {
                Seasons::deleteSeasons($series->id);
                $newSeasons = Seasons::sumNumbersOfSeasons($seasons, 'series_id', $series->id);
            } else {
                $seasonToAdd = $countSeasons - (int) $seasons;
                $newSeasons = Seasons::sumNumbersOfSeasons($seasonToAdd, 'series_id', $series->id);
            }

            Seasons::insert($newSeasons);
            Episodes::createEpisodes($series->seasons, $episodes);
        }

        return $rows;
    }
}
