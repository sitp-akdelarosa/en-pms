<?php

namespace App\Listeners;

use App\Events\TransferItems;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TransferItemsListener
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
     * @param  TransferItems  $event
     * @return void
     */
    public function handle(TransferItems $event)
    {
        //
    }
}
