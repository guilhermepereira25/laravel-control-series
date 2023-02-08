<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Series extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'cover'];
    protected $appends = ['links'];
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

    public static function getLastSerie(string $serieName)
    {
        return DB::table('series')->where('name', $serieName)->first();
    }

    public function links(): Attribute
    {
        return new Attribute(
            get: fn () => [
                [
                    'rel' => 'self',
                    'url' => "api/series/{$this->id}"
                ],
                [
                    'rel' => 'seasons',
                    'url' => "api/series/{$this->id}/seasons"
                ],
                [
                    'rel' => 'episodes',
                    'url' => "api/series/{$this->id}/episodes"
                ]
            ]
        );
    }
}
