@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="page-title">
        <a href="{{ url()->previous() }}" class="back btn">< Back</a>
        <a href="/admin/members/member/pdf/{{ $member->user->id }}" class="btn btn-primary">Export</a>
    </div>
    <div class="member-view">
        <div class="header">
            <div class="row">
                <div class="col-md-2">
                    <div class="member-photo">
                        <img src="{{ url('storage/images/'. $member->user->avatar) }}" alt="{{ $member->user->name }}" title="{{ $member->user->name }}" class="list-profile-photo" />
                    </div>
                </div>
                <div class="col-md-10">
                    <ul class="detail-list">
                        <li>
                            <span class="label">Name</span>
                            <div class="value">{{ $member->name }}</div>
                        </li>
                        <li>
                            <span class="label">Email</span>
                            <div class="value">{{ $member->user->email }}</div>
                        </li>
                        <li>
                            <span class="label">Phone</span>
                            <div class="value">{{ $member->user->phone }}</div>
                        </li>
                        <li>
                            <span class="label">Civil ID</span>
                            <div class="value">{{ $member->details->civil_id }}</div>
                        </li>
                        <li>
                            <span class="label">Member Type</span>
                            <div class="value">{{ ucfirst($member->type) }}</div>
                        </li>
                        <li>
                            <span class="label">Unit</span>
                            <div class="value">{{ $member->details->member_unit->name }}</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="data-section">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="list-title">Mebership Details</h5>
                    <ul class="detail-list">
                        <li>
                            <span class="label">Membership Type</span>
                            <div class="value">{{ ucfirst($member->membership->type) }}</div>
                        </li>
                        
                        <li>
                            <span class="label">Membership Status</span>
                            <div class="value {{ $member->membership->status =='active' ? 'text-success' : 'text-danger' }}">{{ ucfirst($member->membership->status) }}</div>
                        </li>
                    </ul>
                    
                    <h5 class="list-title">Basic Details</h5>
                    <ul class="detail-list">
                        <li>
                            <span class="label">Gender</span>
                            <div class="value">{{ ucfirst($member->gender) }}</div>
                        </li>
                        <li>
                            <span class="label">Date of birth</span>
                            <div class="value">{{ date('M d, Y', strtotime($member->details->dob)) }}</div>
                        </li>
                        <li>
                            <span class="label">Passport No.</span>
                            <div class="value">{{ $member->details->passport_no }}</div>
                        </li>
                        <li>
                            <span class="label">Passport Expiry</span>
                            <div class="value">{{ date('M d, Y', strtotime($member->details->passport_expiry)) }}</div>
                        </li>
                        <li>
                            <span class="label">Company</span>
                            <div class="value">{{ $member->details->company }}</div>
                        </li>
                        <li>
                            <span class="label">Profession</span>
                            <div class="value">{{ $member->details->profession }}</div>
                        </li>
                        @foreach ($member->contacts as $contact)
                            <li>
                                <span class="label">{{ $contact->title }}</span>
                                <div class="value">{{ $contact->value }}</div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-4">
                    <ul class="proof_list">
                        <li>
                            <div class="image">
                                <img src="{{ url('storage/images/'. $member->details->photo_civil_id_front) }}" alt="{{ $member->user->name }}" title="{{ $member->user->name }}" class="image-fluid"  style="width:160px" />
                            </div>
                            <div class="title">Civil ID 01</div>
                        </li>
                        <li>
                            <div class="image">
                                <img src="{{ url('storage/images/'. $member->details->photo_civil_id_back) }}" alt="{{ $member->user->name }}" title="{{ $member->user->name }}"  class="image-fluid"  style="width:160px"/>
                            </div>
                            <div class="title">Civil ID 02</div>
                        </li>
                        <li>
                            <div class="image">
                                <img src="{{ url('storage/images/'. $member->details->photo_passport_front) }}" alt="{{ $member->user->name }}" title="{{ $member->user->name }}"  class="image-fluid"  style="width:160px"/>
                            </div>
                            <div class="title">Passport copy - 01</div>
                        </li>
                        <li>
                            <div class="image">
                                <img src="{{ url('storage/images/'. $member->details->photo_passport_back) }}" alt="{{ $member->user->name }}" title="{{ $member->user->name }}"  class="image-fluid"  style="width:160px"/>
                            </div>
                            <div class="title">Passport copy - 02</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection