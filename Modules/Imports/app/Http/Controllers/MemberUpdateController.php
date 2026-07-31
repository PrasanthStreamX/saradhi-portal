<?php

namespace Modules\Imports\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Imports\Services\UpdateMemberService;

class MemberUpdateController extends BaseController
{
    
    public function __construct(
        protected UpdateMemberService $updateMemberService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function updateMember(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'group' => 'required'
            ]);
            if($validator->fails()){
                return $this->sendError('Required fields are empty or incorrect', $validator->errors(), 400);       
            }
            $data = $request->all();
            $user = User::where('email', $data['email'])->first();
            if(!$user){
                return $this->sendError('Could not find user.');
            }
            if($data['group'] == 'basic'){
                $this->updateMemberService->updateBasic($data, $user->id);
                return $this->sendResponse([], 'Member basic details updated successfully');
            }else if($data['group'] == 'address'){
                $this->updateMemberService->updateAddress($data, $user->id);
                return $this->sendResponse([], 'Member address updated successfully');
            }
        } catch (\Exception $e) {
            return $this->sendError('Failed to update member.', $e);
        }
    }
}
