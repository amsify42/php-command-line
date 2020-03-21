<?php

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