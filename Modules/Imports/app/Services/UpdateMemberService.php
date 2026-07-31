<?php

namespace Modules\Imports\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Modules\Imports\Models\Member;
use Modules\Imports\Models\MemberDetail;
use Modules\Members\Repositories\MemberRepository;
use Modules\Members\Repositories\MemberUnitRepository;

class UpdateMemberService
{
    
    private $api;
    private $accessToken;
    private $headers;
    private $thisUser;

    public function __construct(
        protected MemberRepository $memberRepository,
        protected MemberUnitRepository $unitRepository,
    ){
        $this->api = env('NEW_PORTAL_API').'api/';
        if(!session()->has('transfer_token')){
            $this->login();
        }
        $this->accessToken = Session::get('transfer_token');
        $this->headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->accessToken,
        ];
        $this->thisUser = Auth::user();
    }

    /**
     * Login to new portal
     */
    private function login(){
        $response = Http::post($this->api.'auth/login', [
            'email' => 'shanoob.sekhar@gmail.com',
            'password' => 'abc@123',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            session(['transfer_token' => $data['data']['token']]);
        }
    }

    /**
     * Update member - basic details
     */
    public function updateBasic(array $data, int $user_id)
    {
        try {
            $data['whatsapp_code'] = $data['whatsapp_calling_code'];

            $userData = Arr::only($data, ['name', 'calling_code', 'phone']);
            $memberData = Arr::only($data, ['gender', 'blood_group']);
            $memberDetailsData = Arr::only($data, ['dob', 'emergency_phone_code', 'whatsapp_code', 'emergency_phone', 'whatsapp']);
            
            DB::beginTransaction();
            User::update(['id' => $user_id], $userData);
            Member::update(['user_id' => $user_id], $memberData);
            MemberDetail::update(['user_id' => $user_id], $memberDetailsData);
            DB::commit();
            return;
        } catch(\Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
