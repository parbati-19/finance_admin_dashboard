<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Enums\UserType;

class UserDetail extends Model
{
    use SoftDeletes;
    protected $table = 'user_details';
    protected $fillable = [
        'user_id',
        'user_type',
        'company_name',
        'company_pan',
        'contact_person',
        'company_address',
        'company_phone',
        'personal_phone',
        'company_email',
        'is_verified',
    ];

    protected $casts = [
        // Cast user_type to the UserType enum so you can work with enum instances in PHP
        'user_type' => UserType::class,
        'is_verified' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
