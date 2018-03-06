<?php
namespace Liujiata\Gateway\Fuiou\Util;

class FuyouAes{
	/**
	 * RSA签名
	 * @param $data 待签名数据(按照文档说明拼成的字符串)
	 * @param $private_key_path 商户私钥文件路径
	 * return 签名结果
	 */
	public static function rsaSign($data, $private_key_path) {
	    $priKey = file_get_contents($private_key_path);
	    $res = openssl_get_privatekey($priKey);
	    openssl_sign($data, $sign, $res);
	    openssl_free_key($res);
		//base64编码
	    $sign = base64_encode($sign);
	    return $sign;
	}

	/**
	 * RSA验签
	 * @param $data 待签名数据(如果是xml返回则数据为<plain>标签的值,包含<plain>标签，如果为form(key-value，一般指异步返回类的)返回,则需要按照文档中进行key的顺序进行，value拼接)
	 * @param $ali_public_key_path 富友的公钥文件路径
	 * @param $sign 要校对的的签名结果
	 * return 验证结果
	 */
	public static function rsaVerify($data, $ali_public_key_path, $sign)  {
		$pubKey = file_get_contents($ali_public_key_path);
	    $res = openssl_get_publickey($pubKey);
	    $result = (bool)openssl_verify($data, base64_decode($sign), $res);
	    openssl_free_key($res);    
	    return $result;
	}

	/**
	 *返回结果做验签操作XML返回数据
	 *@param $dataArr 返回的数据 
	 */
	public static function rsaChecksign($dataArr){
		if(is_array($dataArr)){
			$arr = $dataArr['ap']['plain'];
			$sign = $dataArr['ap']['signature'];
			$data = $dataArr['forplain'];
			//var_dump($dataArr,self::rsaVerify($data,PBKEY,$sign));exit;
			if(self::rsaVerify($data,PBKEY,$sign)){
				return $arr;
			}else{
				//return array( "resp_code" => "9999", "resp_desc" => "验签错误", "msg" => $msg);
				$arr['msg'] = $msg;
				return $arr;
			}
		}
	}
}

?>