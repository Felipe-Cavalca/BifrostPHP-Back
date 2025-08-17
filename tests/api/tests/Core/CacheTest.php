<?php

use Bifrost\Core\Cache;
use PHPUnit\Framework\TestCase;

/**
 * @covers Bifrost\Core\Cache
 */
class CacheTest extends TestCase
{
    public function testCannotBeUsedWithoutRedis()
    {
        $this->assertInstanceOf(Cache::class, new Cache());
    }
}
