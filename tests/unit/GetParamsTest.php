<?php

namespace Amsify42\Tests;

use PHPUnit\Framework\TestCase;
use Amsify42\CommandLine\CommandLine;

final class GetParamsTest extends TestCase
{
    private $sampleCLIParams = '-name=amsify -id 42 --global -isBool true -price 42.24 --some-more -message';
    private $paramsArr = [];

    public function testGetParams()
    {
        $this->setSampleCLIParams();

        $this->assertEquals(cli_get_params(), CommandLine::getParams());

        $cliParamsString = $this->sampleCLIParams.' "Some message"';

        $this->assertEquals($cliParamsString, CommandLine::toString());
        $this->assertEquals($cliParamsString, cli_to_string());

        $this->assertTrue(CommandLine::getParam('isBool'));

        $this->assertTrue(CommandLine::isParam('global'));
        $this->assertFalse(cli_is_param('NoKey'));        

        $this->assertSame(42, CommandLine::getParam('id'));
        $this->assertSame(42.24, cli_get_param('price'));
        $this->assertTrue(CommandLine::isParam('some-more'));
        $this->assertEquals('Some message', CommandLine::getParam('message'));
    }

    private function setSampleCLIParams()
    {
        $this->paramsArr    = explode(' ', $this->sampleCLIParams);
        $this->paramsArr[]  = 'Some message';
        $_SERVER['argv']    = array_merge([$_SERVER['argv'][0]], $this->paramsArr);
    }
}