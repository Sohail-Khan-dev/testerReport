<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'project_id',
        'date',
        'task_tested',
        'bug_reported',
        'other',
        'regression',
        'smoke_testing',
        'client_meeting',
        'daily_meeting',
        'mobile_testing',
        'description',
        'automation'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function project(){
        return $this->belongsTo(Project::class);
    }

}
