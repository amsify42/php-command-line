<?php

namespace Amsify42\Tests;

use PHPUnit\Framework\TestCase;
use Amsify42\CommandLine\CommandLine;

final class GetParamsTest extends TestCase
{
    public function testGetParams()
    {
        $this->assertEquals([], CommandLine::getParams());
    }
}