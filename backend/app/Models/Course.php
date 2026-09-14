<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'source_filename',
        'source_path',
        'status',
        'generation_error',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function syllabi(): HasMany
    {
        return $this->hasMany(Syllabus::class)->orderBy('position');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function studyForms(): HasMany
    {
        return $this->hasMany(StudyForm::class);
    }
}