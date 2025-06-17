<?php

namespace Tests\Controllers;

use Mockery;
use PHPUnit\Framework\TestCase;
use App\Controllers\Product;

class ProductTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testIndexActionCallsSaveAndUploadFile()
    {
        $routeParams = [];
        // on teste indexAction en mockant save et uploadFile
        $productMock = Mockery::mock(Product::class, [$routeParams])->makePartial();
        $productMock->shouldReceive('save')->once()->andReturn(1);
        $productMock->shouldReceive('uploadFile')->once()->andReturn(true);
        $productMock->indexAction();

        $this->assertTrue(true);
    }
}
