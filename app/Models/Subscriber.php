<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{

    protected $fillable = [
        "email",
        "user_id",
        "name"
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function campaigns() {
        return $this->belongsToMany(Campaign::class)->withPivot('opened', 'opened_at')->withTimestamps();
    }
    
}
