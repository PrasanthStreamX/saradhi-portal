<?php

namespace Modules\Members\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Members\Models\MemberEnum;
use Modules\Members\Models\MembershipRequest;

class MembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:membership_request.verification.show', ['only' => ['requestsForVerification']]);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function requestsForVerification()
    {
        
        $status_model = MemberEnum::where('type', 'request_status')->where('slug', 'pending')->first();
        $requests = MembershipRequest::with(['member', 'details', 'user'])->where('request_status_id', $status_model->id)->get();
        return view('members::admin.membership.request', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('members::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('members::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('members::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}