<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Templates;
use App\Models\Trackings;
use App\Models\EmailSentTrackings;

class Campaigns extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'template','lists','type','schedule','subject','from_name', 'from_email','status', 'repeat', 'last_run', 'stop_at', 'total_runs', 'batch_id', 'run_at'];

    public function templates(){
        return $this->belongsTo(Templates::class, 'template');
    }
    public function trackings(){
        return $this->hasMany(Trackings::class,'campaign_id', 'id');
    }

    public function sentTrackings(){
        return $this->hasMany(EmailSentTrackings::class,'campaign_id', 'id');
    }
}
