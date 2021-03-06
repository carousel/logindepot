<?php namespace App\Http\Controllers;

use App\LoginDepot\Customer;
use App\LoginDepot\Calendar;
use App\LoginDepot\User;
use App\LoginDepot\Company;
use App\LoginDepot\Worker;
use App\Http\Requests\CreateCustomerBasicProfileRequest;
use App\Http\Requests\CreateWorkerRequest;
use App\Http\Requests\UpdateCustomerBasicProfileRequest;
use App\Http\Requests\CreateCompanyProfileRequest;
use App\Http\Requests\CalendarEventShareRequest;
use App\Http\Requests\UpdateWorkerRequest;
use App\LoginDepot\Order;
use App\LoginDepot\Quote;

class CompaniesController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
    //public function getIndex()
    //{
        //return view('companies.dashboard');
    //}
	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function getIndex($company_name)
	{
        $saved_quotes = Quote::where("status","saved")->get();
        $saved_quotes_count = count($saved_quotes);
        return view('companies.dashboard')
            ->with("company_name",$company_name)
            ->with("saved_quotes",$saved_quotes)
            ->with("saved_quotes_count",$saved_quotes_count);
	}
	public function getCalendar($company_name)
	{
        $company = Company::where("company_name",$company_name)->first();
        $workers = Worker::where("company_id",$company->id)->get();
        $workers  = $workers->lists("first_name");
        //$workers["default"] = "Please select a worker";
        $keys = array_values($workers);
        $values = array_values($workers);
        $workers = array_combine($keys,$values);
        return view('companies.calendar')
            ->with("workers",$workers)
            ->with("company_name",$company_name);
	}
    public function getCustomers($company_name)
    {
        $customers = Customer::all();
        if(\Request::ajax()){
            return $customers;
        };
        return view('companies.customer.index')
            ->with("company_name",$company_name)
            ->with("saved_quotes",$saved_quotes)
            ->with("saved_quotes_count",$saved_quotes_count);
    }

    public function getShowCustomer($company_name,$customer)
    {
        $customer = Customer::where("first_name",$customer)->first();
        return view("companies.customers.show")
            ->with("company_name",$company_name)
            ->with("customer",$customer);
    }
    public function getCreateCustomer($company_name)
    {
        return view("companies.customers.create")
            ->with("company_name",$company_name);
    }
    public function getUpdateCustomer($company,$customer,$quote)
    {
        $quote_object = \DB::table("quotes")->where("quote_id",$quote)->first();
        //$customer = \DB::table("customers")->where("order_id",$order)->first();
        $customer_object = Customer::where("order_id",$order)->first();

        $states = \Config::get("lists.states");
        $vehicle_type = \Config::get("lists.vehicle_type");
        return view("companies.quotes.create")
            ->with("company_name",$company)
            ->with("states",$states)
            ->with("vehicle_type",$vehicle_type)
            ->with("quote_id",$quote_object->quote_id);
    }
    public function postUpdateCustomer($company_name,$customer_name,UpdateCustomerBasicProfileRequest $request)
    {
        dd($customer_name);
        $customer = Customer::where("name",$customer_name)->first();
        dd($customer);
        //$customer->first_name = $request["first_name"];
        //$customer->last_name = $request["last_name"];
        //$customer->email = $request["email"];
        //$customer->save();
        //$customer = Customer::where("first_name",$request["first_name"])->first();

        //return \Redirect::to("companies/{$company_name}/customers")
            //->with("update_status","Customer {$request["first_name"]} profile updated")
            //->with("customer",$customer)
            //->with("company_name",$company_name);
    }
