<?php

namespace App\Repositories;

use App\Http\Requests\SeriesFormRequest;
use App\Models\Episodes;
use App\Models\Series;
use App\Models\Seasons;
use App\Repositories\SeriesRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class EloquentSeriesRepository implements SeriesRepository
{
    /**
     * Método que cria uma série, temporada e episódios
     * rever depois porque o método não deveria receber como paramêtro o SeriesFormRequest $request
     *
     * @param SeriesFormRequest
     * @return Series
     */
    public function add(SeriesFormRequest $request): Series
    {
        try {
            DB::beginTransaction();

            $serie = Series::create($request->only('name'));

            $seasons = [];
            $seasons = Seasons::sumNumbersOfSeasons($request->seasonsQuantity, 'series_id', $serie->id);

            if ($seasons) {
                Seasons::insert($seasons);

                Episodes::createEpisodes($serie->seasons, $request->episodesQuantity);
            }

            DB::commit();
            return $serie;
        } catch (Exception $ex) {
            $ex->getMessage();
        } finally {
            DB::rollBack();
            return $serie;
        }
    }

    public function delete(int $serie_id): int
    {
        try {
            DB::beginTransaction();

            $affected = DB::delete(
                "DELETE FROM series WHERE id = :id",
                [$serie_id]
            );

            DB::commit();

            return $affected;
        } catch (Exception $ex) {
            $ex->getMessage();
        } finally {
            DB::rollBack();

            return $affected;
        }
    }

    public function update(SeriesFormRequest $request, Series $series)
    {
        $rows = DB::update(
            "UPDATE series SET name = :name WHERE id = :id",
            [$request->name, $series->id]
        );

        if ($rows) {
            //lógica do update foi um pouco complexa, aqui, se o número de temporadas que já existem for menor que o número informado pelo
            //user no request, deletamos os registros para $series->id e depois inserimos os registros novamente
            //se for maior, (int) $countSeasons - (int) $request->seasonsQuantity = temporadas que precisamos adicionar
            $countSeasons = Seasons::getCountOfSeasonsPerSerie($series->id);
            $newSeasons = [];

            if ($countSeasons < $request->seasonsQuantity) {
                Seasons::deleteSeasons($series->id);

                $newSeasons = Seasons::sumNumbersOfSeasons($request->seasonsQuantity, 'series_id', $series->id);

                Seasons::insert($newSeasons);

                Episodes::createEpisodes($series->seasons, $request->episodesQuantity);
            } else {
                $seasonToAdd = (int) $countSeasons - (int) $request->seasonsQuantity;

                $newSeasons = Seasons::sumNumbersOfSeasons($seasonToAdd, 'series_id', $series->id);

                Seasons::insert($newSeasons);

                Episodes::createEpisodes($series->seasons, $request->episodesQuantity);
            }
        }

        return $rows;
    }
}
