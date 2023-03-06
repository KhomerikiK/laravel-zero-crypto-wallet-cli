<?php

namespace App\Commands;

use App\Commands\Traits\WalletCommendable;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Cache;
use Khomeriki\BitgoWallet\Adapters\BitgoAdapter;
use LaravelZero\Framework\Commands\Command;

class BitgoExpressCommand extends Command
{
    use WalletCommendable;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'bitgo:express {--url=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Set bitgo express url 📡';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(BitgoAdapter $adapter): void
    {
        $url = $this->option('url');
        START:
        if (! $url) {
            $url = $this->ask('📡 Enter bitgo express url', 'http://localhost:3080');
        }

        try {
            config(['bitgo.express_api_url' => $url]);
            $ping = $adapter->pingExpress();
            if (! $ping->ok()) {
                goto START;
            }

            Cache::rememberForever('bitgo_express_url', function () use ($url) {
                return $url;
            });
            $this->info('Bitgo express api url set successfully');
        } catch (\Exception $exception) {
            $this->warn('Bitgo express api url is invalid,'.$exception->getMessage());
            $url = null;
            goto START;
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
