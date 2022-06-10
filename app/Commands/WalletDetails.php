<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Khomeriki\BitgoWallet\Facades\Wallet;
use LaravelZero\Framework\Commands\Command;

class WalletDetails extends Command
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
     * @return mixed
     */
    public function handle()
    {
        $wallets = DB::table('wallets')
            ->select('crypto_currency', 'bitgo_id', 'label', 'passphrase')
            ->get();

        $option = $this->menu('Choose wallet to generate address', $wallets->pluck('label')->toArray())
            ->setForegroundColour('green')
            ->setBackgroundColour('black')
            ->setWidth(200)
            ->setMargin(5)
            ->setExitButtonText("Abort")
            ->addStaticItem('AREA 2')
            ->open();
        $selected = $wallets->get($option);
        $wallet = null;
        $maxSpendableAmount = [];
        $this->task('📟Retrieving wallet data📡', function () use (&$wallet, &$maxSpendableAmount, $selected) {
            $wallet = Wallet::init($selected->crypto_currency, $selected->bitgo_id)->get();
            $maxSpendableAmount = $wallet->getMaximumSpendable();
        });

        $this->info("💳 Crypto currency: {$wallet->id}");
        $this->info("💳 Wallet id: {$wallet->id}");
        $this->info("🏷  Wallet address: {$wallet->receiveAddress['address']}");
        $this->info("🏦 Balance: ".$this->baseUnitToCoin((int)$wallet->balance));
        $this->info("✅ Confirmed Balance: ".$this->baseUnitToCoin((int)$wallet->confirmedBalance));
        $this->info("💶 Maximum spendable amount: ".$this->baseUnitToCoin((int)$maxSpendableAmount['maximumSpendable']));
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
