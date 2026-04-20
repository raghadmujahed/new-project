<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioEntry extends Model
{
    use HasFactory;

    protected $fillable = ['student_portfolio_id', 'title', 'content', 'file_path'];

    public function studentPortfolio()
    {
        return $this->belongsTo(StudentPortfolio::class);
    }
}