<?php


namespace Gbowo;

use Gbowo\Exception\UnknownAdapterException;
use Gbowo\Adapter\Paystack\PaystackAdapter;
use Gbowo\Contract\Adapter\AdapterInterface;
use Gbowo\Adapter\Amplifypay\AmplifypayAdapter;

class GbowoFactory
{

    const PAYSTACK = "paystack";

    const AMPLIFY_PAY = "amplifypay";

    /**
     * @var AdapterInterface[]
     */
    protected $availableAdapters = [];

    public function __construct(array $types = [])
    {
        $this->setDefaultAdapters();
        $this->addCustomAdapters($types);
    }

    protected function addCustomAdapters(array $types)
    {
        if (!empty($types)) {
            $this->validateCustomAdapters($types);
            $this->availableAdapters = array_merge($this->availableAdapters, $types);
        }
    }


    protected function setDefaultAdapters()
    {
        $this->availableAdapters = [
            self::PAYSTACK => new PaystackAdapter(),
            self::AMPLIFY_PAY => new AmplifypayAdapter()
        ];
    }

    /**
     * @param array $types
     * @throws \Gbowo\Exception\UnknownAdapterException
     */
    protected function validateCustomAdapters(array $types)
    {
        foreach ($types as $type => $value) {
            if (array_key_exists($type, $this->availableAdapters)) {
                throw $this->throwException(
                    "You cannot override an internal adapter"
                );
            }

            if (!$value instanceof AdapterInterface) {
                throw $this->throwException("This is not a valid adapter");
            }
        }
    }

    protected function throwException(string $message)
    {
        return new UnknownAdapterException($message);
    }

    /**
     * @param string $adapterIdentifier
     * @return \Gbowo\Contract\Adapter\AdapterInterface
     * @throws \Gbowo\Exception\UnknownAdapterException
     */
    public function createAdapter(string $adapterIdentifier)
    {
        if (!array_key_exists($adapterIdentifier, $this->availableAdapters)) {
            throw $this->throwException(
                "Unknown adapter. Did you forget to add this adapter on object initialization ?"
            );
        }

        return $this->availableAdapters[$adapterIdentifier];
    }
}
