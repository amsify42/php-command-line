<?php

require_once __DIR__.'/../vendor/autoload.php';

\Amsify42\CommandLine\Task::setLogPath(__DIR__.DS.'logs');
\Amsify42\CommandLine\Task::run(\Amsify42\Tests\Task\Sample::class, [], true);

// $task = new \Amsify42\CommandLine\Task();
// $task->process();