# Gbowo - Unified API for Hippy Nigerian Payment Gateways

[![Latest Version on Packagist](https://img.shields.io/packagist/v/adelowo/gbowo.svg?style=flat-square)](https://packagist.org/packages/adelowo/gbowo)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/adelowo/gbowo/master.svg?style=flat-square)](https://travis-ci.org/adelowo/gbowo)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/adelowo/gbowo.svg?style=flat-square)](https://scrutinizer-ci.com/g/adelowo/gbowo/?branch=master)
[![Quality Score](https://img.shields.io/scrutinizer/g/adelowo/gbowo.svg?style=flat-square)](https://scrutinizer-ci.com/g/adelowo/gbowo)
[![Total Downloads](https://img.shields.io/packagist/dt/adelowo/gbowo.svg?style=flat-square)](https://packagist.org/packages/adelowo/gbowo)

- [Installation](#installation)
- [Usage](#usage)
    - [Adapters](#adapters)
        - [Custom Adapters](#extend)
- [Plugins](#plugins)
- [Framework Integration/Bridges](#frameworks)
    - [Laravel](#laravel)
- [Example Application](#example)

<h2 id="installation">Installation
</h2>

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

<h2 id="usage"> Usage </h2>

```php

require_once 'vendor/autoload.php';

$adapter = new \Gbowo\Adapter\Paystack\PaystackAdapter();

$response = $adapter->charge(); 

```

For the paystack and amplifypay adapters, the response received would be a string, which denotes an _authorization__url_. You are to make a redirect to the url to complete the transaction.

> Other adapters implementation may do something much different like redirect internally ( from the adapter) but this isn't done for good reasons. This is because different systems may have different ways of performing redirects via _Request_ or _Response_ objects, or whatever have they. So as long as you can get the returned url, you can use the _Adapter_ in your framework or whatever have you.

A basic example would be something like

```php

header("Location : {$response}");
exit();

```

<h2 id="adapters">Adapters</h2>

#### Quick usage
```php
$adapter = (new \Gbowo\GbowoFactory())->createAdapter("paystack"); //or "amplifypay"

return $adapter->charge();
```

#### Usage in depth

_Gbowo_ ships with two adapters : one for [paystack](https://paystack.co) and the other for [Amplifypay](https://amplifypay.com).

While both payment gateway offer similar features, there are a few subtle differences (obviously).

_Gbowo_ requires some value to be present in the environment i.e `$_ENV`. For paystack, this is `$_ENV['PAYSTACK_SECRET_KEY']` while the amplifypay adapter requires two values : your merchant id and an api key. This should be present in the following format : `$_ENV['AMPLIFYPAY_MERCHANT_ID']` and `$_ENV['AMPLIFYPAY_API_KEY']`.

> You are strongly advised to keep your keys and/or tokens out of your code and instead load them into `$_ENV` by some other means. We don't enforce this but it is a best practice, [12 Factor App Guideline](https://12factor.net/config).
A library that would help with this is `vlucas/phpdotenv`. All it needs is a `.env` file and you are golden. Remember not to commit the `.env` else it still isn't out of your "code".
A sample `.env.example` has been provided in the `resources` directory. You can copy and rename that to `.env` in your root dir.

```php

$paystack = new \Gbowo\Adapter\Paystack\PaystackAdapter();

$amplifyPay = new \Gbowo\Adapter\Amplifypay\AmplifypayAdapter()

```

A GuzzleHttp `Client` instance would be created automatically and values gotten from the `$_ENV` would be used to set the appropriate authorization headers where applicable.

You can prevent this "auto-wiring" by providing an instance of Guzzlehttp Client in the constructor.

```php

$client = new \GuzzleHttp\Client(['key' => "value"]);

$amplifyPay = new \Gbowo\Adapter\Amplifypay\AmplifypayAdapter($client)

```

The payment flow for both adapters is pretty much the same. User initiates first time / one time transaction and is redirected to a secure page where payment details are to be inputted. After this (a successful payment request), the gateway would issue a redirect to a url you have supplied them as a callback. In this url, you should fetch the details of a user (who is now a customer) such as an *authorization_code* , *transaction_reference* among others. This is for recurrent transactions and should be persisted to a storage mechanism.

Initiating the transaction should be performed by calling the `charge` method on the adapter. And making a redirect where applicable to the response as described earlier.

To fetch the data from the url callback you have registered on your chosen gateway, you have to call the `getPaymentData` method on the adapter. It's response would contain some data about the customer.

```php

//paystack adapter
var_dump($adapter->getPaymentData($_GET['trxref'])) ; //you should clean this up.

//amplifypay adapter
var_dump($adapter->getPaymentData($_GET['tran_response'])); // clean up

```

> The `getPaymentData` method can also be called on some transaction reference you have stored in your db or some other form of storage mechanism.

> When you call the `getPaymentData` on the paystack adapter. The reference is verified by paystack before a response is sent

### Adapters Methods.

[Paystack](https://paystack.co) :

* `charge(array $data = [])`
* `getCustomer(int $id)`
* `getAllCustomers()`
* `chargeWithToken(array $userToken)` // a token plus email address (or custom stuff)
* `getPaymentData(string $transRef)`
* `fetchPlan($planIdentifier)`
* `fetchAllPlans()`

> Checkout [gbowo-paystack](https://github.com/adelowo/gbowo-paystack) . It contains an additional set of [plugins](#plugins) for the paystack adapter.

[Amplifypay](https://amplifypay.com) :

* `charge(array $data = [])`
* `unsubcribeCustomerFromPlan(array $data)`
* `chargeWithToken(array $userToken)` //a token in amplifypay is a key pair of values.
* `getPaymentData(string $transRef)`
* `fetchPlan($planIdentifier)`
* `fetchAllPlans()`

> The `charge` method parameter (`array $data = []`) should contain stuffs like amount, email, x, y, z). Those would be handed over to the payment gateway

<h2 id="extend">Custom Adapters</h2>

> Using laravel, please check out how to [add your custom adapter](#laravel)

```php

//let's assume it is an enterprisey app

$interswitch = new class implements \Gbowo\Contract\Adapter\AdapterInterface
{
    protected $interswitch;

    public function __construct()
    {
        $this->interswitch = new \stdClass(new \ArrayObject(new \stdClass())); // It wasn't me
    }

    public function charge(array $data = [])
    {
        return "charged by interswitch";
    }
};

$adapter = new \Gbowo\GbowoFactory(["interswitch" => $interswitch]); //add the interswith adapter as a custom one.

$interswitchAdapter = $adapter->createAdapter("interswitch");

$interswitchAdapter->charge(['a' => 'b', 'c' => 'd']);
```

<h2 id="plugins">Extending Adapters via Plugins</h2>

Different gateways implement various features and there's no way we can support all of them without losing our sanity.

Supporting all features would lead to a bloat (an interface, class bloat). Take for instance : create `InterfaceX` to support feature X for `AdapterE` while `AdapterE` still makes use of features (and therefore interfaces) for `AdapterA`,`AdapterQ` and so on. Now imagine this scenario plays out for 4 -5 adapters.
 
 Apart from the bloat, we cannot create a (visual) diagram of which interfaces are being used and it'd lead to a situation where we cannot remove a certain class or interface because we do not know who (what adapter) depends on them.

To prevent this, _Gbowo_ implements a plugin architecture that eases extension or "adding new methods/behaviours" to an existing adapter without inheritance or touching core code. To achieve this, there is a `Pluggable` trait that contains the logic and **MUST** be imported by an adapter implementation.

A look at the [paystack adapter](src/Gbowo/Adapter/Paystack/PaystackAdapter.php) and [amplifypay](src/Gbowo/Adapter/Amplifypay/AmplifypayAdapter.php) would reveal that they do not have the methods described above in their public api. In fact they expose only 3 methods :
* `__construct(Client $client = null)` // if it counts as one
* `getHttpClient()`
* `charge(array $data = [])` //This is gotten from the `AdapterInterface` implemented.

But a look at their `registerPlugins` method  - which is gotten from the __Pluggable__ trait - tells how the methods described in the `Adapters method` section above come about.

A plugin is a plain PHP class that **MUST** implement the `PluginInterface`. This interface exposes two methods :

* `getPluginAccessor() : string`
* `setAdapter(Adapter $adapter)`
* `handle(string $reference)` //Typehint as much as you want. 2,3 args ? Your call.

> I have written a [detailed post about this here](https://lanreadelowo.com/blog/2017/03/08/extending-gbowo-via-plugins/)

```php

namespace Vendor\AdapterName\Plugin;

use Gbowo/Contract/Plugin/PluginInterface;
use Gbowo\Exception\TransactionVerficationFailedException;

class ApiPinger implements PluginInterface
{

    public function getPluginAccessor():string
    {
        return "pingApi"; //Oops.. Let's confirm if the api isn't dead before making any request. And it must be a string without parenthesis
    }

    /**
     * You can also leave this method out but you must extend the `AbstractPlugin` class. Doing so, means you'd have to get rid of the plugin interface here as the abstract plugin already does that.
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        //useful for helpers like getting stuffs from "accessors" on the adapter instance like the already configured HttpClient object
        $this->adapter = $adapter ; 
        return $this;
    }

    /**
     * Ping the gateway Api
     * @param  bool $shouldThrow. Should an exception be thrown if the api is down ?.
     * @return bool true - if the api is up and running.
                   false - if the api is down and $throw is set to false.
     * @throws \Exception if the api is down and $throw is set to false.
     */
    public function handle(bool $shouldThrow = false)
    {
        $response = $this->adapter->getHttpClient()->get("https://api.homepage.com");
    
        if ($response->getStatusCode() != 200 ) {
            return true;
        }
    
        if ($shouldThrow) {
            throw TransactionVerficationFailedException::createFromResponse($response);
        }
    
        return false;
    }
}

```

> `createFromResponse` is a wrapper that allows client code inspect the response for why there was a failure (say an invalid HTTP status code).. You should call `getResponse` on the exception in other to inspect it. This is also true for official plugins provided by Gbowo.

The `getPluginAccessor` is of tremendous interest here since it determines what plugin the method call should be deferred to. This is done by the magic method `__call` in the [`Pluggable`](src/Gbowo/Traits/Pluggable.php) trait.


```php
$adapter->addPlugin(new Vendor\AdapterName\Plugin\ApiPinger(PaystackAdapter::API_LINK));
//Usage like this

$adapter->pingApi(true);
$adapter->pingApi();

```
Not all plugins would make it to the core eventually and not even all plugins in the core would be "added" on instantiation. For instance, the `GetAllCustomers` plugin isn't added to the `PaystackAdapter` internally. To use the plugin, you'd have to add it yourself.

```php

$adapter->addPlugin(new GetAllCustomers(PaystackAdapter::API_LINK))

```

<h2 id="frameworks"> Framework Integration</h2>

<h2 id="laravel">Laravel </h2>

Prior to `v1.5.0`, a Laravel bridge was provided alongside this package but in light of best  interests, it [has been moved to it's own repository](https://github.com/adelowo/laravel-gbowo).

If upgrading from a previous version to `v1.5.0`, please note that you would have to run `composer require adelowo/laravel-gbowo`

> Please note that the decision to give the Laravel bridge a life of it's own doesn't mean BC break. The namespaces were preserved and would continue to work as is.

<h2 id="example"> </h2>

##### Sample App

[Gbowo-app](https://github.com/adelowo/gbowo-app) - Built with SlimPHP.


### Contributing

Awesome, I'd love that. Fork, send PR. But hey, unit testing is one honking great idea. Let's have more of that.

### Bug Reports, Issue tracking and Security Vulnerabilities

Please make use of the [issue tracker](https://github.com/adelowo/gbowo/issues) for bug reports, feature request and others except Security issues. If you do discover a vulnerability, please send a mail to `me@lanreadelowo.com`.

### License
[MIT](http://opensource.org/licenses/MIT)
