<?php

namespace Tests;

use Config\Config;

class DummyTest extends \PHPUnit_Framework_TestCase
{
    public function testDummy(){
        Config::init();
        $this->assertEquals(1, 1);
    }
}
