<?php

namespace App\Listeners;

use App\Events\CreateSeriesEvent;
use App\Repositories\SeriesRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateSeriesListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(private SeriesRepository $repository)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CreateSeriesEvent $event
     * @return void
     */
    public function handle(CreateSeriesEvent $event): void
    {
        $this->repository->add($event);
    }
}
