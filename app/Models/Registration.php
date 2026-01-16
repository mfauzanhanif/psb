<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'previous_school_level',
        'previous_school_name',
        'previous_school_npsn',
        'previous_school_address',
        'destination_institution_id',
        'destination_class',
        'funding_source',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function destinationInstitution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'destination_institution_id');
    }
}
