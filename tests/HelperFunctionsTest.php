<?php

namespace Gbowo\Tests;

use PHPUnit\Framework\TestCase;

class HelperFunctionsTest extends TestCase
{
    public function testToKobo()
    {
        $this->assertEquals(100000, \Gbowo\toKobo(1000));
        $this->assertEquals(23539, \Gbowo\toKobo(235.39));
        $this->assertEquals(123450, \Gbowo\toKobo(1234.50));
    }

    public function testToQueryParams()
    {
        $dictionary1 = ["name" => "Lanre", "hobby" => "Trolling"];

        //This testcase shows url encoding.
        $dictionary2 = ["name" => "Zeus", "about" => "god of all gods",
               "home" => "Mount Olympus", "wife" => "Hera"
               ];

        $this->assertEquals("?name=Lanre&hobby=Trolling", \Gbowo\toQueryParams($dictionary1));

        $expected = "?name=Zeus&about=god+of+all+gods&home=Mount+Olympus&wife=Hera";

        $this->assertEquals($expected, \Gbowo\toQueryParams($dictionary2));
    }
}
