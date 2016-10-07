<?php

namespace Gbowo\Bridge\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @codeCoverageIgnore
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class Gbowo
 * @package Gbowo\Bridge\Laravel
 */
class Gbowo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'gbowo';
    }
}
