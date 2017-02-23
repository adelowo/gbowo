<?php

namespace Gbowo\Tests;

class HelperFunctionsTest extends \PHPUnit\Framework\TestCase
{

    public function testToKobo()
    {
        $this->assertEquals(100000, \Gbowo\toKobo(1000));
        $this->assertEquals(23539, \Gbowo\toKobo(235.39));
        $this->assertEquals(123450, \Gbowo\toKobo(1234.50));
    }
}
