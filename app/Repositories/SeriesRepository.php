<?php

namespace App\Repositories;

use App\Events\CreateSeriesEvent;
use App\Http\Requests\SeriesFormRequest;
use App\Models\Series;
use Exception;

interface SeriesRepository
{
    public function add(CreateSeriesEvent $event);

    public function delete(Series $serie);

    public function update(SeriesFormRequest $request, Series $series);
}
