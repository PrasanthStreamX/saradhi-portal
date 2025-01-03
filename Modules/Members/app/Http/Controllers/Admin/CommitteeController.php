<?php

namespace Modules\Members\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Modules\Members\Models\Member;
use Modules\Members\Models\MemberCommittee;
use Modules\Members\Models\MemberEnum;
use Modules\Members\Models\MemberHasCommittee;
use Modules\Members\Models\MemberUnit;

class CommitteeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menuParent = 'committees';
        $committees = MemberCommittee::where('active', 1)->paginate(25); 
        //dd($committees);
        return view('members::admin.committee.list', compact('committees', 'menuParent'));
    }

    public function show($id, $prevPage = null)
    {
        $menuParent = 'committees';
        $backTo = $prevPage ?  '/admin/committee?page='.$prevPage : '/admin/committee';
        $committee = MemberCommittee::with('unit')->where('id', $id)->first();
        $members = MemberHasCommittee::with('member', 'member.membership')->where('member_committee_id', $committee->id)->orderBy('designation_id', 'asc')->get();
        return view('members::admin.committee.show', compact('committee', 'members', 'menuParent', 'backTo'));
    }

    /**
     * Show the users for creating.
     *
     * @return \Illuminate\Http\Response
     */
    public function autocomplete(Request $request)
    {
        $data = [];
        $input = $request->get('query');
        $data = Member::with('user','membership')
                ->WhereHas('user', function($q) use ($input) {
                    return $q->where('name', 'LIKE', '%' . $input . '%');
                })
                ->take(10)
                ->get();
        return response()->json($data);
    }

    public function create()
    {
        $menuParent = 'committees';
        $committee_types = MemberEnum::select('id', 'slug', 'name', 'category')->where('type', 'committee_type')->get();
        $units = MemberUnit::get();
        return view('members::admin.committee.create', compact('committee_types', 'units', 'menuParent'));
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), 
            [
                'committee_type_id' => 'required'
            ],[
                'committee_type_id' => 'Committee Type is required'
            ]
        );
        if($validator->fails()){
            return Redirect::back()->withErrors($validator)->withInput()->with('error', 'Some fields are not valid');       
        }
        $committee = MemberCommittee::create([
            'committee_type_id' => $input['committee_type_id'],
            'member_unit_id' => isset($input['member_unit_id']) ? $input['member_unit_id'] : null,
            'formed_on' => $input['formed_on'],
            'year' => date('Y', strtotime($input['formed_on']))
        ]);
        
        return redirect('/admin/committee/create_member/'.$committee->id);
    }

    public function createCommitteeMember($id)
    {
        $menuParent = 'committees';
        $committee = MemberCommittee::with('unit')->where('id', $id)->first();
        $designations = MemberEnum::select('id', 'slug', 'name', 'category')->where('type', 'designation')->get();
        return view('members::admin.committee.create_committee_members', compact('committee', 'designations', 'menuParent'));
    }

    public function storeCommitteeMember(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), 
            [
                'committee_id' => 'required'
            ],[
                'committee_id' => 'Committee ID is required'
            ]
        );
        if($validator->fails()){
            return Redirect::back()->withErrors($validator)->withInput()->with('error', 'Some fields are not valid');       
        }
        $committee = MemberCommittee::where('id', $input['committee_id'])->first();
        $designations = $input['designation'];
        $members = $input['members'];
        DB::beginTransaction();
        foreach($designations as $key => $designation){
            MemberHasCommittee::create([
                'member_committee_id' => $committee->id,
                'user_id' => $members[$key],
                'designation_id' => $designation
            ]);
        }
        DB::commit();
        return redirect('/admin/committee/show/'.$input['committee_id']);
    }

    public function edit($id)
    {
        $menuParent = 'committees';
        $backTo =  '/admin/committee/show/'.$id;
        $committee = MemberCommittee::where('id', $id)->first();
        $members = MemberHasCommittee::with('member', 'member.membership')->where('member_committee_id', $committee->id)->orderBy('designation_id', 'asc')->get();
        $units = MemberUnit::get();
        $designations = MemberEnum::select('id', 'slug', 'name', 'category')->where('type', 'designation')->get();
        return view('members::admin.committee.edit', compact('committee', 'members', 'designations', 'units', 'backTo', 'menuParent'));
    }

    public function update(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), 
            [
                'committee_id' => 'required',
                'formed_on' => 'required|date_format:Y-m-d'
            ],[
                'committee_id.required' => 'Committee ID is required',
                'formed_on.required' => 'Committee formation date is required',
                'formed_on.date_format' => 'Invalid committee formation date'
            ]
        );
        if($validator->fails()){
            return Redirect::back()->withErrors($validator)->withInput()->with('error', 'Some fields are not valid');       
        }
        $committee = MemberCommittee::where('id', $input['committee_id'])->first();
        
        
        $designations = $input['designation'];
        $members = $input['members'];
        DB::beginTransaction();
        // Removing old data
        MemberHasCommittee::where('member_committee_id', $committee->id)->delete();

        // Storing new data
        MemberCommittee::where('id', $committee->id)->update([
            'member_unit_id' => isset($input['member_unit_id']) ? $input['member_unit_id'] : null,
            'formed_on' => $input['formed_on'],
            'year' => date('Y', strtotime($input['formed_on'])),
            'active' => $input['status']
        ]);
        foreach($designations as $key => $designation){
            MemberHasCommittee::create([
                'member_committee_id' => $committee->id,
                'user_id' => $members[$key],
                'designation_id' => $designation
            ]);
        }
        DB::commit();
        return redirect('/admin/committee/show/'.$input['committee_id']);
    }
}
