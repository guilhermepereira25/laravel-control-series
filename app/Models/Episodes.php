<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection as SeasonCollection;

class Episodes extends Model
{
    use HasFactory;
    protected $fillable = ['number', 'watched'];

    public function seasons()
    {
        return $this->belongsTo(Seasons::class);
    }

    //scope watched olhar depois
    public function scopeWatched(Builder $query)
    {
        $query->where('watched', '=', 1);
    }

    public static function getEpisodesPerSeason($season_id)
    {
        $episodes = DB::table('episodes')->where('season_id', '=', $season_id)->first('number');

        return $episodes->number;
    }

    public function updateEpisodes(int $quantity, int $series_id): int
    {
        $season = self::getEpisodesBySeason($series_id);

        $rows = DB::update(
            "UPDATE episodes SET number = :number WHERE season_id = :season_id",
            [$quantity, $season->id]
        );

        return $rows;
    }

    public function setWatchedEpisodes(array $watchedEpisodes)
    {
        try {
            DB::beginTransaction();

            $affected = DB::table('episodes')->
                            whereIn('id', $watchedEpisodes)->
                                update(['watched' => 1]);

            DB::commit();
            return $affected;
        } catch (Exception $ex) {
            DB::rollBack();
            echo "ocorreu um erro ao inserir a propriedade watched na tabela episodes => {$ex->getMessage()}";
        }
    }

    public static function createEpisodes(SeasonCollection $seasons, int $episodesQuantity)
    {
        foreach ($seasons as $season) {
            for ($j = 1; $j <= $episodesQuantity; $j++) {
                $episodes[] = [
                    'season_id' => $season->id,
                    'number' => $j,
                ];
            }
        }

        return Episodes::insert($episodes);
    }
}
