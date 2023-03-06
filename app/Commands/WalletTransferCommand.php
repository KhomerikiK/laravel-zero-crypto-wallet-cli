<?php

namespace App\Commands;

use App\Commands\Traits\WalletCommendable;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
use Khomeriki\BitgoWallet\Data\Requests\TransferData;
use Khomeriki\BitgoWallet\Data\Requests\TransferRecipientData;
use LaravelZero\Framework\Commands\Command;

class WalletTransferCommand extends Command
{
    use WalletCommendable;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'wallet:transfer';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Send transfer from wallet 💸';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = $this->authorize();
        $wallet = $this->selectWallet($token);
        $bitgoWallet = $wallet->bitgo();

        $address = $this->ask('🔗 Enter receive address');
        $amount = $this->ask("💸 Enter $bitgoWallet->coin amount");

        $passphrase = $this->secret('🔑 Wallet passphrase ');
        $baseAmount = $this->coinToBaseUnit($amount);

        $this->task("📟 📡 Sending $amount $bitgoWallet->coin to $address", function () use ($passphrase, $bitgoWallet, $baseAmount, $address) {
            $transferData = TransferData::fromArray([
                'walletPassphrase' => $passphrase,
                'recipients' => [
                    TransferRecipientData::fromArray([
                        'amount' => $baseAmount,
                        'address' => $address,
                    ]),
                ],
            ]);

            $result = $bitgoWallet->sendTransfer($transferData);
            if ($error = Arr::get($result, 'error')) {
                $this->notify('Sending transfer', $error);

                return false;
            }
            $this->notify('Sending transfer', 'Transfer sent successfully');

            return true;
        });
        $this->call('wallet:get', ['wallet' => $wallet->bitgo_id]);
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
