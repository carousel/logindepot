<?php namespace App\Handlers\Events;

use App\Events\CompanyWasRegistered;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class CompanyRegisteredHandler {

	/**
	 * Create the event handler.
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
	 * @param  CompanyWasRegistered  $event
	 * @return void
	 */
	public function handle(CompanyWasRegistered $event)
	{
	}
    /**
    * 
    */
    public function onCompanyRegistration($data)
    {
        //send mail is registered
        \Mail::send("emails.welcome",[],function($message) use ($data){
            $message->to($data["email"])
                ->subject("Welcome to LoginDepot");
        });
        return true;
    }
    /**
    * 
    */
    public function subscribe($events)
    {
        $events->listen("App\Events\CompanyRegistered"
            ,"App\Handlers\Events\CompanyRegisteredHandler@onCompanyRegistration");
        
    }

}
