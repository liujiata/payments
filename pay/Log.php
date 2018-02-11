<?php
namespace Liujiata;

use Monolog\Handler;
use Monolog;


class Log{

	private static logger;
	//get logger object
	public static getLogger()
	{
		return self::logger ?: self::logger = self::createDefualtLogger();
	}
	//has logger object ?
	public static hasLogger()
	{
		return self::logger?true:false;
	}
	//set logger object ?
	public static setLogger($handler)
	{
		return self::logger = $handler;
	}
	//create logger
	public static createDefualtLogger()
	{
		if(self::hasLogger) return self::logger;

		$stream = new StreamHandler(__DIR__ . "/liujiata.log");

		$logger = new Logger('liujiata.log');

		$logger->pushHandler($stream);

		return $logger;
	}
	
	//使用魔法调用 __callStatic
	public static function __callStatic($method, $args)
	{
		return forward_static_call_array([self::getLogger(), $method], $args);
	}
	//如使用实例对象呢,基本不会用到
	public function __call($method, $args)
	{
		return call_user_func_array([self::getLogger(), $method], $args);
	}
}