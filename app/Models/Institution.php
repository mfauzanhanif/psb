<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type'];

    public function feeComponents(): HasMany
    {
        return $this->hasMany(FeeComponent::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'destination_institution_id');
    }

    public function fundTransfers(): HasMany
    {
        return $this->hasMany(FundTransfer::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}
