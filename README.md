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

##usage
### Generate wallet
command:
```bash
php cwc wallet:generate
```
output:
```bash
 chose crypto currency [tbtc]:
  [0] tbtc
  [1] tltc
  [2] tzec
  [3] tdash
  [4] tbch
 > tbtc

 ✍️  Enter wallet label: :
 > testnet btc wallet

 🔑 Enter wallet passphrase: :
 >

📟 generating wallet 📡: ✔

💳 wallet id: 62a31fdc8544ec000801b2229f9688c8
🏷 wallet address: 2N6CKrt1zdVj298Q1upRVaN3GN1p4ft3Pjt
```


## List wallets
command:
```bash
php cwc wallet:list
```
output:
```bash
+----+-----------------+--------------------+----------------------------------+------------+
| id | crypto_currency | label              | bitgo_id                         | passphrase |
+----+-----------------+--------------------+----------------------------------+------------+
| 6  | tbtc            | testnet btc wallet | 62a31fdc8544ec000801b2229f9688c8 | testing    |
| 5  | tzec            | testing zec wallet | 62a31f0e91010900083811f99d467008 | testing    |
| 4  | tzec            | testing zec wallet | 62a31edc0556980007c9424e9ab9d112 | testing    |
| 3  | tbch            | testing bch wallet | 62a31ec08544ec000801518d37437c52 | testing    |
| 2  | tltc            | testing ltc wallet | 62a31db69101090008379e380b4e9783 | testing    |
| 1  | tbtc            | testing btc wallet | 62a31ac038c0180007e8506ab3d6fc3c | testing    |
+----+-----------------+--------------------+----------------------------------+------------+
```

## Generate address
command:
```bash
php cwc address:generate
```
output:
```bash

       Choose wallet to generate address
       --------------------------------------------------------------------------------------------------------------------------

       ● testing btc wallet
       ○ testing ltc wallet
       ○ testing bch wallet
       ○ testing zec wallet
       ○ testing zec wallet
       ○ testnet btc wallet
       AREA 2
       ○ Abort


        ✍️  Enter address label: (optional) [address-2022-06-10 10:50:34]:
        > testing address
        
        📟 generating address 📡: ✔
        💳 wallet id: 62a31db69101090008379e380b4e9783
        🏷 wallet address: Qjc8PXkeTcGRYNTrZNA5QUZcr7XRiHHnkY
```

## Get wallet
command:
```bash
 php cwc wallet:get
```
output:
```bash

       Choose wallet to generate address
       --------------------------------------------------------------------------------------------------------------------------

       ○ testing btc wallet
       ● testing ltc wallet
       ○ testing bch wallet
       ○ testing zec wallet
       ○ testing zec wallet
       ○ testnet btc wallet
       AREA 2
       ○ Abort

        💳 Crypto currency: 62a31ac038c0180007e8506ab3d6fc3c
        💳 Wallet id: 62a31ac038c0180007e8506ab3d6fc3c
        🏷 Wallet address: 2NDeu2jRgR3zi3Hjs8cgweRuoHfc5j4L2tF
        🏦 Balance: 0.0000168
        ✅ Confirmed Balance: 0.0000168
        💶 Maximum spendable amount: 0.0000000
```
