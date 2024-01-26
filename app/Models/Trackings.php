<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trackings extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = ['campaign_id', 'contact_id', 'batch_id', 'opens', 'clicks', 'unsubscribe'];
}
