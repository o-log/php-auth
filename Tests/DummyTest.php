<?php

namespace Tests;

use Config\AuthDemoConfig;

class DummyTest extends \PHPUnit_Framework_TestCase
{
    public function testDummy(){
        AuthDemoConfig::init();
        $this->assertEquals(1, 1);
    }
}
