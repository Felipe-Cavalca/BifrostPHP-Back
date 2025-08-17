<?php

use Bifrost\Core\Queue;
use PHPUnit\Framework\TestCase;

/**
 * @covers Bifrost\Core\Queue
 */
class QueueTest extends TestCase
{
    public function testCanBeUsed()
    {
        $this->assertInstanceOf(Queue::class, new Queue());
    }
}
