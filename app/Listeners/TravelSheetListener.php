<?php

namespace App\Listeners;

use App\Events\TravelSheet;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TravelSheetListener
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
     * @param  TravelSheet  $event
     * @return void
     */
    public function handle(TravelSheet $event)
    {
        //
    }
}
