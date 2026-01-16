<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentParent extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'type',
        'name',
        'life_status',
        'nik',
        'place_of_birth',
        'date_of_birth',
        'education',
        'pesantren_education',
        'job',
        'income',
        'phone_number',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
