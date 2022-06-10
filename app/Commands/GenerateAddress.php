<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Khomeriki\BitgoWallet\Facades\Wallet;
use LaravelZero\Framework\Commands\Command;

class GenerateAddress extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'address:generate';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate address on wallet';

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

        $label = $this->ask('✍️  Enter address label: (optional)', "address-".(string)now());
        $selected = $wallets->get($option);
        $wallet = null;
        $this->task('📟 generating address 📡', function () use (&$wallet, $selected, $label) {
            $wallet = Wallet::init($selected->crypto_currency, $selected->bitgo_id)->generateAddress($label);
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
