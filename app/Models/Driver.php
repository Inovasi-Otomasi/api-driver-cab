<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Driver extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
    protected $hidden = [
        'password',
    ];
}
