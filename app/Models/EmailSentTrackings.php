<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Trackings;

class EmailSentTrackings extends Model
{
    use HasFactory;

    protected $fillable = ['campaign_id', 'batch_id', 'total_sent'];

    public function trackings(){
        return $this->hasMany(Trackings::class,'batch_id','batch_id');
    }
}
