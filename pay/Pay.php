<?php
namespace Liujiata;

use Monolog\Handler;
use Monolog;
use Liujiata\Support\Config;

class Pay{

	protected $config;

	function __construct($config)
	{
		$this->config = new Config($config);

	}
	//集中处理调用
	protected function pay($method)
	{
		!isset($this->config['log'])?:$this->registerLog($this->config['log']);
		$gateway = __NAMESPACE__."\\Gateway\\".$method;

		if(class_exists($gateway)){
			return self::make($gateway);
		}
	}

	//注册默认的日志记录方式
	protected function registerLog()
	{
		$file = $this->config['log'];
		$stream = new StreamHandler($file);
		$logger = new logger('liujiata.logs');
		$logger->pushHandler($stream);

		Log::setLogger($logger);

	}

	//实例化
	protected function make($method)
	{
		$app = new $method($this->config);

		return $app;
	}
	//魔术方法调用付款方式
	public static function __callStatic($method, $parameters)
	{
		if(is_string($method)){
			$pay = new self(...$parameters);
			return $pay->pay($method);
		}
	}
}
