<?php

namespace Modules\Members\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Members\Models\MemberDetail;
use Modules\Members\Models\Member;
use Modules\Members\Models\MemberEnum;
use Modules\Members\Models\Membership;
use Modules\Members\Models\MembershipRequest;
use Modules\Members\Notifications\MembershipApproval;

class MembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware(
            'permission:membership_request.verification.show|membership_request.review.show|membership_request.approval.show|membership_request.confirm', 
            ['only' => ['requests']]
        );
        $this->middleware(
            'permission:membership_request.verification.verify|membership_request.review.review|membership_request.approval.approve|membership_request.confirm', 
            ['only' => ['changeStatus']]
        );
        $this->middleware('permission:membership_request.confirm', ['only' => ['confirmMembershipRequest']]);
    }
    
    /**
     * Display list of membership requests.
     */
    public function requests(Request $request)
    {
        $menuParent = 'requests';
        $results = MembershipRequest::with(['member', 'details', 'user', 'member.relations.relationship'])->where('checked', 0)->orderBy('id', 'desc');
        
        if($request->query('type')){
            $type = $request->query('type');
        }else{
            $type = 'submitted';
        }
        switch ($type) {
            case 'submitted':
                $results = $results->where('request_status_id', 3)->paginate();
                break;
            case 'verified':
                $results = $results->where('request_status_id', 4)->paginate();
                break;
            case 'reviewed':
                $results = $results->where('request_status_id', 5)->paginate();
                break;
            case 'approved':
                $results = $results->where('request_status_id', 6)->paginate();
                break;
            case 'rejected':
                $results = $results->where('request_status_id', 1)->paginate();
                break;
            default:
                $results = $results->where('request_status_id', 3)->paginate();
                $type = 'submitted';
                break; 
        }
        //dd($results);
        foreach($results as $requested_user){
            $requested_user->duplicate_civil_id = null;
            $requested_civil_id = $requested_user->details->civil_id;
            $duplicate = MemberDetail::select('user_id')->where('civil_id',$requested_civil_id)->where('user_id', '!=', $requested_user->user_id)->get();
            if($duplicate){
                $requested_user->duplicate_civil_id = $duplicate->count();
            }
        };
        $requests = requestsByPermission($results);
        //dd($requests);
        return view('members::admin.membership.request', compact('requests','type', 'menuParent'));
    }

    public function changeStatus(Request $request)
    {
        $user = Auth::user();

        $input = $request->all();
        $user_id = $input['user_id'];
        $current_status_id = $input['current_status_id'];
        $active_request = MembershipRequest::where('user_id', $user_id)->where('request_status_id', $current_status_id)->where('checked', 0)->first();
        if($active_request){

            $current_status = MemberEnum::where('type', 'request_status')->where('id', $current_status_id)->first();
            $current_status_order = $current_status->order;

            if($request->input('action') == 'revise'){
                if($current_status->slug == 'rejected'){
                    $previous_status_id = $active_request->rejected;
                    MembershipRequest::where('user_id', $user_id)->where('request_status_id', $previous_status_id)->update([
                        'checked' => 0
                    ]);
                }else{
                    $previous_status_order = $current_status_order - 1;
                    $previous_status =  MemberEnum::where('type', 'request_status')->where('order',  $previous_status_order)->first();
                    MembershipRequest::where('user_id', $user_id)->where('request_status_id', $previous_status->id)->update([
                        'checked' => 0
                    ]);
                }
                $active_request->delete();
                return redirect()->back();
            }

            if($request->input('action') == 'reject' && $current_status->slug == 'rejected'){
                return redirect()->back()->with('error', 'The request already rejected');
            }

            
            $next_status_order = $current_status_order + 1;
            if($current_status->order == 0){ //if current status is rejected
                $old_status_order = MembershipRequest::where('user_id', $user_id)->where('request_status_id', $current_status->rejected)->first();
                $next_status_order = $old_status_order + 1;
            }
            
            $active_request->checked = 1;
            $active_request->save();

            $rejected = null;
            if($request->input('action') == 'reject'){
                $new_status = MemberEnum::where('type', 'request_status')->where('order', 0)->first();
                $rejected = $current_status_id;
            }else{
                $new_status = MemberEnum::where('type', 'request_status')->where('order', $next_status_order)->first();
            }
            MembershipRequest::create([
                'user_id' => $user_id,
                'request_status_id' => $new_status->id,
                'rejected' => $rejected,
                'updated_by' => $user->id,
                'remark' => $input['remark']
            ]);
        }

        return redirect()->back();
    }

    public function confirmMembershipRequest(Request $request){
        $admin = Auth::user();
        $input = $request->all();

        $validator = Validator::make($request->all(), ...$this->validationRules());

        if($validator->fails()){
            return Redirect::back()->withErrors($validator)->withInput()->with('error', 'Some fields are not valid');
        }

        $user_id = $input['user_id'];

        

        // check active request and it is ready to confirm
        $approved_status = MemberEnum::where('type', 'request_status')->where('slug', 'approved')->first();
        $new_status = MemberEnum::where('type', 'request_status')->where('slug', 'confirmed')->first();
        $active_request = MembershipRequest::where('user_id', $user_id)->where('request_status_id', $approved_status->id)->where('checked', 0)->first();
        
        if($active_request){
            $membership = Membership::where('user_id', $user_id)->first();
            $member = Member::where('user_id', $user_id)->first();
            $user = User::find($user_id);

            // Update current request status to checked
            $active_request->checked = 1;
            $active_request->save();

            MembershipRequest::create([
                'user_id' => $user_id,
                'request_status_id' => $new_status->id,
                'checked' => 1,
                'updated_by' => $admin->id,
                'remark' => $input['remark']
            ]);

            // Updating membership table
            $membership->mid = $input['mid'];
            $membership->start_date = $input['start_date'];
            $membership->updated_date = $input['start_date'];
            $membership->expiry_date = date('Y-m-d', strtotime('+1 year', strtotime($input['start_date'])));
            $membership->status = 'active';
            $membership->save();

            // Updating members table
            $member->active = 1;
            $member->save();

            $messages['hi'] = "Hi {$user->name}";
            $messages['message'] = "Congratulations!. Your membership application has been approved.";
            $user->notify(new MembershipApproval($messages));

            return redirect()->back()->with('success', 'Successfully confirmed the request');
        }

        return Redirect::back()->withErrors(['request' => ['Invalid request']]);

    }

    protected function validationRules()
    {
        $rules =  [
            'user_id'      => ['required', Rule::exists(User::class, 'id')],
            //'mid'          => ['required', Rule::unique(Membership::class, 'mid')]
            'mid'          => ['required']
        ];
        $messages = [
            'user_id.required' => 'User ID is required',
            'user_id.exists' => 'User ID is not valid',
            'mid.required' => 'Membership ID is required',
            //'mid.exists' => 'Membership ID is already used',
        ];

        return [
            $rules,
            $messages
        ];
    }
 
}
