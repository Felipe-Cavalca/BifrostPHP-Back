<?php

use Bifrost\Core\Get;
use PHPUnit\Framework\TestCase;

/**
 * @covers Bifrost\Core\Get
 */
class GetTest extends TestCase
{
    public function testCanBeUsed()
    {
        $this->assertInstanceOf(Get::class, new Get());
    }
}
