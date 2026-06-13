<?php

namespace App\Models\reports;

use App\Models\reports\ReportAction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reportable()
    {
        return $this->morphTo();
    }

    public function actions()
    {
        return $this->hasMany(ReportAction::class);
    }
}
