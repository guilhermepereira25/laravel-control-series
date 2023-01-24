<?php

namespace App\Repositories;

use App\Http\Requests\SeriesFormRequest;
use App\Models\Series;

interface SeriesRepository
{
    public function add(SeriesFormRequest $request): Series;

    public function delete(int $serie_id): int;

    public function update(SeriesFormRequest $request, Series $series);
}
