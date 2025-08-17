<?php

use Bifrost\Class\HttpResponse;
use Bifrost\Core\AppError;
use Bifrost\Enum\HttpStatusCode;
use PHPUnit\Framework\TestCase;

/**
 * @covers Bifrost\Core\AppError
 */
class AppErrorTest extends TestCase
{
    public function testCanBeUsed()
    {
        $response = new HttpResponse(
            status: HttpStatusCode::BAD_REQUEST,
            message: 'This is a test error',
            errors: ['field' => 'error message']
        );
        $appError = new AppError($response);

        $this->assertInstanceOf(AppError::class, $appError);
        $this->assertInstanceOf(Exception::class, $appError);
        $this->assertEquals((string) $response, $appError->getMessage());
        $this->assertEquals((string) $response, (string) $appError);
        $this->assertSame($response, $appError->response);
    }
}
