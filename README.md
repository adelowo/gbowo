# Gbowo - Unified API for Hippy Nigerian Payment Gateways

[![Latest Version on Packagist](https://img.shields.io/packagist/v/adelowo/gbowo.svg?style=flat-square)](https://packagist.org/packages/adelowo/gbowo)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/adelowo/gbowo/master.svg?style=flat-square)](https://travis-ci.org/adelowo/gbowo)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/adelowo/gbowo.svg?maxAge=2592000&style=flat-square)](https://scrutinizer-ci.com/g/adelowo/gbowo/?branch=master)
[![Quality Score](https://img.shields.io/scrutinizer/g/adelowo/gbowo.svg?style=flat-square)](https://scrutinizer-ci.com/g/adelowo/gbowo)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/bade68de-1511-4d67-85dc-09f6e23c5ea0.svg?style=flat-square)](https://insight.sensiolabs.com/projects/09ff34ee-feb3-49c0-acbc-52781179deb9)
[![Total Downloads](https://img.shields.io/packagist/dt/adelowo/gbowo.svg?style=flat-square)](https://packagist.org/packages/adelowo/gbowo)

> WIP

### Installation

Install Gbowo via one of the following methods :

- [Composer](https://getcomposer.org) (Recommended) :

```bash
    composer require "adelowo/gbowo" : "~1.0"
```

- Repo Cloning :

```bash
    git clone https://github.com/adelowo/gbowo.git
```

- [Download a release](https://github.com/adelowo/gbowo/releases)

> If downloading the library without composer or cloning directly from the repository, you'd have to write an autoloader yourself. My bad

### Usage

```php

require_once 'vendor/autoload.php';

$adapter = new \Gbowo\Adapter\Paystack\PaystackAdapter();

$gbowo = new Gbowo($adapter);

$response = $gbowo->charge(); //depending on the adapter in use, the response would vary.

```

For the paystack and amplifypay adapters, the response received would be a string, which denotes an _authorization__url_. You are to make a redirect to the url to complete the transaction.

> Other adapters implementation may do something much different like redirect internally ( from the adapter) but this isn't done for good reasons. This is because different systems may have different ways of performing redirects via _Request_ or _Response_ objects, or whatever have they. So as long as you can get the returned url, you can use the _Adapter_ in your framework.

A basic example would be something like

```php

header("Location : {$response}");
exit();

```

### Adapters

_Gbowo_ ships with two adapters : one for [paystack](https://paystack.co) and the other for [Amplifypay](https://amplifypay.com).

While both payment gateway offer similar features, there are a few subtle differences (obviously).

_Gbowo_ requires some value to be present in the environment i.e `$_ENV`. For paystack, this is `$_ENV['PAYSTACK_SECRET_KEY']` while the amplifypay adapter requires two values : your merchant id and an api key. This should be present in the following format : `$_ENV['AMPLIFYPAY_MERCHANT_ID']` and `$_ENV['AMPLIFYPAY_API_KEY']`.

> You are strongly advised to keep your keys / tokens out of your code and instead load them into `$_ENV` by some other means. We don't enforce this but it is a best practice and even made it to the [12 Factor App Guideline](http://updatethis).
A library that would help with this is `vlucas/phpdotenv`. All it needs is a `.env` file and you are golden. Remember not to commit the `.env` else it still isn't out of your "code".
A sample `.env.example` has been provided in the `resources` directory. You can copy and rename that to `.env` in your root dir.

```php

$paystack = new \Gbowo\Adapter\Paystack\PaystackAdapter();

$amplifyPay = new \Gbowo\Adapter\Amplifypay\AmplfiypayAdapter()

```
A GuzzleHttp `Client` instance would be created automatically and values gotten from the `$_ENV` would be used to set the appropriate authorization headers where applicable.

You can prevent this "auto-wiring" by providing an instance of Guzzlehttp Client in the constructor.

```php

$client = new \GuzzleHttp\Client(['key' => "value"]);

$amplifyPay = new \Gbowo\Adapter\Amplifypay\AmplfiypayAdapter($client)

```

The payment flow for both adapters is pretty much the same. User initiates first time / one time transaction and is redirected to a secure page where payment details are to be inputted. After this (a successful payment request), the gateway would issue a redirect to a url you have supplied them as a callback. In this url, you should fetch the details of a user (who is now a customer) such as an *authorization_code* , *transaction_reference* among others. This is for recurrent transactions and should be persisted to a storage mechanism.

Initiating the transaction should be performed by calling the `charge` method on the adapter. And making a redirect to the response as described earlier.

To fetch the data from the url callback the gateway, you have to call the `getPaymentData` method on the adapter. A response containing all data about the customer.

```php

//paystack adapter
var_dump($adapter->getPaymentData($_GET['trxref'])) ; //you should clean this up.

//amplifypay adapter
var_dump($adapter->getPaymentData($_GET['tran_response'])); // clean up

```

### Adapters Methods.

Paystack :

* `getCustomer(int $id)`
* `getAllCustomers()`
* `chargeWithToken($token)`
* `getPaymentData(string $transRef)`

Amplifypay :

* `unsubcribeCustomerFromPlan(array $data)`
* `chargeWithToken(array $token)` //a token in amplifypay is a key pair of values.
* `getPaymentData(string $transRef)`


### Extending Adapters via Plugins

Different gateways implement various features and there's no way we can support all of them without losing our sanity.

Supporting all features would lead to a bloat (an interface, class bloat). Take for instance : create _InterfaceX_ to support feature X for _AdapterE_ while _AdapterE_ still makes use of features (and therefore interfaces) for _AdapterA_,_AdapterQ_ and so on. Now imagine this sceanrio plays out for 4 -5 adapters. Apart from the bloat, we cannot create a diagram of which interfaces are being used and it'd lead to a situation where we cannot remove a certain class or interface because we do not know who (what adapter) depends on them.

To prevent this, _Gbowo_ implements a plugin architecture that eases extension or "adding new methods/behaviours" to an existing adapter without sub-classing or editing core code. To achieve this, there is a `Pluggable` trait that contains the logic and **MUST** be imported by an adapter implementation.

A look at the [paystack adapter](src/Gbowo/Adapter/Paystack/PaystackAdapter.php) and [amplifypay](src/Gbowo/Adapter/Amplifypay/AmplifypayAdapter.php) would reveal that they do not have the methods described above in their public api. In fact they expose only 3 methods :
* `__construct(Client $client = null)` //if it counts as one
* `getHttpClient()`
* `charge(array $data = null)` //This is gotten from the _Adapter_ Interface implemented.

But a look at their `registerPlugins` method tells how the methods described in the `Adapters method` section above come about.

A plugin is a plain PHP class that **MUST** implement the `Plugin` interface. This interface exposes two methods :
* `getPluginAccessor() : string`
* `setAdapter(Adapter $adapter)`

```php

namespace Vendor/AdapterName/Plugin;

use Gbowo/Contract/Plugin/Plugin;

class ApiPinger implements Plugin
{

    public function getPluginAccessor():string
    {
        return "pingApi"; //Oops.. Let's confirm if the api isn't dead before making any request. And it must be a string without parenthesis
    }

    /**
     * You can also leave this method out but you must extend the `AbstractPlugin` class. Doing so, means you'd have to get rid of the plugin interface here as the abstract plugin already does that.
     */
    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter ; //useful for helpers like getting the already configured Client object
        return $this;
    }
}

```

The `getPluginAccessor` is of tremendous interest here since it determines what plugin the method call should be deferred to. This is done by the magic method `__call` in the [`Pluggable`](src/Gbowo/Traits/Pluggable.php) trait.

In addition to this, a plugin must be defined as handleable. As of now, our Pinger plugin does nothing when `$adapter->pingApi()` is called. Heck, an exception would even be thrown complaining about the plugin being un-handleable.

 To fix this and make our plugin do some real work, an `handle` method **MUST** be defined with it own's arguments.

> We do not enforce the handle method via an interface as some plugins might need just one argument, two args, or even 4 ( 2 `string`s, one `array`, one `int`).

```php

//Class ApiPinger

/**
 * Ping the gateway Api
 * @param  bool $throw Should an exception be thrown if the api is down.
 * @return int 1 - if the api is up and running.
               0 - if the api is down and $throw is set to false.
 * @throws \Exception if the api is down and $throw is set to false.
 */
public function handle(bool $throw = true)
{
    $response = $this->adapter->getHttpClient()->get("https://api.homepage.com");

    if ($response->getStatusCode() === 200) {
        return 1;
    }

    if ($throw) {
        throw new \Exception("API is dead", $response->getStatusCode());
    }

    return 0;
}

```
Remember to register the plugin like below :

```php
$adapter->addPlugin(new Vendor/AdapterName/Plugin/ApiPinger(https://api.somesite.com));

```
Not all plugins would make it to the core eventually and not even all plugins in the core would be "added" on instantiation. For instance, the `GetAllCustomers` plugin isn't added to the _PaystackAdapter_ internally. To use the plugin, you'd have to add it yourself.

```php

$adapter->addPlugin(new GetAllCustomers(PaystackAdapter::API_LINK))

```

### Contributing

Awesome, I'd love that. Fork, send PR. But hey, unit testing is one honking great idea. Let's have more of that.

### Bug Reports, Issue tracking and Security Vulnerabilities

Please make use of the [issue tracker](https://github.com/adelowo/gbowo/issues) for bug reports, feature request and others except Security issues. If you do discover a vulnerability, please send a mail to me@adelowolanre.com.

### License
[MIT](http://opensource.org/licenses/MIT)
