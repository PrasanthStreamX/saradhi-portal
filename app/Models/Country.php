<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'calling_code', 'flag'];

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class, 'country_code', 'code');
    }
}
