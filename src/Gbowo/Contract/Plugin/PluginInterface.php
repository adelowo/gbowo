<?php

namespace Gbowo\Contract\Plugin;

use Gbowo\Contract\Adapter\AdapterInterface;

interface PluginInterface
{
    public function getPluginAccessor() : string;

    public function setAdapter(AdapterInterface $adapter);
}
