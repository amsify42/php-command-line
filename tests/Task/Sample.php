<?php

namespace Amsify42\Tests\Task;

use Amsify42\CommandLine\Task\BaseTask;

class Sample extends BaseTask
{
	public function init()
	{
		printMsg('Processing Sample Task');
	}
}