<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Series;
use App\Models\Episodes;
use Illuminate\Support\Facades\DB;

class Seasons extends Model
{
    use HasFactory;

    protected $fillable = ['number'];
    protected $primaryKey = 'id';

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episodes::class, 'season_id');
    }

    /**
     * Esse método retorna um array com as temporadas para séries (coluna id, number)
     * 
     * @param int $series_id
     * @return array
     */
    public static function getSeasonForSerie(int $series_id): array
    {
        return DB::select(
            "SELECT id, number FROM seasons WHERE series_id = :series_id", 
            [$series_id]
        );
    }

    public static function getCountOfSeasonsPerSerie(int $series_id): int
    {
        return DB::table('seasons')
                    ->where('series_id', $series_id)
                        ->count('id');
    }

    public static function deleteSeasons(int $series_id): int
    {
        return DB::table('seasons')
                    ->where('series_id', '=', $series_id)
                        ->delete();
    }

    /**
     * Retorna o número de temporadas para série
     * 
     * @param int $season_id
     * @return int
     */
    public static function getNumberOfSeasonsPerSerie(array $seasons): int
    {
        $numberSeasons = 0;

        foreach ($seasons as $season) {
            $numberSeasons += $season->number;
        }

        return $numberSeasons;
    }

    public static function sumNumbersOfSeasons(int $quantity, string $column, int $id): array
    {
        for ($index = 1; $index <= $quantity; $index++) {
            $values[] = [
                "$column" => $id,
                'number' => $index
            ];
        }

        return $values;
    }

    public function numberOfWatchedEpisodes(): int 
    {
        return $this->episodes
            ->filter(fn ($episode) => $episode->watched)
            ->count();
    }
}
