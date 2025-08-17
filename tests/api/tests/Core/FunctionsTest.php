<?php

use Bifrost\Core\Functions;
use PHPUnit\Framework\TestCase;

/**
 * @covers Bifrost\Core\Functions
 */
class FunctionsTest extends TestCase
{
    public function testSanitize()
    {
        // Aspas simples devem ser escapadas com barra invertida
        $this->assertEquals("\\'", Functions::sanitize("'"));

        // Barra invertida deve ser escapada com outra barra invertida
        $this->assertEquals("\\\\", Functions::sanitize("\\"));

        // String sem caracteres especiais permanece igual
        $this->assertEquals("hello", Functions::sanitize("hello"));

        // Se passar número, deve retornar o número como está (pois não é string)
        $this->assertEquals(123, Functions::sanitize(123));
    }

    public function testSanitizeArray()
    {
        $input = [
            "O'Reilly",
            "C:\\path\\to\\file",
            123,          // número deve permanecer intacto
            null,         // null deve permanecer intacto
            "Hello \"World\""
        ];

        $expected = [
            "O\\'Reilly",
            "C:\\\\path\\\\to\\\\file",
            123,
            null,
            "Hello \\\"World\\\""
        ];

        $this->assertEquals($expected, Functions::sanitizeArray($input));
    }
}