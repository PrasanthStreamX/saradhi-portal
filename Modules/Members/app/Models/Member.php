<?php

namespace Modules\Members\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Member extends Model
{
    use HasFactory;
    
    //protected $connection = 'mysql';
    protected $appends = ['is_trustee'];
    

    protected $fillable = [
        'user_id',
        'parent_id',
        'type',
        'name',
        'gender',
        'blood_group',
        'photo',
        'active'
    ];

    //User
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
    // Member details
    public function details(): HasOne
    {
        return $this->hasOne(MemberDetail::class, 'user_id', 'user_id');
    }

    // Membership details
    public function membership(): HasOne
    {
        return $this->hasOne(Membership::class, 'user_id', 'user_id');
    }

    // Member addresses
    public function localAddress(): HasOne
    {
        return $this->hasOne(MemberLocalAddress::class, 'user_id', 'user_id');
    }
    public function permanentAddress(): HasOne
    {
        return $this->hasOne(MemberPermanentAddress::class, 'user_id', 'user_id');
    }
    
    public function notes(): HasMany
    {
        return $this->hasMany(MemberNote::class, 'user_id', 'user_id');
    }

    public function relations(): HasMany
    {
        return $this->hasMany(MemberRelation::class, 'member_id', 'id');
    }
    
    public function requests(): HasMany
    {
        return $this->hasMany(MembershipRequest::class, 'user_id', 'user_id');
    }

    public function committees(): HasMany
    {
        return $this->hasMany(MemberHasCommittee::class, 'user_id', 'user_id');
    }

    public function trustee(): HasOne
    {
        return $this->hasOne(MemberTrustee::class, 'user_id', 'user_id');
    }

    public function getIsTrusteeAttribute(): bool
    {
        return $this->trustee !== null;
    }
}
