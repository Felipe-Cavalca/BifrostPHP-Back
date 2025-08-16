<?php

use Bifrost\Core\Functions;
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{

    public function testSanitize()
    {
        $this->assertEquals("''", Functions::sanitize("'"));
        $this->assertEquals("\\\\", Functions::sanitize("\\"));
        $this->assertEquals("test '' test \\\\", Functions::sanitize("test ' test \\"));
        $this->assertEquals("test", Functions::sanitize("test"));
        $this->assertEquals("", Functions::sanitize(""));
    }

    public function testSanitizeArray()
    {
        $testArray = ["test'", 'test\\', 'test"'];
        $sanitizedArray = Functions::sanitizeArray($testArray);
        $this->assertEquals("test\'", $sanitizedArray[0]);
        $this->assertEquals("test\\\\", $sanitizedArray[1]);
        $this->assertEquals("test\\\"", $sanitizedArray[2]);

        $testArray = ["test", "no-special-chars"];
        $sanitizedArray = Functions::sanitizeArray($testArray);
        $this->assertEquals("test", $sanitizedArray[0]);
        $this->assertEquals("no-special-chars", $sanitizedArray[1]);

        $this->assertEquals([], Functions::sanitizeArray([]));
    }

}
