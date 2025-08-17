<?php

use Bifrost\Core\Request;
use PHPUnit\Framework\TestCase;

/**
 * @covers Bifrost\Core\Request
 */
class RequestTest extends TestCase
{
    public function testCanBeUsed()
    {
        $this->assertInstanceOf(Request::class, new Request());
    }
}
