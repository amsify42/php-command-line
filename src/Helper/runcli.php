<?php

set_time_limit(0);

if(php_sapi_name() == 'cli')
{
	require_once __DIR__.'/../../vendor/autoload.php';

	$task = new \Amsify42\CommandLine\Task();
	$task->process();
}
else
{
	echo "\nNot a valid environment\n";
}