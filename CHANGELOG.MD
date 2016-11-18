# CHANGELOG

## 1.0

* First stable release.
* Requires PHP >= 7.0.
* Paystack adapter.
* Amplifypay adapter.
* Marked `Gbowo\Gbowo` as deprecated.

## 1.0.1

* Marked `AmplifyPayAdapter::baseUrl` as a constant to allow it's access from plugins not in the core.

## 1.1.0

* Added `\Gbowo\Adapter\Paystack\Plugin\FetchPlan` plugin.
* Added `\Gbowo\Adapter\Paystack\Plugin\FetchAllPlans` plugin.
* Added `\Gbowo\Adapter\AmplifyPay\Plugin\FetchPlan` plugin.
* Added `\Gbowo\Adapter\AmplifyPay\Plugin\FetchAllPlans` plugin.

## 1.1.1

* Added `\Gbowo\GbowoFactory` for easy creation of adapters and adding custom adapters natively.
* Fixed a bug that was experienced when using __Gbowo__ with Laravel. 
  Both expose a `env` function. And this made __Gbowo__'s `env` to fail since the function didn't get registered.

