<?php

namespace App\Commands;

use App\AccessToken;
use Illuminate\Support\Facades\Cache;
use Khomeriki\BitgoWallet\Adapters\BitgoAdapter;
use LaravelZero\Framework\Commands\Command;

class CommandBase extends Command
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

    public function availableCoins()
    {
        return $this->choice(
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
    }

    public function menuScreen(string $title)
    {
        return $this->ask($title)
            ->setForegroundColour('green')
            ->setBackgroundColour('black')
            ->setWidth(200)
            ->setMargin(5)
            ->setExitButtonText('Abort')
            ->addStaticItem('AREA 2')
            ->open();
    }

    public function authorize(): AccessToken
    {
        $token = Cache::get('access_token', '');

        $token = AccessToken::where('token', $token)->first();

        config(['bitgo.api_key' => $token->token]);

        $bitgo = new BitgoAdapter();
        $res = $bitgo->me()->status();

        if ($res != 200) {
            $this->call('start');
        }

        return $token;
    }
}
