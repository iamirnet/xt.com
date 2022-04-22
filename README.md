[![Latest Version](https://img.shields.io/github/release/iamirnet/xt.com.svg?style=flat-square)](https://github.com/iamirnet/xt.com/releases)
[![GitHub last commit](https://img.shields.io/github/last-commit/iamirnet/xt.com.svg?style=flat-square)](#)
[![Packagist Downloads](https://img.shields.io/packagist/dt/iamirnet/xtcom.svg?style=flat-square)](https://packagist.org/packages/iamirnet/xtcom)

# PHP XT API
This project is designed to help you make your own projects that interact with the [XT API](https://doc.xt.com/).

#### Installation
```
composer require iamirnet/xtcom
```
<details>
 <summary>Click for help with installation</summary>

## Install Composer
If the above step didn't work, install composer and try again.
#### Debian / Ubuntu
```
sudo apt-get install curl php-curl
curl -s http://getcomposer.org/installer | php
php composer.phar install
```
Composer not found? Use this command instead:
```
php composer.phar require "iamirnet/xtcom"
```

#### Installing on Windows
Download and install composer:
1. https://getcomposer.org/download/
2. Create a folder on your drive like C:\iAmirNet\XT
3. Run command prompt and type `cd C:\iAmirNet\XT`
4. ```composer require iamirnet/xtcom```
5. Once complete copy the vendor folder into your project.

</details>

#### Getting started
`composer require iamirnet/xtcom`
```php
require 'vendor/autoload.php';
// config by specifying api key and secret
$api = new \iAmirNet\XT\Client("<api key>","<secret>");
```


=======
#### Trading Market Configuration [more...](https://doc.xt.com/#quotesgetMarketConfig)
```php
//Call this before running any functions
print_r($api->marketConfig(/* optional */"btc_usdt"));
```

#### Kline/Candlestick Data [more...](https://doc.xt.com/#quotesgetKLine)
```php
//Call this before running any functions
print_r($api->kline("btc_usdt",/* Kline type is optional */ "1min",/* Since is optional */  0));
```

#### Aggregated Markets （Ticker) [more...](https://doc.xt.com/#quotesgetTicker)
```php
//Call this before running any functions
print_r($api->bookTicker("btc_usdt"));
```

#### Latest Ticker of all Markets [more...](https://doc.xt.com/#quotesgetTickers)
```php
//Call this before running any functions
print_r($api->bookPrices());
```

#### Market Depth Data [more...](https://doc.xt.com/#quotesgetDepth)
```php
//Call this before running any functions
print_r($api->depth("btc_usdt"));
```

#### Latest Market Transactions Record [more...](https://doc.xt.com/#quotesgetTrades)
```php
//Call this before running any functions
print_r($api->trades("btc_usdt"));
```

#### Get Server Time [more...](https://doc.xt.com/#tradegetServerTime)
```php
//Call this before running any functions
print_r($api->getServerTime());
```

#### Get Trading (Spot) Account Assets [more...](https://doc.xt.com/#tradegetBalance)
```php
//Call this before running any functions
print_r($api->balances());
```

#### Get the Account Type [more...](https://doc.xt.com/#tradegetAccounts)
```php
//Call this before running any functions
print_r($api->account());
```

#### Get Specific Account Assets [more...](https://doc.xt.com/#tradegetFunds)
```php
//Call this before running any functions
$accountId = 2;
print_r($api->specificAccount($accountId));
```

#### Place a New Order [more...](https://doc.xt.com/#tradeorder)
###### Buy
```php
//Call this before running any functions
$quantity = 1;
$price = 0.0005;
print_r($api->buy("btc_usdt", $quantity, $price, "LIMIT"));
```

###### Sell
```php
//Call this before running any functions
$quantity = 1;
$price = 0.0006;
print_r($api->sell("btc_usdt", $quantity, $price, "LIMIT"));
```

#### Bulk Orders [more...](https://doc.xt.com/#tradebatchOrder)
```php
//Call this before running any functions
$data = [
    [
        "price" => 10000.123,
        "amount" => 0.1,
        "type" => 1    // 1, buy, 0 sell
    ],
    [
        "price" => 10000.123,
        "amount" => 0.1,
        "type" => 0    // 1, buy, 0 sell
    ],
];
print_r($api->bulkOrders("btc_usdt", $data));
```

#### Cancel an Order [more...](https://doc.xt.com/#tradecancel)
```php
//Call this before running any functions
$orderId = 156387346384491;
print_r($api->cancel("btc_usdt", $orderId));
```

#### Cancel the Bulk Orders [more...](https://doc.xt.com/#tradebatchCancel)
```php
//Call this before running any functions
$data = [];
$data[] = 157154392122493;
$data[] = 157154392122494;
$data[] = 157154392122495;
$data[] = 157154392122496;
$data[] = 157154392122497;
print_r($api->bulkOrdersCancel("btc_usdt", $data));
```

#### Order Information [more...](https://doc.xt.com/#tradegetOrder)
```php
//Call this before running any functions
$orderId = 156387346384491;
print_r($api->orderInfo("btc_usdt", $orderId));
```

#### Get Uncompleted Orders [more...](https://doc.xt.com/#tradegetOpenOrders)
```php
$openorders = $api->openOrders("btc_usdt",/* page is optional */ 1,/* pageSize is optional */  10);
print_r($openorders);
```

#### Get a batch of Orders Information [more...](https://doc.xt.com/#tradegetBatchOrders)
```php
//Call this before running any functions
$data = [];
$data[] = 157154392122493;
$data[] = 157154392122494;
$data[] = 157154392122495;
$data[] = 157154392122496;
$data[] = 157154392122497;
print_r($api->bulkOrdersInfo("btc_usdt", $data));
```

#### Get Transaction Records [more...](https://doc.xt.com/#trademyTrades)
```php
$mytrades = $api->myTrades("btc_usdt",
        /* limit is optional */ 200,
        /* Start Time is optional */  1626428273000,
        /* End Time is optional */  1626428873020,
        /* From ID is optional */  6821734611983271937);
print_r($mytrades);
```

## Contribution
- Give us a star :star:
- Fork and Clone! Awesome
- Select existing [issues](https://github.com/iamirnet/xt.com/issues) or create a [new issue](https://github.com/iamirnet/xt.com/issues/new) and give us a PR with your bugfix or improvement after. We love it ❤️

## Donate
- USDT Or TRX: TUE8GiY4vmz831N65McwzZVbA9XEDaLinn 😘❤
