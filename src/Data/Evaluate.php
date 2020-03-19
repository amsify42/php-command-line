<?php

namespace Amsify42\CommandLine\Data;

class Evaluate
{
	/**
	 * Convert value to its string version
	 * @param  mixed $value
	 * @return mixed
	 */
	public static function toString($value)
	{
		/**
		 * If value having space in between
		 */
		if(strpos($value, ' ') !== false)
		{
			return '"'.$value.'"';
		}
		/**
		 * Convert to str version if value is bool or null
		 */
		else if(is_bool($value) || is_null($value))
		{
			return var_export($value, true);
		}
		else
		{
			return $value;
		}
	}

	/**
	 * Converts str to its evaluated value
	 * @param  string $str [description]
	 * @return mixed
	 */
	public static function toValue($str)
	{
		$value = trim($str);
		if(is_numeric($value))
		{
			$value = strpos($value, '.') === false ? intval($value) : floatval($value);
		}
		else
		{
			$lower = strtolower($value);
			if($lower == 'true' || $lower == 'false' || $lower == 'null')
			{
				$value = json_decode($lower);
			}
		}
		return $value;
	}
}