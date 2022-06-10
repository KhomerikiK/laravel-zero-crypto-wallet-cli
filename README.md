<p align="center">
    <img title="Laravel Zero" height="100" src="https://raw.githubusercontent.com/laravel-zero/docs/master/images/logo/laravel-zero-readme.png" />
</p>

##  CLI tool for Bitgo crypto wallet
- used package for bitgo integration: https://github.com/KhomerikiK/laravel-bitgo-wallet
##requirements
- php 8

##setup
```bash
composer install

cp .env.example .env
```

###usage
```bash
➜  crypto-wallet-cli git:(master) ✗ php crypto-wallet-cli wallet:generate

 chose crypto currency [tbtc]:
  [0] tbtc
  [1] tltc
  [2] tzec
  [3] tdash
  [4] tbch
 > tbtc

 ✍️  Enter wallet label: :
 > bitcoin testnet wallet

 🔑 Enter wallet passphrase: :
 >

📡 generating wallet... 📡
 1/1 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%
📟 wallet has generated 💎

💳 wallet id: 62a29e5721b8ef0007d58945cee69c00
🏷  wallet address: 62a29e5721b8ef0007d58945cee69c00
➜  crypto-wallet-cli git:(master) ✗
```
