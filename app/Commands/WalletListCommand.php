<?php

namespace App\Commands;

use App\Commands\Traits\WalletCommendable;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class WalletListCommand extends Command
{
    use WalletCommendable;

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
    protected $description = 'List generated wallets 💻';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $token = $this->authorize();
        $wallets = $token->wallets()
            ->select('id', 'crypto_currency', 'label', 'bitgo_id')
            ->orderBy('id', 'desc')
            ->get()->map(function ($wallet) {
                return $wallet->toArray();
            })->toArray();

        $this->table(
            ['id', 'crypto_currency', 'label', 'bitgo_id'],
            $wallets
        );
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
