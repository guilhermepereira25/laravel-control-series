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
