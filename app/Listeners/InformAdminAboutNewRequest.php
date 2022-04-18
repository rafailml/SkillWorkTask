<?php

namespace App\Listeners;

use App\Events\CompanyRequestRecieved;
use Illuminate\Support\Facades\Log;

class InformAdminAboutNewRequest
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
     * @param  \App\Events\CompanyRequestRecieved  $event
     * @return void
     */
    public function handle(CompanyRequestRecieved $event)
    {
        Log::info("Inform admin about new request");
    }
}
