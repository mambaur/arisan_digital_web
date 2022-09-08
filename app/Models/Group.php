<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $dates = ['periods_date'];

    public function members()
    {
        return $this->hasMany(Member::class, 'group_id');
    }
}
