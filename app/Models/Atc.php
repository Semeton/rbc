<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Atc extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getAtcTypeAttribute(): string
    {
        return match ($this->attributes['atc_type']) {
            'bg' => 'BG',
            'cash_payment' => 'Cash Payment',
        };
    }
}
