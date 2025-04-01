<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'periods_date' => 'datetime',
    ];

    public function members()
    {
        return $this->hasMany(Member::class, 'group_id');
    }

    public static function generateUniqueKey(): string
    {
        $KEY_LENGTH = 6;
        do {
            $key = Str::random($KEY_LENGTH);
        } while (Group::where(DB::raw('BINARY `code`'), $key)->exists());
        return $key;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function owners()
    {
        return $this->hasMany(GroupOwner::class, 'group_id');
    }

    // public function owners()
    // {
    //     return $this->belongsToMany(User::class, 'group_owners');
    // }
}
