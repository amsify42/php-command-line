<?php

if(!defined('DS'))
{
    define('DS', DIRECTORY_SEPARATOR);
}

function cli_get_params()
{
    return \Amsify42\CommandLine\CommandLine::getParams();
}

function cli_to_string()
{
    return \Amsify42\CommandLine\CommandLine::toString();
}

function cli_is_param($key)
{
    return \Amsify42\CommandLine\CommandLine::isParam($key);
}

function cli_get_param($key)
{
    return \Amsify42\CommandLine\CommandLine::getParam($key);
}

function exTN($p=false, $wDT=false)
{
    \Amsify42\CommandLine\Helper\ExecTime::now($p, $wDT);
}

function printMsg($msg, $arrow=true, $sleep=true)
{
    echo "\n\n".($arrow? "-->": "")." {$msg}\n";
    if($sleep) sleep(rand(0.25,0.5));
}