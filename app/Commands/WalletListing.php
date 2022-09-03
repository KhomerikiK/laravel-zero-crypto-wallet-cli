<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class WalletListing extends CommandBase
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'wallet:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all the wallets';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $token = $this->authorize();
        $wallets = $token->wallets()
            ->select('id', 'crypto_currency', 'label', 'bitgo_id', 'passphrase')
            ->orderBy('id', 'desc')
            ->get()->map(function ($wallet) {
                return $wallet->toArray();
            })->toArray();

        $this->table(
            ['id', 'crypto_currency', 'label', 'bitgo_id', 'passphrase'],
            $wallets
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
