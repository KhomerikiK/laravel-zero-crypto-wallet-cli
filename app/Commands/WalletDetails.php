<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Khomeriki\BitgoWallet\Facades\Wallet;
use LaravelZero\Framework\Commands\Command;

class WalletDetails extends CommandBase
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'wallet:get';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $token = $this->authorize();
        $wallets = $token->wallets()
            ->select('crypto_currency', 'bitgo_id', 'label', 'passphrase')
            ->get();

        $option = $this->choice(
            'Chose your wallet:',
            $wallets->pluck('label')->toArray(),
            0
        );

        $selectedWallet = $wallets->where('label', $option)->first();
        $wallet = null;
        $maxSpendableAmount = [];
        $this->task('📟Retrieving wallet data📡', function () use (&$wallet, &$maxSpendableAmount, $selectedWallet) {
            $wallet = Wallet::init($selectedWallet->crypto_currency, $selectedWallet->bitgo_id)->get();
            $maxSpendableAmount = $wallet->getMaximumSpendable();
        });

        if (is_null($wallet)) {
            $this->call('wallet:get');
        }

        $this->info("💳 Crypto currency: {$wallet->coin}");
        $this->info("💳 Wallet id: {$wallet->id}");
        $this->info("🏷  Wallet address: {$wallet->receiveAddress['address']}");
        $this->info('🏦 Balance: '.$this->baseUnitToCoin((int) $wallet->balance));
        $this->info('✅ Confirmed Balance: '.$this->baseUnitToCoin((int) $wallet->confirmedBalance));
        $this->info('💶 Maximum spendable amount: '.$this->baseUnitToCoin((int) $maxSpendableAmount['maximumSpendable']));
    }

    public function baseUnitToCoin($baseUnits)
    {
        return number_format($baseUnits / 100000000, 7);
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
