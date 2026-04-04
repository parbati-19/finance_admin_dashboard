<?php

namespace App\Models;

use App\Models\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'category',
        'date',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'type' => TransactionType::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
