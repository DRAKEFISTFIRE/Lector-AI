<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyFormItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_form_id',
        'prompt',
        'expected_answer',
        'type',
        'position',
    ];

    public function studyForm(): BelongsTo
    {
        return $this->belongsTo(StudyForm::class);
    }
}