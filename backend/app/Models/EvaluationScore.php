<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationScore extends Model
{
    use HasFactory;

    protected $fillable = ['evaluation_id', 'item_id', 'response_text', 'file_path', 'score'];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function item()
    {
        return $this->belongsTo(EvaluationItem::class, 'item_id');
    }
}