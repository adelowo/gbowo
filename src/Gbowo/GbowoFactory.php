<?php


namespace Gbowo;

use InvalidArgumentException;
use Gbowo\Adapter\Paystack\PaystackAdapter;
use Gbowo\Contract\Adapter\AdapterInterface;
use Gbowo\Adapter\Amplifypay\AmplifypayAdapter;

class GbowoFactory
{

    const PAYSTACK = "paystack";

    const AMPLIFY_PAY = "amplifypay";

    protected $availableAdapters = [];

    public function __construct(array $types = [])
    {
        $this->setDefaultAdapters();

        if (!empty($types)) {
            foreach ($types as $type => $value) {
                if (!$value instanceof AdapterInterface) {
                    throw $this->throwException("This is not a valid adapter");
                }
            }
            $this->availableAdapters = array_merge($this->availableAdapters, $types);
        }
    }

    protected function setDefaultAdapters()
    {
        $this->availableAdapters = [
            self::PAYSTACK => PaystackAdapter::class,
            self::AMPLIFY_PAY => AmplifypayAdapter::class
        ];
    }

    protected function throwException(string $message)
    {
        return new InvalidArgumentException($message);
    }

    /**
     * @param string $adapterIdentifier
     * @return AdapterInterface
     * @throws InvalidArgumentException
     */
    public function createAdapter(string $adapterIdentifier)
    {
        if (!array_key_exists($adapterIdentifier, $this->availableAdapters)) {
            throw $this->throwException(
                "Unknown adapter. Did you forget to add this adapter on object initialization ?"
            );
        }

        return new $this->availableAdapters[$adapterIdentifier];
    }
}
