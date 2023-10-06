<?php

namespace Amsify42\CommandLine\Task;

use Amsify42\CommandLine\CommandLine;

class BaseTask
{
	protected function validate($params)
	{
		$isValid = true;
		foreach($params as $pk => $param)
		{
			if(!CommandLine::isParam($param))
			{
				$isValid = false;
				printMsg($param.' is required', true, false);
			}
		}
		if(!$isValid)
		{
			exit;
		}
	}

	protected function input($param, $default=NULL)
	{
		return CommandLine::getParam($param, $default);
	}
}