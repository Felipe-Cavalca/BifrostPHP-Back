<?php

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class AutoloadTest extends TestCase
{
    public function testAutoload()
    {
        $this->assertTrue(class_exists(\Bifrost\Core\AppError::class));
    }
}
