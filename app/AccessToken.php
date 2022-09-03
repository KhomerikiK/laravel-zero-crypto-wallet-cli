<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccessToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
    ];

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }
}
