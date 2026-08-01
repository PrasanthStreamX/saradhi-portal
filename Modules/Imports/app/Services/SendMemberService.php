<?php

namespace Modules\Imports\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class SendMemberService
{
    
    private $api;

    public function __construct(){
        $this->api = env('NEW_PORTAL_API');
    }
    /**
     * Sending user details to the old portal
     */
    public function createOrUpdateMember(array $request)
    {
        if(!env('NEW_PORTAL_SYNC')){
            return;
        }
        $url = $this->api.'migration/update/member';
        Http::post($url, $request);
        return true;
    }
}
