# CHANGELOG

## 1.7.0

* Added `Gbowo\Contract\Adapter\AdapterClientInterface`.. This is to make the `getHttpClient` (adapters already have) part of their specified public API

* Removed deprecated `Gbowo\Gbowo`.. This class was actually deprecated in the initial 1.0.0 release.

## 1.6.1

* Added response context for the AmplifyPayAdapter.

## 1.6.0

* Added context to exceptions being rendered. Prior to this version, if an error occurred while communicating with the API (say an invalid HTTP response code), an exception was thrown and there was no way client code would be able to get the reason for the failure - manually inspect the status code, or even the body.

* A new Exception class was introduced . `Gbowo\Exception\TransactionVerficationFailedException`. This was done as both Paystack and Amplifypay adapters had their own variant of the exception and there was no significant difference between them.

* Removed `Gbowo\Adapter\Paystack\Exception\TransactionVerficationFailedException` and ``Gbowo\Adapter\Amplifypay\Exception\TransactionVerficationFailedException`. Please see the above.


## 1.5.0

* Moved Framework integrations into their own repositories. This isn't a BC break as namespaces were preserved. The affected integrations include
  * The Laravel bridge. [New repo](https://github.com/adelowo/laravel-gbowo)

## 1.4.1
* Bug Fix - Marked `201` HTTP status code - alongside `200` - as a valid response from Paystack as per [the docs](https://developers.paystack.co/v1.0/docs/errors)

## 1.4.0
* Another improvement in the Plugin architecture. This isn't a breaking change. It's more of a revert to the style employed in <= 1.3.0 as it allows for much more readability and the style employed in 1.3.0 wasn't well thought out.
* Added two helper methods :

- `\Gbowo\toKobo` : Convert a given amount to it's kobo equivalent.
- `\Gbowo\toQueryParams` : Convert a dictionary into a URI query.

## 1.3.1
* BugFix - Fixed bug in `\Gbowo\Adapter\Paystack\Plugin\ChargeWithToken` sending unexpected data to paystack which leads to a failure to charge a user via the authentication token.

## 1.3.0
* Improvement - Enforce `handle` method of all Plugins by adding it as a method of `Gbowo\Contract\Plugin\PluginInterface`.

## 1.2.0
* Added `Gbowo\Exception\UnknownAdapterException`. This is thrown only by the `Gbowo\GbowoFactory`
* Improvement/BugFix - Return the instance attached to `Gbowo\GbowoFactory` ___as is___ rather than re-instantiating it - `new`ing it up .
This is to prevent running into dependency issues, as a custom adapter might require some certain class/config value in it's constructor. If that happens, `Gbowo\GbowoFactory` cannot figure that out as it is not a `Container`.

## 1.1.2
* BugFix - prevented an internal adapter from being overriden when adding a custom adapter with the same key as one defined in \Gbowo\GbowoFactory

## 1.1.1

* Added `\Gbowo\GbowoFactory` for easy creation of adapters and adding custom adapters natively.
* Fixed a bug that was experienced when using __Gbowo__ with Laravel.
  Both expose a `env` function. And this made __Gbowo__'s `env` to fail since the function didn't get registered.


## 1.1.0

* Added `\Gbowo\Adapter\Paystack\Plugin\FetchPlan` plugin.
* Added `\Gbowo\Adapter\Paystack\Plugin\FetchAllPlans` plugin.
* Added `\Gbowo\Adapter\AmplifyPay\Plugin\FetchPlan` plugin.
* Added `\Gbowo\Adapter\AmplifyPay\Plugin\FetchAllPlans` plugin.


## 1.0.1

* Marked `AmplifyPayAdapter::BASE_URL` as a constant to allow it's access from plugins not in the core.

## 1.0

* First stable release.
* Requires PHP >= 7.0.
* Paystack adapter.
* Amplifypay adapter.
* Marked `Gbowo\Gbowo` as deprecated.
