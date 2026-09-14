<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Syllabus extends Model
{
    use HasFactory;

    protected $table = 'syllabi';

    protected $fillable = [
        'course_id',
        'unit_title',
        'summary',
        'key_points',
        'position',
    ];

    protected $casts = [
        'key_points' => 'array',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}