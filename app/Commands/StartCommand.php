<?php

namespace App\Commands;

use App\AccessToken;
use App\Commands\Traits\WalletCommendable;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Cache;
use Khomeriki\BitgoWallet\Adapters\BitgoAdapter;
use LaravelZero\Framework\Commands\Command;

class StartCommand extends Command
{
    use WalletCommendable;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'start';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Start CLI with login 🥳';

    /**
     * @return void
     */
    public function handle(): void
    {
        START:
        $token = $this->secret('🔐 Enter your bitgo access token');
        config(['bitgo.api_key' => $token]);
        $bitgo = new BitgoAdapter();
        $res = $bitgo->me()->status();

        if ($res == 200) {
            $accessToken = AccessToken::where('token', $token)->first();
            if (! $accessToken) {
                AccessToken::create(['token' => $token]);
            }
            Cache::put('access_token', $token, 600);
            $this->info('You have successfully logged in! ');
        } else {
            $this->warn('⚠️ Incorrect access token!');
            goto START;
        }
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
