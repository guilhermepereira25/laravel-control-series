<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;
use App\Models\Seasons;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Series extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'cover'];
    private string $name;

    public function seasons(): HasMany
    {
        return $this->hasMany(Seasons::class, 'series_id');
    }

    public function episodes(): HasManyThrough
    {
        return $this->hasManyThrough(
            Episodes::class,
            Seasons::class,
            'series_id',
            'season_id'
        );
    }

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function (Builder $queryBuilder) {
            $queryBuilder->orderBy('name');
        });
    }

    public static function getAllSeries(): array
    {
        return DB::select('SELECT id, name, cover FROM series;');
    }

    public static function getLastSerie(string $serieName)
    {
        return DB::table('series')->where('name', $serieName)->first();
    }
}
