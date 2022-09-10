<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
use Khomeriki\BitgoWallet\Facades\Wallet;
use LaravelZero\Framework\Commands\Command;

class WalletDetails extends CommandBase
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'wallet:get {--wallet=}';

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
    public function handle()
    {
        $token = $this->authorize();
        $selectedWallet = $this->selectWallet($token, $this->option('wallet'));

        if ($selectedWallet) {
            $wallet = null;
            $maxSpendableAmount = [];
            $this->task('📟 Retrieving wallet data📡', function () use (&$wallet, &$maxSpendableAmount, $selectedWallet) {
                $wallet = Wallet::init($selectedWallet->crypto_currency, $selectedWallet->bitgo_id)->get();
                $maxSpendableAmount = $wallet->getMaximumSpendable();
            });

            if (is_null($wallet)) {
                $this->call('wallet:get');
            }

            $this->renderWalletInfo($wallet, $maxSpendableAmount);

            $transfers = $wallet->getTransfers(['limit' => 500]);
            $this->renderTransaction($transfers);
        } else {
            $this->warn('⚠️ Wallet not found !');
        }
    }

    /**
     * @param $wallet
     * @param $maxSpendableAmount
     * @return void
     */
    private function renderWalletInfo($wallet, $maxSpendableAmount): void
    {
        $this->info("💳 Crypto currency: {$wallet->coin}");
        $this->info("💳 Wallet id: {$wallet->id}");
        $this->info("🏷  Wallet address: {$wallet->receiveAddress['address']}");
        $this->info('🏦 Balance: '.$this->baseUnitToCoin((int) $wallet->balance));
        $this->info('✅ Confirmed Balance: '.$this->baseUnitToCoin((int) $wallet->confirmedBalance));
        $this->info('💶 Maximum spendable amount: '.$this->baseUnitToCoin((int) $maxSpendableAmount['maximumSpendable']));
    }

    /**
     * @param $transfers
     * @return void
     */
    private function renderTransaction($transfers): void
    {
        $transfers = array_map(function ($transfer) {
            $transfer = Arr::only((array) $transfer, ['coin', 'value', 'type', 'usd', 'state', 'feeString']);
            $transfer['value'] = $this->baseUnitToCoin($transfer['value']);
            $transfer['usd'] = $this->usdFormat($transfer['usd']);
            $transfer['feeString'] = $this->baseUnitToCoin($transfer['feeString']);

            return $transfer;
        }, $transfers);

        $this->newLine();
        $this->line('Wallet transfers');
        $this->table(
            ['coin', 'type', 'value', 'feeString', 'usd', 'state'],
            $transfers,
        );
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
