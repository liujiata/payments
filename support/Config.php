<?php
namespace Liujiata\Support;

class Config implements \ArrayAccess{

	private $item = [];
	//实例化
	public function __construct(array $config)
	{
		foreach($config as $k=>$v){
			$this->set($this->item,$k, $v);
		}
	}

	//设置config对象
	protected function set(&$array,$key, $value)
	{
		if(is_null($key)){
			return $array = $value;
		}
		$keys = explode('.', $key);

		$array[array_shift($keys)] = $value;

		return $array;
	}
	//获取设置的对象
	protected function get($array, $key)
	{
		if(isset($array[$key])){
			//var_dump($array[$key]);
			return $array[$key];
		}
	}

	//访问对象的$config的值
	public function offsetGet($key)
	{
		return $this->get($this->item, $key);
	}
	//访问对象是否存在
	public function offsetExists($key)
	{
		return isset($this->config[$key]);
	}
	//设置对象
	public function offsetSet($key, $value)
	{
		$this->set($key, $value);
	}
	//删除对象
	public function offsetUnset ($key) 
	{
		unset($this->config[$Key]);
	}

}
?>