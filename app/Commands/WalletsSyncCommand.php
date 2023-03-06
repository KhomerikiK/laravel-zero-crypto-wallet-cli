<?php

namespace App\Commands;

use App\Commands\Traits\WalletCommendable;
use App\Wallet;
use Illuminate\Console\Scheduling\Schedule;
use Khomeriki\BitgoWallet\Facades\Wallet as BitgoWallet;
use LaravelZero\Framework\Commands\Command;

class WalletsSyncCommand extends Command
{
    use WalletCommendable;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'wallets:sync';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sync wallets from Bitgo 🔄';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $token = $this->authorize();
        $wallets = BitgoWallet::listAll();
        foreach ($wallets as $wallet) {
            if (! Wallet::where('bitgo_id', $wallet->id)->exists()) {
                $token->wallets()->save(new Wallet([
                    'crypto_currency' => $wallet->coin,
                    'bitgo_id' => $wallet->id,
                    'label' => $wallet->label,
                    'created_at' => $wallet->startDate,
                ]));
            }
            $this->info("✅ Wallet $wallet->label synchronised from Bitgo");
        }
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
