<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Seasons;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Series extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    private string $name;

    public function seasons(): HasMany
    {
        return $this->hasMany(Seasons::class, 'series_id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function (Builder $queryBuilder) {
            $queryBuilder->orderBy('name');
        });
    }
    
    public static function getAllSeries(): array
    {
        $series = DB::select('SELECT id, name FROM series;');

        return $series;
    }
}
