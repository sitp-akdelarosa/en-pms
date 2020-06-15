<?php

namespace App\Listeners;

use App\Events\Notify;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyListener
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
     * @param  Notify  $event
     * @return void
     */
    public function handle(Notify $event)
    {
        //
    }
}
