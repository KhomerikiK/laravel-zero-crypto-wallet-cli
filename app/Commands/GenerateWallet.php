<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Khomeriki\BitgoWallet\Facades\Wallet;
use LaravelZero\Framework\Commands\Command;

class GenerateWallet extends Command
{
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
    protected $description = 'Generate crypto wallet';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $coin = $this->choice(
            'chose crypto currency',
            [
                'tbtc',
                'tltc',
                'tzec',
                'tdash',
                'tbch',
            ],
            0
        );
        $label = $this->ask('✍️  Enter wallet label: ');
        $pass = $this->secret('🔑 Enter wallet passphrase: ');

        $wallet = null;
        $this->task('📟 generating wallet 📡', function () use (&$wallet, $label, $pass, $coin) {
            $wallet = Wallet::init($coin)->generate($label, $pass);
            DB::table('wallets')->insert([
                'crypto_currency' => $coin,
                'bitgo_id' => $wallet->id,
                'label' => $label,
                'passphrase' => $pass,
            ]);
        });
        $this->newLine();

        $this->line("💳 wallet id: {$wallet->id}");
        $this->line("🏷  wallet address: {$wallet->receiveAddress['address']}");
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
