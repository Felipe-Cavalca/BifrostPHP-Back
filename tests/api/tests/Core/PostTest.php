<?php

use Bifrost\Core\Post;
use PHPUnit\Framework\TestCase;

/**
 * @covers Bifrost\Core\Post
 */
class PostTest extends TestCase
{
    public function testCanBeUsed()
    {
        $this->assertInstanceOf(Post::class, new Post());
    }
}
