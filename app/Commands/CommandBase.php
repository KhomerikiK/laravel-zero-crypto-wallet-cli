<?php

namespace App\Commands;

use App\AccessToken;
use App\Wallet;
use Illuminate\Support\Facades\Cache;
use Khomeriki\BitgoWallet\Adapters\BitgoAdapter;
use LaravelZero\Framework\Commands\Command;

class CommandBase extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'start';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @param  int  $baseUnits
     * @return string
     */
    protected function baseUnitToCoin(int $baseUnits): string
    {
        return number_format($baseUnits / 100000000, 7);
    }

    /**
     * @param  float  $usd
     * @return string
     */
    protected function usdFormat(float $usd): string
    {
        return number_format($usd, 2);
    }

    /**
     * @param  float  $coinAmount
     * @return int
     */
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

    /**
     * @return AccessToken
     */
    public function authorize(): AccessToken
    {
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
