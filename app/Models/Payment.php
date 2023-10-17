<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * @mixin Model
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'fio', 'email', 'phone_number', 'amount'
    ];

    protected $casts = [
        'amount' => 'integer'
    ];
}
