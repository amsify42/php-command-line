<?php

namespace Amsify42\CommandLine;

use Amsify42\CommandLine\Data\Evaluate;

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
				if(is_numeric($key))
				{
					self::$string .= Evaluate::toString($value).' ';	
				}
				else
				{
					/**
					 * Key is of getopt
					 */
					if(in_array($key, self::$goKeys))
					{
						self::$string .= '-'.$key.' ';
					}
					else
					{
						self::$string .= '-'.$key.'=';
					}
					self::$string .= Evaluate::toString($value).' ';
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
					 * If pattern matches param like getopt
					 */
				    else if(preg_match('/^-([^-]+)(.*)/', $_SERVER['argv'][$i], $match))
				    {
				        $getOptKey = trim($match[1]);
				    }
				    /**
					 * If pattern matches with double hiphen
					 */
				    else if(preg_match('/^--([^--]+)(.*)/', $_SERVER['argv'][$i], $match))
				    {
				        self::$params[trim($match[1])] = true;
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