<?php

namespace App\Repositories;

use App\Events\CreateSeriesEvent;
use App\Models\Series;

interface SeriesRepository
{
    public function add(CreateSeriesEvent $event);

    public function delete(Series $serie);

    public function update(array $data, Series $series);
}
