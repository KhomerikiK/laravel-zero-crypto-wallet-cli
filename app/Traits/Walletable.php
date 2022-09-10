<?php

namespace App\Traits;

use Khomeriki\BitgoWallet\Facades\Wallet;

trait Walletable
{
    public function bitgo()
    {
        return Wallet::init($this->crypto_currency, $this->bitgo_id);
    }
}
