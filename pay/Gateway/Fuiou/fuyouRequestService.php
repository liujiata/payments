<?php
namespace Liujiata\Gateway\Fuiou;

use Liujiata\Gateway\Fuiou\Util\FuyouUtil;
use Liujiata\Gateway\Fuiou\Util\FuyouAes;

class FuyouRequestService {

	protected $merchantId;

	protected $signKey;

	protected $fyver;

	protected $testMode = false;

	function __construct($option)
	{
		$this->merchantId = $option['mchnt_cd'];
		$this->signKey = $option['singKey'];
		$this->fyver = $option['fyver'];
		$this->testMode = $option['testMode'];
	}
	/**
	 * 绑定富友账户(页面接口)
	 * @param entity
	 */
	function page_BindFuyouAccount($bindFuyouPage){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 1);
		$data = array();
		$data = $bindFuyouPage;
		$data['ver'] = $this->fyver;
		$data['mchnt_cd'] = $this->merchantId;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;
		$data['certif_tp'] = 0;//var_dump($signShow);exit;
		echo FuyouUtil::createFormskip($apiUrl, $data);
	}
	
	/**
	 * 富友快速充值接口(页面接口)
	 * @param entity
	 */
	function pageQuickRecharge($rechargePage){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 2);
		$data = array();
		$data = $rechargePage;
		$data['mchnt_cd'] = $this->merchantId;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;//var_dump($data);exit;
		echo FuyouUtil::createFormskip($apiUrl, $data);
	}
	/**
	 * 富友网银充值接口(页面接口)
	 * @param entity
	 */
	function pageBankRecharge($rechargePage){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 3);
		$data = array();
		$data = $rechargePage;
		$data['mchnt_cd'] = $this->merchantId;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;
		echo FuyouUtil::createFormskip($apiUrl, $data);
	}
	/**
	 *富友投标接口（预授权接口）
	 *@param 
	 */
	function tenderQuery($tenderData){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 4);
		$data = array();
		$data = $tenderData;
		$data['mchnt_cd'] = $this->merchantId;
		$data['ver'] = $this->fyver;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;
		$result = FuyouUtil::PostHttpRequest($apiUrl,$data);
		return FuyouAes::rsaChecksign($result);
	}

	/**
	 *富友转账接口（满标，还款，转账接口）
	 *@param 
	 */
	function transferBu($transferData){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 5);
		$data = array();
		$data = $transferData;
		$data['mchnt_cd'] = $this->merchantId;
		$data['ver'] = $this->fyver;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;
		$result = FuyouUtil::PostHttpRequest($apiUrl,$data);
		if($result['resp_code'] == "9999"){
			return $result;
		}
		return FuyouAes::rsaChecksign($result);
	}
	/**
	 *富友提现接口（页面）
	 *@param 
	 */
	function userDrawcash($drawcash){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 6);
		$data = array();
		$data = $drawcash;
		$data['mchnt_cd'] = $this->merchantId;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;
		echo FuyouUtil::createFormskip($apiUrl, $data);
	}
	/**
	 * 商户与个人之间转账接口
	 *
	 */
	function transferBmu($dataBmu){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 7);
		$data = array();
		$data = $dataBmu;
		$data['mchnt_cd'] = $this->merchantId;
		$data['ver'] = $this->fyver;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;
		$result = FuyouUtil::PostHttpRequest($apiUrl,$data);
		return FuyouAes::rsaChecksign($result);
	}
	/**
	 *APP个人用户自助开户注册
	 *
	 */
	function appWebReg($appdata){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 8);
		$data = array();
		$data = $appdata;
		$data['ver'] = $this->fyver;
		$data['mchnt_cd'] = $this->merchantId;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;
		$data['certif_tp'] = 0;//var_dump($signShow);exit;
		echo FuyouUtil::createFormskip($apiUrl, $data);
	}
	/**
	 *APP免登签约(签约之后可使用快速充值)
	 *
	 */
	function appSignCard($appCard){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 9);
		$data = array();
		$data = $appCard;
		$data['mchnt_cd'] = $this->merchantId;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;//var_dump($signShow);exit;
		echo FuyouUtil::createFormskip($apiUrl, $data);
	}
	/**
	 *APP个人用户免登录快速充值
	 *
	 */
	function appQuickRecharge($appRecharge){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 10);
		$data = array();
		$data = $appRecharge;
		$data['mchnt_cd'] = $this->merchantId;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;//var_dump($signShow);exit;
		echo FuyouUtil::createFormskip($apiUrl, $data);
	}
	/**
	 *APP个人用户免登录快速充值
	 *
	 */
	function appBankRecharge($appRecharge){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 11);
		$data = array();
		$data = $appRecharge;
		$data['mchnt_cd'] = $this->merchantId;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;//var_dump($signShow);exit;
		echo FuyouUtil::createFormskip($apiUrl, $data);
	}
	/**
	 *APP个人用户免登录提现（页面）
	 *@param 
	 */
	function appDrawcash($drawcash){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 12);
		$data = array();
		$data = $drawcash;
		$data['mchnt_cd'] = $this->merchantId;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;
		echo FuyouUtil::createFormskip($apiUrl, $data);
	}
	/**
	 *余额查询接口
	 *@param 
	 */
	function balanceAction($drawcash){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 13);
		$data = array();
		$data = $drawcash;
		$data['mchnt_cd'] = $this->merchantId;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;
		$result = FuyouUtil::PostHttpRequest($apiUrl,$data);
		return FuyouAes::rsaChecksign($result);
	}
	/**
	 *更换银行卡接口
	 *@param 
	 */
	function changeCard($carddata){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 14);
		$data = array();
		$data = $carddata;
		$data['mchnt_cd'] = $this->merchantId;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;
		echo FuyouUtil::createFormskip($apiUrl, $data);
	}
	/**
	 *查询更换银行卡接口结果
	 *@param 
	 */
	function changeCardResult($cardresult){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 15);
		$data = array();
		$data = $cardresult;
		$data['mchnt_cd'] = $this->merchantId;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;
		$result = FuyouUtil::PostHttpRequest($apiUrl,$data);
		return FuyouAes::rsaChecksign($result);
	}
	/**
	 *短信通知修改
	 *@param 
	 */
	function smsConfig($smsdata){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 16);
		$data = array();
		$data = $smsdata;
		$data['mchnt_cd'] = $this->merchantId;
		$signShow = $data['page_notify_url'].'|'.$data['busi_tp'].'|'.$data['login_id'].'|'.$data['mchnt_cd'].'|'.$data['mchnt_txn_ssn'];
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;
		echo FuyouUtil::createFormskip($apiUrl, $data);
	}
	/**
	 * 富友预授权撤销接口
	 */
	function preAuthCancel($requestdata){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 18);
		$data = array();
		$data = $requestdata;
		$data['ver'] = $this->fyver;
		$data['mchnt_cd'] = $this->merchantId;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;
		$result = FuyouUtil::PostHttpRequest($apiUrl,$data);
		return FuyouAes::rsaChecksign($result);
	}
	/**
	 * 查询交易订单状态
	 *
	 */
	function queryTxn($querydata){
		$apiUrl = FuyouUtil::urlFactory($this->testMode , 19);
		$data = array();
		$data = $querydata;
		$data['mchnt_cd'] = $this->merchantId;
		$signShow = FuyouUtil::sortKey($data);
		$sign = FuyouAes::rsaSign($signShow,$this->signKey);
		$data['signature'] = $sign;
		$result = FuyouUtil::PostHttpRequest($apiUrl,$data);
		return FuyouAes::rsaChecksign($result);
	}
}

?>