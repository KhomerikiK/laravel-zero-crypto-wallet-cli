<?php

namespace App\Commands\Traits;

use App\AccessToken;
use App\Wallet;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Khomeriki\BitgoWallet\Adapters\BitgoAdapter;

trait WalletCommendable
{
    private function checkDatabase(): void
    {
        $default = config('database.default');
        $database = config("database.connections.$default.database");
        if ($default == 'sqlite' && ! file_exists($database)) {
            touch($database);
            Artisan::call('migrate');
            $this->info('SQLite database created');
        }
    }

    protected function baseUnitToCoin(int $baseUnits): string
    {
        return number_format($baseUnits / 100000000, 7);
    }

    protected function usdFormat(float $usd): string
    {
        return number_format($usd, 2);
    }

    protected function coinToBaseUnit(float $coinAmount): int
    {
        return (int) ($coinAmount * 100000000);
    }

    public function availableCoins()
    {
        return $this->choice(
            'Choose crypto currency',
            [
                'tbtc',
                'tltc',
                'tzec',
                'tdash',
                'tbch',
            ],
            0
        );
    }

    public function authorize(): AccessToken
    {
        $this->checkDatabase();
        START:
        $accessToken = Cache::get('access_token');
        $expressApiUrl = Cache::get('bitgo_express_url');
        if (! $expressApiUrl) {
            $this->warn('Bitgo express api url not set');
            $this->call('bitgo:express');
        }

        $token = AccessToken::where('token', $accessToken)->first();
        if (! $token) {
            $this->call('start');
            goto START;
        }

        config(['bitgo.api_key' => $token->token]);
        config(['bitgo.express_api_url' => $expressApiUrl]);

        $bitgo = new BitgoAdapter();
        $res = $bitgo->me()->status();

        if ($res != 200) {
            $this->call('start');
            goto START;
        }

        return $token;
    }

    protected function selectWallet(AccessToken $token, ?string $walletId = null): ?Wallet
    {
        if ($walletId) {
            $selectedWallet = $token->wallets()->where('bitgo_id', $walletId)->first();
        } else {
            $wallets = $token->wallets()
                ->select('crypto_currency', 'bitgo_id', 'label', 'passphrase')
                ->get();

            $option = $this->choice(
                'Choose your wallet:',
                $wallets->pluck('label')->toArray(),
                0
            );

            $selectedWallet = $wallets->where('label', $option)->first();
        }

        return $selectedWallet;
    }
}
