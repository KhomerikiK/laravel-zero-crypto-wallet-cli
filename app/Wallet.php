<?php

namespace App;

use App\Traits\Walletable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    protected $fillable = [
        'crypto_currency',
        'bitgo_id',
        'label',
        'passphrase',
    ];

    use HasFactory, Walletable;

    public function accessToken(): BelongsTo
    {
        return $this->belongsTo(AccessToken::class);
    }
}
