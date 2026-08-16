<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
    ];

    // This creates the relationship so you always know WHICH user did the action
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}