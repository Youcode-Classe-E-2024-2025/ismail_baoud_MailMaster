<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function newsletter() {
        return $this->belongsTo(Newsletter::class);
    }
    
    public function subscribers() {
        return $this->belongsToMany(Subscriber::class)->withPivot('opened', 'opened_at')->withTimestamps();
    }
    
}
