<?php

namespace Modules\Members\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Members\Models\Member;
use Modules\Members\Models\MemberDetail;
use Modules\Members\Models\MembershipRequest;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('members::index');
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
        $member = Member::with(['user', 'details', 'membership', 'contacts', 'addresses', 'relations', 'requests', 'committees', 'trustee'])->where('user_id' , $id)->first();
        return view('members::admin.member.show', compact('member'));
    }

    /**
     * Generate member view pdf
     */
    public function generatePDF($id)
    {
        $member = Member::with(['user', 'details', 'membership', 'contacts', 'addresses', 'relations', 'requests', 'committees', 'trustee'])->where('user_id' , $id)->first();
        
        $data = [
            'title' => 'Membership Application',
            'date' => date('M d, Y'),
            'member' => $member
        ];

        //return view('members::admin.member.pdf', compact('data'));
        $pdf = Pdf::loadView('members::admin.member.pdf', compact('data'));

        return $pdf->download('member_request_'.str_replace(" ", "-", $member->user->name).'.pdf');

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