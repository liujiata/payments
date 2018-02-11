<?php
namespace Liujiata\Support;

class Config implements ArrayAccess{

	private $config = [];
	//实例化
	public function __construct(array $config)
	{
		foreach($config as $k=>$v){
			$this->set($k, $v);
		}
	}

	//设置config对象
	public function set($key, $value)
	{
		if(is_null($key)){
			return ;
		}
		$keys = explode('.', $key);

		$this->array[array_shift($keys)] = $value;

		return $this->array;
	}

	//访问对象的$config的值
	public function offsetGet($key)
	{
		if(isset($key)){
			return $this->config[$key];
		}
			return;
	}
	//访问对象是否存在
	public function offsetExists($key)
	{
		return isset($this->config[$key]);
	}

}
?>