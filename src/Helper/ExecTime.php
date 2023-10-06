<?php

namespace Amsify42\CommandLine\Helper;

use DateTime;

class ExecTime
{
	private static $sTime 	= NULL;

	private static $started = false;

	public static $traces 	= [];

	public static function start()
	{
		self::$started = false;
		self::$traces  = [];
		self::$sTime   = new DateTime();
	}

	public static function started()
	{
		return self::$started;
	}

	public static function now($print=false, $withDt=false)
	{
		$time = "";
		$file = '';
		$line = '';
		$backtrace = debug_backtrace();
		foreach($backtrace as $bk => $bTrace)
		{
			if((isset($bTrace['class']) && $bTrace['class'] == 'Amsify42\CommandLine\Helper\ExecTime' && $bTrace['function'] == 'now') || $bTrace['function'] == 'exTN')
			{
				$file = $bTrace['file'];
				$line = $bTrace['line'];
				if($bTrace['function'] == 'exTN')
				{
					break;
				}
			}
		}
		if(self::$sTime === NULL)
		{
			self::start();
		}
		self::$started = true;
	    self::addTime($time, $withDt);
		if($file)
		{
			$time .= " File: ".$file;
		}
		if($line)
		{
			$time .= " Line: ".$line;
		}
		if($print)
		{
			$prePost = "<br>";
			if(php_sapi_name() == 'cli')
			{
				$prePost = "\n";
			}
			echo $prePost.$time.$prePost;
		}
		else
		{
			self::$traces[] = trim($time);
		}
	}

	private static function addTime(&$time, $withDt=false, $startTime=NULL)
	{
		$eTime = new DateTime();
	    $diff  = ($startTime !== NULL)? $startTime->diff($eTime): self::$sTime->diff($eTime);
	    self::addText('millisecond', $diff->f*1000, $time);
	    if($diff->s > 0)
	    {
	    	self::addText('second', $diff->s, $time);
	    }
	    if($diff->i > 0)
	    {
	    	self::addText('minute', $diff->i, $time);
	    }
	    if($diff->h > 0)
    	{
    		self::addText('hour', $diff->h, $time);
    	}
    	if($diff->d > 0)
    	{
    		self::addText('day', $diff->d, $time);
    	}
    	if($diff->m > 0)
    	{
    		self::addText('month', $diff->m, $time);
    	}
		if($withDt)
		{
			$time .= " - DateTime: ".$eTime->format('Y-m-d H:i:s');
		}
	}

	private static function addText($type, $num, &$time)
	{
		$time = $num." ".$type.($num > 1? "s": "")." ".$time; 
	}

	public static function ended($withDt=false, $startTime=NULL)
	{
		$time = "";
		self::addTime($time, $withDt, $startTime);
		return $time;
	}

	public static function traces()
	{
		return self::$traces;
	}
}