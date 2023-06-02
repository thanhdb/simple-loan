<?php

namespace Tests\Unit\Traits;

use App\Http\Traits\ApiResponseTrait;
use Tests\TestCase;

class ApiResponseTraitTest extends TestCase
{
    use ApiResponseTrait;
    /**
     * Test success response method
     * @return void
     */
    public function test_success_response()
    {
        $trait = new class {
            use ApiResponseTrait {
                successResponse as public;
            }
        };

        $response = $trait->successResponse(['test'], 'success')->getData();

        $this->assertEquals('success', $response->message);
        $this->assertEquals('test', $response->data[0]);
    }

    /**
     * Test error response method
     * @return void
     */
    public function test_error_response()
{
        $trait = new class {
            use ApiResponseTrait {
                errorResponse as public;
            }
        };

        $response = $trait->errorResponse('error', 400)->getData();

        $this->assertEquals('error', $response->message);
        $this->assertEquals('Error', $response->status);
    }
}