public function postCreateCustomer($company_name,CreateCustomerBasicProfileRequest $request)
{
    $company_id = Company::where("company_name",$company_name)->first()->id;
    $customer = new Customer;
    $customer->first_name = $request["first_name"];
    $customer->last_name = $request["last_name"];
    $customer->email = $request["email"];
    $customer->secondary_email = $request["secondary_email"];
    $customer->phone = $request["phone"];
    $customer->company_id = $company_id;
    $customer->save();
    return \Redirect::to("companies/{$company_name}/customers")
        ->with("create_status","Customer {$customer->first_name} profile has been created")
        ->with("company_name",$company_name);
    }
    /**
    * 
    */
    public function postDeleteCustomer($company_name,$customer)
    {
        $customer = Customer::where("first_name",$customer)->first();
        $customer->delete();
        return \Redirect::to("companies/{$company_name}/customers")
            ->with("delete_status","Customer {$customer->first_name} has been deleted")
            ->with("customer",$customer)
            ->with("company_name",$company_name);
        
    }
    public function getWorkers($company_name) {
        $workers = Worker::all();
        return view("companies.workers.index")       
            ->with("company_name",$company_name)
            ->with("workers",$workers);
    }
    public function getUpdateWorker($company_name,$worker)
    {
        $worker_object = Worker::where("first_name",$worker)->first();
        return view("companies.workers.update")
            ->with("company_name",$company_name)
            ->with("worker_object",$worker_object)
            ->with("worker",$worker);
    }
    public function getCreateWorker($company_name)
    {
        return view("companies.workers.create")
            ->with("company_name",$company_name);
    }
    public function postCreateWorker($company_name,CreateWorkerRequest $request)
    {
        $company = Company::where("company_name",$company_name)->first();
        $worker = new Worker;
        $user = new User;
        $user->username = $request["username"];
        $user->email = $request["email"];
        $user->password = \Hash::make($request["password"]);
        $user->role = "worker";
        $user->save();
        $worker->first_name = $request["first_name"];
        $worker->email = $request["email"];
        $worker->last_name = $request["last_name"];
        $worker->account_number = $request["account_number"];
        $worker->company_id = $company->id;
        $worker->save();

        //return \Redirect::to("companies/{$company}/workers")
        return \Redirect::to("/companies/" . $company_name . "/workers")
            ->with("create_status","worker {$worker->first_name} profile has been created")
            ->with("company_name",$company_name);
    }
    public function postDeleteWorker($company_name,$worker)
    {
        $worker = Worker::where("first_name",$worker)->first();

        $user = User::where("email",$worker->email)->first();
        $user->delete();
        $worker->delete();
        return \Redirect::to("/companies/" . $company_name . "/workers")
            ->with("delete_status","worker {$worker->first_name} has been deleted")
            ->with("worker",$worker);
        
    }
    public function postUpdateWorker($company_name,$worker,UpdateWorkerRequest $request)
    {
        $worker = Worker::where("first_name",$worker)->first();
        $worker_user = User::where("email",$worker->email)->first();
        //dd($worker_user->email);
        //dd("hello");
        $worker_user->username = $request["username"];
        $worker_user->email = $request["email"];
        $worker_user->password = $request["password"];
        $worker_user->role = "worker";
        $worker->first_name = $request["first_name"];
        $worker->last_name = $request["last_name"];
        $worker->account_number = $request["account_number"];
        $worker->email = $request["email"];
        $worker_user->save();
        $worker->save();
        //$worker_object = Worker::where("first_name",$request["first_name"])->first();

        return \Redirect::to("companies/{$company_name}/workers")
            ->with("update_status","worker {$request["first_name"]} profile updated")
            ->with("worker",$worker);
    }

    public function getCreateProfile(){
        return view("companies.profile.create");
    }

    public function postCreateProfile(CreateCompanyProfileRequest $request){
        //dd($request->all());
        $user_id = \Auth::user()->id;
        $user = User::where("id",$user_id)->first();
        $user->profile_complete = true;
        $user->save();
        $company = new Company;
        $company->company_name = $request["company_name"];
        $company->dot_number = $request["dot_number"];
        $company->mc_number = $request["mc_number"];
        $company->logo = $request["logo"];
        $company->website = $request["website"];
        $company->user_id = $user_id;
        $company->save();

        return \Redirect::to("/auth/login");
    }
    public function shareCalendar(CalendarEventShareRequest $request){

        if(\Auth::user()->role == "company"){
            $company = Company::where("user_id",\Auth::user()->id)->first();
        }
        $calendar = new Calendar;
        $start_date = $request->start_year . "-" . $request->start_month . "-" . $request->start_day;
        $end_date = $request->end_year . "-" . $request->end_month . "-" . $request->end_day;
        $worker = Worker::where("first_name",$request->worker)->first();
        $calendar->company_id = $company->id;
        $calendar->worker_id = $worker->id;
        $calendar->event_title = $request->title;
        $calendar->event_description = $request->description;
        $calendar->event_color = $request->color;
        $calendar->event_start_date = $start_date;
        $calendar->event_end_date = $end_date;
        $calendar->save();
        return \Redirect::to("/companies/" . $company->company_name . "/calendar")
            ->with("calendar_event_status","New event for {$worker->first_name} has been created");
    }
}
