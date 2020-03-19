<?php

require_once __DIR__.'/../vendor/autoload.php';

use Amsify42\CommandLine\CommandLine;

var_dump(CommandLine::getParams());

var_dump(CommandLine::toString());