<?php
namespace Liujiata\Gateway\Fuiou\Util;

class FuyouUtil{
	public static $xmlHead = "<?xml version='1.0' encoding='UTF-8'?>";
	/**
	*@param istesturl 测试地址
	* 1.个人用户自助开户注册(网页版);
	* 2.商户P2P网站免登录快速充值接口;
	* 3.商户P2P网站免登录网银充值接口;
	* 4.预授权接口
	* 5.划拨(个人与个人之间)
	* 6.提现(网页版)
	* 7.转账(商户与个人之间)
	* 8.APP个人用户自助开户注册
	* 9.APP免登签约
	* 10.APP个人用户免登录快速充值
	* 11.APP个人用户免登录快捷充值
	* 12.APP个人用户免登录提现
	* 13.余额查询
	* 14.更换银行卡
	* 15.更换银行卡查询
	* 16.用户短信配置
	* 18.预授权撤销接口
	**/
	public static function urlFactory($istestUrl,$state){
		$realUrl = "https://jzh-test.fuiou.com/jzh/";
		if(!$istestUrl){
			$realUrl = "https://jzh-test.fuiou.com/jzh/";
		}
		switch($state){
			case 1:
			$realUrl .= "webReg.action";
			break;
			case 2:
			$realUrl .= "500001.action";
			break;
			case 3:
			$realUrl .= "500002.action";
			break;
			case 4:
			$realUrl .= "preAuth.action";
			break;
			case 5:
			$realUrl .= "transferBu.action";
			break;
			case 6:
			$realUrl .= "500003.action";
			break;
			case 7:
			$realUrl .= "transferBmu.action";
			break;
			case 8:
			$realUrl .= "app/appWebReg.action";
			break;
			case 9:
			$realUrl .= "app/appSign_card.action";
			break;
			case 10:
			$realUrl .= "app/500001.action";
			break;
			case 11:
			$realUrl .= "app/500002.action";
			break;
			case 12:
			$realUrl .= "app/500003.action";
			break;
			case 13:
			$realUrl .= "BalanceAction.action";
			break;
			case 14:
			$realUrl .= "changeCard2.action";
			break;
			case 15:
			$realUrl .= "queryChangeCard.action";
			break;
			case 16:
			$realUrl .= "app/authConfig.action";
			break;
			case 18:
			$realUrl .= "preAuthCancel.action";
			break;
			case 19:
			$realUrl .= "queryTxn.action";
		}
		return $realUrl;
	}

	/**
	* form表单自动跳转
	*@param pageUrl 跳转地址
	*@param customParam 数据
	**/
	public static function createFormskip($pageUrl,$customParam){
		$attrs = array() ;
		if( !empty($customParam)){
			$attrs = $customParam;
		}
		$result = "<html><head><script type='text/javascript'>window.onload=function(){document.getElementById('submitForm').submit();}</script></head><body>";
		try {
			if(strpos($pageUrl,"?") != false){
				$url = split("\\?",$pageUrl);
				$params = split("\\?",$pageUrl);
				if(strpos($params,"&") != false){
					if(empty($attrs)){
						$attrs  = split("&",$params);
					}else{
						array_push($attrs,split("&",$params));
					}
				}else{
					if(empty($attrs)){
						$attrs = array($params);
					}else{
						array_push($attrs,split("&",$params));
					}
				}
			}else{
				$url = $pageUrl;
			}
			$result.= "<form action='".$url."' id='submitForm' method='post'>";
			if(!empty($attrs)){
				foreach($attrs as $feild=>$value){
					if(strpos($feild,"=")!=false){
						$feilds = array();
						if(strpos($feild,self::$xmlHead) != false){
							$feilds = split("=<[?]xml version='1.0' encoding='UTF-8'[?]>",$feild);
							$feilds[1] = self::xmlHead.$feilds[1];
						}else{
							$feilds = split("\\=",$feild);
						}
						$result.="<input type='hidden' name='".$feilds[0]."'  value='".$feilds[1]."'  >";
							
					}else{
						$result.="<input type='hidden' name='".$feild."'  value='".$value."' >";
					}
				}
			}
			$result.="</form></body></html>";
		} catch (Exception $e) {
			return "页面返回地址解析异常 ";
		}
		return $result;
	}
	/**
	*对数据进行排序
	*@param datas需要排序的数组
	**/
	public static function sortKey($datas){
		if(is_array($datas)){
			ksort($datas);
			$res = null;
			foreach($datas as $k => $v){
				if($res == null){
					$res = $v;
				}else{
					$res .= "|".$v;
				}
			}
			return $res;
		}
	}
	/**
	*XML转Array
	**/
	public static function xmlToArray($resultXML){
		$resultArray = array();
		foreach($resultXML->children() as $node){
			$nodeName = $node->getName();
			if(count($node->children()) == 0){
				$resultArray[$nodeName] = $node;
			}else{
				if(!array_key_exists($nodeName,$resultArray)){
					$resultArray[$nodeName] = array();
				}
				array_push($resultArray[$nodeName],self::xmlToArray($node));
			}
		}
		return $resultArray;
	}
	/**
	 *POST请求数据返回XML
	 *@param apiUrl 请求地址
	 *@param data 请求数据
	 */
	public static function PostHttpRequest($apiUrl,$data){
		try {
			$httpRequest = self::userCurlRequest($apiUrl,$data);
			$res = self::xml_to_array($httpRequest);
			$res['forplain'] = self::xml_to_plain($httpRequest);
			return $res;
		} catch (Exception $e) {
			//print $e->getMessage();exit;
			return array( "resp_code" => "9999", "resp_desc" => "远程请求返回失败" );
		}
	}
	/**
	 *模拟post请求数据
	 *@param url 请求地址
	 *@param data 请求数据
	 *return 返回结果
	 */
	private static function userCurlRequest($url,$data){
		$user_agent = "Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)";
		$headers = array('content-type: application/x-www-form-urlencoded;charset=UTF-8');
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_HEADER,$headers);
		curl_setopt($ch,CURLOPT_USEAGENT,$user_agent);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($data));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//不验证证书下同
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		//执行
		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);//获取一个curl连接资源句柄的信息
		curl_close($ch);
		if ($httpCode != 200){
			throw new Exception("远程请求返回失败".$httpCode);
		}
		return $result;
	}
	/**
	 * xml转array
	 */
	static function xml_to_array($xml)
	{
		$matches = array();
		$reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
		if(preg_match_all($reg, $xml, $matches))
		{
			$count = count($matches[0]);
			$arr = array();
			for($i = 0; $i < $count; $i++)
			{
			$key= $matches[1][$i];
			$val = self::xml_to_array( $matches[2][$i] );  // 递归
				if(array_key_exists($key, $arr))
				{
					if(is_array($arr[$key]))
					{
						if(!array_key_exists(0,$arr[$key]))
						{
						$arr[$key] = array($arr[$key]);
						}
					}else{
						$arr[$key] = array($arr[$key]);
					}
					$arr[$key][] = $val;
				}else{
					$arr[$key] = $val;
				}
			}
			return $arr;
		}else{
			return $xml;
		}
	}
	/**
	 *截取xml的值符合富友<plain>内的string
	 */
	static function xml_to_plain($xml)
	{
		$xmlIndex = stripos($xml,'<plain>');
		$xmlFor = strripos($xml,'</plain>');
		return substr($xml,$xmlIndex,($xmlFor-$xmlIndex+8));
	}
}
?>