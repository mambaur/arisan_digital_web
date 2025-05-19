<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArisanHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'date' => 'datetime',
    ];

    protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function arisanHistoryDetails()
    {
        return $this->hasMany(ArisanHistoryDetail::class, 'arisan_history_id');
    }

    public function winners()
    {
        return $this->hasMany(ArisanHistoryWinner::class, 'arisan_history_id');
    }
}
