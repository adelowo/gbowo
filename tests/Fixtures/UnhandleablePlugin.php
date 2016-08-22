<?php

namespace Gbowo\Tests\Fixtures;

use Gbowo\Plugin\AbstractPlugin;

class UnhandleablePlugin extends AbstractPlugin
{

    public function getPluginAccessor() : string
    {
        return 'unhandleable';
    }

}
