<?php

namespace App\Repositories;

use App\Events\CreateSeriesEvent;
use App\Http\Requests\SeriesFormRequest;
use App\Models\Series;

interface SeriesRepository
{
    public function add(CreateSeriesEvent $request): Series;

    public function delete(Series $serie): mixed;

    public function update(SeriesFormRequest $request, Series $series);
}
