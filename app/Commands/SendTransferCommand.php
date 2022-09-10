<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
use Khomeriki\BitgoWallet\Data\Requests\TransferData;
use Khomeriki\BitgoWallet\Data\Requests\TransferRecipientData;
use LaravelZero\Framework\Commands\Command;

class SendTransferCommand extends CommandBase
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'transfer:send';

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
    public function handle()
    {
        $token = $this->authorize();
        $wallet = $this->selectWallet($token);
        $bitgoWallet = $wallet->bitgo();

        $address = $this->ask('🔗 Enter receive address');
        $amount = $this->ask("💸 Enter $bitgoWallet->coin amount");

        $baseAmount = $this->coinToBaseUnit($amount);

        $this->task("📟 📡 Sending $amount $bitgoWallet->coin to $address", function () use ($bitgoWallet, $wallet, $baseAmount, $address) {
            $transferData = TransferData::fromArray([
                'walletPassphrase' => $wallet->passphrase,
                'recipients' => [
                    TransferRecipientData::fromArray([
                        'amount' => $baseAmount,
                        'address' => $address,
                    ]),
                ],
            ]);

            $result = $bitgoWallet->sendTransfer($transferData);

            return ! Arr::has($result, 'error');
        });
        $this->call('wallet:get', ['wallet' => $wallet->bitgo_id]);
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
