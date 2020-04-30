<?php

namespace Amsify42\CommandLine;

use Amsify42\PHPVarsData\Data\Evaluate;

class CommandLine
{
	/**
	 * Checks if CLI params already loaded
	 * @var boolean
	 */
	private static $loaded = false;
	/**
	 * Collects getopt keys if passed
	 * @var array
	 */
	private static $goKeys = [];
	/**
	 * Collects double hyphen keys if passed
	 * @var array
	 */
	private static $dhKeys = [];
	/**
	 * All cli params
	 * @var array
	 */
	private static $params = [];
	/**
	 * Stores the string version of all the params
	 * @var string
	 */
	private static $string = NULL;

	function __construct()
	{
		$this->loadParams();
	}

	private static function setString()
	{
		if(!self::$string && sizeof(self::$params) > 0)
		{
			foreach(self::$params as $key => $value)
			{
				/**
				 * If the key name is numeric, it will assume the param to be passed without key name
				 */
				if(is_numeric($key))
				{
					self::$string .= Evaluate::toString($value).' ';	
				}
				else
				{
					/**
					 * If key is of double hyphen
					 */
					$isDH = false;
					if(in_array($key, self::$dhKeys))
					{
						$isDH = true;
						self::$string .= '--'.$key.' ';
					}
					/**
					 * If key is of getopt
					 */
					else if(in_array($key, self::$goKeys))
					{
						self::$string .= '-'.$key.' ';
					}
					else
					{
						self::$string .= '-'.$key.'=';
					}
					/**
					 * Assign value if param is not of type double hyphen
					 */
					if(!$isDH)
					{
						self::$string .= Evaluate::toString($value, true).' ';
					}
				}
			}
			self::$string = trim(self::$string);
		}
	}

	public static function loadParams()
	{
		if(!self::$loaded)
		{
			if(isset($_SERVER['argv']))
			{
				$getOptKey = NULL;
				for($i=1; $i<count($_SERVER['argv']); $i++)
				{
					/**
					 * If key collected for getopt, assign and reset key to NULL
					 */
					if($getOptKey)
					{
						/**
						 * Collect get opt keys for preparing toString
						 */
						if(!in_array($getOptKey, self::$goKeys))
						{
							self::$goKeys[] = $getOptKey;
						}
						self::$params[$getOptKey] = Evaluate::toValue($_SERVER['argv'][$i]);
						$getOptKey = NULL;
					}
					/**
				     * If pattern matches like -{key}={val}
					 */
				    else if(preg_match('/^-([^=]+)=(.*)/', $_SERVER['argv'][$i], $match))
				    {
				        self::$params[trim($match[1])] = Evaluate::toValue($match[2]);
				    }
				    /**
					 * If pattern matches param like getopt -{key}
					 */
				    else if(preg_match('/^-([^-]+)(.*)/', $_SERVER['argv'][$i], $match))
				    {
				        $getOptKey = trim($match[1]);
				    }
				    /**
					 * If pattern matches with double hiphen --{key}
					 */
				    else if(preg_match('/^--(.*)/', $_SERVER['argv'][$i], $match))
				    {
				    	$dhKey = trim($match[1]);
				    	if(!in_array($dhKey, self::$dhKeys))
						{
							self::$dhKeys[] = $dhKey;
						}
				        self::$params[$dhKey] = true;
				    }
				    /**
				     * else will assign to argv key value
				     */
				    else
				    {
				    	self::$params[$i] = Evaluate::toValue($_SERVER['argv'][$i]);
				    }
				}
			}
		}
		self::$loaded = true;
	}

	public static function isParams($keys=[])
	{
		self::loadParams();
		$result = false;
		if(sizeof($keys)> 0)
		{
			$result = true;
			foreach($keys as $kk => $key)
			{
				if(!self::isParam($key))
				{
					$result = false;
					break;
				}
			}
		}
		return $result;
	}

	public static function getParams()
	{
		self::loadParams();
		return self::$params;
	}

	public static function isParam($key)
	{
		self::loadParams();
		return isset(self::$params[$key])? true: false;
	}

	public static function getParam($key)
	{
		self::loadParams();
		return isset(self::$params[$key])? self::$params[$key]: NULL;
	}

	public static function toString()
	{
		self::loadParams();
		self::setString();
		return self::$string;
	}
}