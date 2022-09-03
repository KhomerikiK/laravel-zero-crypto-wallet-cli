<?php

namespace App\Commands;

use App\AccessToken;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Cache;
use Khomeriki\BitgoWallet\Adapters\BitgoAdapter;

class StartCommandBase extends CommandBase
{
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $token = $this->secret('🔐 Enter your bitgo access token');
        config(['bitgo.api_key' => $token]);
        $bitgo = new BitgoAdapter();
        $res = $bitgo->me()->status();

        if ($res == 200) {
            $accessToken = AccessToken::where('token', $token)->first();
            if (! $accessToken) {
                AccessToken::create(['token' => $token]);
            }
            Cache::put('access_token', $token);
            $this->info('You have successfully logged in! ');
        } else {
            $this->warn('⚠️ Incorrect access token!');
            $this->call('start');
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
