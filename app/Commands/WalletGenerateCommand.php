<?php

namespace App\Commands;

use App\Commands\Traits\WalletCommendable;
use Illuminate\Console\Scheduling\Schedule;
use Khomeriki\BitgoWallet\Facades\Wallet;
use LaravelZero\Framework\Commands\Command;

class WalletGenerateCommand extends Command
{
    use WalletCommendable;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'wallet:generate';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate wallet 📡';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $token = $this->authorize();

        $coin = $this->availableCoins();

        $label = $this->ask('✍️ Wallet label ');
        $pass = $this->secret('🔑 Wallet passphrase ');

        $wallet = null;

        $this->task('📟 Generating wallet 📡', function () use (&$wallet, $label, $pass, $coin, $token) {
            $wallet = Wallet::init($coin)->generate($label, $pass);
            $token->wallets()->create([
                'crypto_currency' => $coin,
                'bitgo_id' => $wallet->id,
                'label' => $label,
                'passphrase' => $pass,
            ]);
        });
        $this->newLine();

        $this->notify('Generating wallet', 'wallet generated successfully');
        $this->line("💳 Wallet id: {$wallet->id}");
        $this->line("🏷  Wallet address: {$wallet->receiveAddress['address']}");

        $this->call('wallet:list');
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
