<?php

namespace App\Listeners;

use App\Events\SeriesCreated as SeriesCreatedEvent;
use App\Mail\MailSeriesCreated;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EmailUsersAboutSeriesCreated implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param SeriesCreatedEvent $event
     * @return void
     */
    public function handle(SeriesCreatedEvent $event): void
    {
        $usersList = User::all();

        foreach ($usersList as $key => $user) {
            $mail = new MailSeriesCreated(
                $event->serieName,
                $event->serieId,
                $event->seasonsQuantity,
                $event->episodesPerSeason,
                $user->name,
            );

            $when = now()->addSeconds($key * 5);

            Mail::to($user)->later($when, $mail);
        }
    }
}
