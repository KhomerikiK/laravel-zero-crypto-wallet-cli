<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Khomeriki\BitgoWallet\Facades\Wallet;
use LaravelZero\Framework\Commands\Command;

class GenerateAddress extends CommandBase
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'wallet:address';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate address on wallet';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $token = $this->authorize();
        $coin = $this->availableCoins();

        $wallets = $token->wallets;

        $option = $this->choice(
            "Chose your $coin wallet:",
            $wallets->pluck('label')->toArray(),
            0
        );

        $label = $this->ask('✍️  Enter address label: (optional)', 'address-'.(string) now());

        $selectedWallet = $wallets->where('label', $option)->first();
        $wallet = null;
        $this->task('📟 generating address 📡', function () use (&$wallet, $selectedWallet, $label) {
            $wallet = Wallet::init($selectedWallet->crypto_currency, $selectedWallet->bitgo_id)->generateAddress($label);
        });

        $this->line("💳 wallet id: {$wallet->id}");
        $this->line("🏷  wallet address: {$wallet->address}");
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
