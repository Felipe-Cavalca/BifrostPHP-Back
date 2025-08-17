<?php

use Bifrost\Core\Session;
use PHPUnit\Framework\TestCase;

/**
 * @covers Bifrost\Core\Session
 */
class SessionTest extends TestCase
{
    public function testCanBeUsed()
    {
        $this->assertInstanceOf(Session::class, new Session());
    }
}
