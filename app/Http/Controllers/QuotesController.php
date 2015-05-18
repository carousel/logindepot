<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateQuotesRequest;

use Illuminate\Http\Request;

class QuotesController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
        $user = \Auth::user();
        if($user->role = "company"){
            \View::share("company",$user->first_name);
        }
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getCreate($company)
	{
        return view("companies.quotes.create")
            ->with("company",$company);
	}
	/**

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function postCreate(CreateQuotesRequest $request)
	{
        return $request->all();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}