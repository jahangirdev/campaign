<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'country', 'address', 'list_id', 'full_name', 'status', 'attemps', 'clicks', 'opens', 'last_sent', 'point', 'validity'];
}
