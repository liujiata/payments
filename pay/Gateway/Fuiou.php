<?php
namespace Liujiata\Gateway;

use Liujiata\Gateway\Fuiou\FuyouRequestService;
/*
 *接口详情请返回fuiou接口
 */
class Fuiou {
	function __construct($config){
		$this->config = $config;
		$this->option = [
			'mchnt_cd'	=>	$this->config['mchnt_cd'],
			'singKey'	=>	$this->config['singKey'],
			'fyver'	=>	$this->config['fyver'],
			'testMode'	=>	$this->config['testMode']
		];
		$this->noticeUrl = $this->config['noticeUrl'];
		$this->returnUrl = $this->config['returnUrl'];
		$this->fuyouRequestService = new FuyouRequestService($this->option);
	}

	//调用notice通知地址*******更改为自己的回调处理接口********
	protected function makeNoticeUrl($actionType)
	{
		return $this->noticeUrl . $actionType;
	}

	//调用return通知地址*******更改为自己的回调处理接口********
	protected function makeReturnUrl($actionType)
	{
		return $this->returnUrl . $actionType;
	}

	/**
	*注册开户
	*@param 注册接受的数据
	**/
     public function register($data)
     {
     	$datas = array();
     	$actionType = 'register';
     	//必填项
        $datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn']; //流水号
        $datas['certif_id'] = $data['certif_id'];//身份证号码
        //$datas['certif_tp'] = 0;
        //选择
        $datas['user_id_from'] = $data['user_id_from']; //用户在商户的ID号
        $datas['mobile_no'] = $data['mobile_no']; //客户手机号
        $datas['cust_nm'] = $data['cust_nm']; //客户姓名
        $datas['mail'] = $data['mail'];
        $datas['city_id'] = $data['city_id'];
        $datas['parent_bank_id'] = $data['parent_bank_id'];
        $datas['bank_nm'] = $data['bank_nm'];
        $datas['capAcntNo'] = $data['capAcntNo'];
        $datas['page_notify_url'] = $this->makeNoticeUrl($actionType);
        $datas['back_notify_url'] = $this->makeReturnUrl($actionType);
        $this->fuyouRequestService->page_BindFuyouAccount($datas);
	}
	/**
	*用户快速充值接口
	*@param 数据
	**/
	public function QuickRecharge($data)
	{
		$datas = array();
		$actionType = 'recharge';
		//必填项
		$datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn']; //流水号
		$datas['login_id'] = $data['login_id'];
		$datas['amt'] = $data['amt'];
		$datas['page_notify_url'] = $this->makeNoticeUrl($actionType);
		$datas['back_notify_url'] = $this->makeReturnUrl($actionType);
        $this->fuyouRequestService->pageQuickRecharge($datas);
	}
	/**
	*用户网银充值接口
	*@param 数据
	**/
	public function BankRecharge($data)
	{
		$datas = array();
		$actionType = 'bankrecharge';
		//必填项
		$datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn']; //流水号
		$datas['login_id'] = $data['login_id'];
		$datas['amt'] = $data['amt'];
		$datas['page_notify_url'] = $this->makeNoticeUrl($actionType);
		$datas['back_notify_url'] = $this->makeReturnUrl($actionType);userlogs($datas);
        $this->fuyouRequestService->pageBankRecharge($datas);
	}
	/**
	 *用户投标接口
	 *
	 */
	public function actionloan($data)
	{
		$datas = array();
		//必填项
		$datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn']; //流水号
		$datas['out_cust_no'] = $data['out_cust_no'];//投标人
		$datas['in_cust_no'] = $data['in_cust_no']; //借款人
		$datas['amt'] = $data['amt'];
		$datas['rem'] = '投标';//备注
        $return = $this->fuyouRequestService->tenderQuery($datas);

        if($return['resp_code'] == '0000')
        {
             $res['return'] = 1;
             $res['contract_no'] = $return['contract_no'];
             $res['errmsg'] = "投标成功!";
         }
         else
         {
        userlogs($datas,$return);
             $res['return'] = 0;
             $res['errmsg'] = $return['msg'];
         }
         return $res;
	}
	/**
	 *用户转账接口(满标,还款)
	 *
	 **/
	public function transferUser($data)
	{
		$datas = array();
		//必填项
		$datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn']; //流水号
		$datas['out_cust_no'] = $data['out_cust_no'];//投标人
		$datas['in_cust_no'] = $data['in_cust_no']; //借款人
		$datas['amt'] = $data['amt'];
		$datas['rem'] = '满标';//备注
		if($datas['action_type'] == "4"){
			$datas['rem'] = '还款';//备注
		}
		$datas['contract_no'] = $data['contract_no'];
        $return = $this->fuyouRequestService->transferBu($datas);
        if($return['resp_code'] == '0000')
        {
             $res['return'] = 1;
             $res['contract_no'] = $return['contract_no'];
             $res['errmsg'] = "满标成功!";
         }
         else
         {
	    	 userlogs($datas,$return);
             $res['return'] = 0;
             $res['errmsg'] = $return['resp_desc'];
         }
         return $res;
	}

	/**
	 *用户提现接口
	 *@param data
	 **/
	public function userDrawcash($data)
	{
		$datas = array();
		$actionType = 'cashing';
		//必填项
		$datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn']; //流水号
		$datas['login_id'] = $data['login_id'];
		$datas['amt'] = $data['amt'];
		$datas['page_notify_url'] = $this->makeNoticeUrl($actionType);
		$datas['back_notify_url'] = $this->makeReturnUrl($actionType);
        $this->fuyouRequestService->userDrawcash($datas);
	}
	/**
	 *用户转账接口
	 *@param data
	 */
	public function comTransfer($data)
	{
		$datas = array();
		//必填项
		$datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn']; //流水号
		//商户给用户转账
		if($data['actionType'] == 1){
			$datas['out_cust_no'] = FUYOUUSER;//转出
			$datas['in_cust_no'] = $data['in_cust_no']; //转入
		}elseif($data['actionType'] == 2){	//用户给商户转账
			$datas['out_cust_no'] = $data['out_cust_no'];//转出
			$datas['in_cust_no'] = FUYOUUSER; //转入
		}
		$datas['amt'] = $data['amt'];
		$datas['contract_no'] = '';
		$datas['rem'] = '转账';
        $return = $this->fuyouRequestService->transferBmu($datas);
        if($return['resp_code'] == '0000')
        {
             $res['return'] = 1;
             $res['errmsg'] = "转账成功!";
         }
         else
         {
             $res['return'] = 0;
             $res['errmsg'] = $return['msg'];
         }
         return $res;
	}
	/**
	 *APP个人用户自助开户注册
	 *@param
	 */
	public function appRegister($data)
	{
		$datas = array();
     	$actionType = 'register';
     	$actionTypeApp = 'm_register';
     	//必填项
        $datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn']; //流水号
        $datas['certif_id'] = $data['certif_id'];//身份证号码
        //$datas['certif_tp'] = 0;
        //选择
        $datas['user_id_from'] = $data['user_id_from']; //用户在商户的ID号
        $datas['mobile_no'] = $data['mobile_no']; //客户手机号
        $datas['cust_nm'] = $data['cust_nm']; //客户姓名
        $datas['mail'] = $data['mail'];
        $datas['city_id'] = $data['city_id'];
        $datas['parent_bank_id'] = $data['parent_bank_id'];
        $datas['bank_nm'] = $data['bank_nm'];
        $datas['capAcntNo'] = $data['capAcntNo'];
        $datas['page_notify_url'] = $this->makeNoticeUrl($actionTypeApp);
        $datas['back_notify_url'] = $this->makeReturnUrl($actionType);
        $this->fuyouRequestService->appWebReg($datas);
	}
	/**
	 *APP免登签约
	 *
	 */
	public function appSignCard($data)
	{
		$datas = array();
     	$actionType = 'm_appsign';
     	//必填项
        $datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn']; //流水号
        $datas['login_id'] = $data['login_id'];
        $datas['mobile'] = $data['mobile'];//银行预留手机号
        $datas['page_notify_url'] = $this->makeNoticeUrl($actionType);
        $this->fuyouRequestService->appSignCard($datas);
	}
	/**
	 *APP快速充值
	 *
	 */
	public function appQuickRc($data)
	{
		$datas = array();
     	$actionType = 'm_appqrecharge';
     	//必填项
        $datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn']; //流水号
        $datas['login_id'] = $data['login_id'];
        $datas['amt'] = $data['amt'];
        $datas['page_notify_url'] = $this->makeNoticeUrl($actionType);
        $datas['back_notify_url'] = $this->makeReturnUrl($actionType);
        $this->fuyouRequestService->appQuickRecharge($datas);
	}
	/**
	 *APP快捷充值
	 *
	 */
	public function appBankRc($data)
	{
		$datas = array();
     	$actionType = 'm_appqrecharge';
     	//必填项
        $datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn']; //流水号
        $datas['login_id'] = $data['login_id'];
        $datas['amt'] = $data['amt'];//银行预留手机号
        $datas['page_notify_url'] = $this->makeNoticeUrl($actionType);
        $datas['back_notify_url'] = $this->makeReturnUrl($actionType);
        $this->fuyouRequestService->appBankRecharge($datas);
	}
	/**
	 *APP提现接口
	 *@param data
	 **/
	public function appDrawcash($data)
	{
		$datas = array();
		$actionType = 'cashing';
		$actionTypeApp = 'm_cashing';
		//必填项
		$datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn']; //流水号
		$datas['login_id'] = $data['login_id'];
		$datas['amt'] = $data['amt'];
		$datas['page_notify_url'] = $this->makeNoticeUrl($actionTypeApp);
		$datas['back_notify_url'] = $this->makeReturnUrl($actionType);
        $this->fuyouRequestService->appDrawcash($datas);
	}
	/**
	 *用户余额查询
	 *
	 */
	public function balance($data)
	{
		$datas = array();
		$datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn'];
		$datas['mchnt_txn_dt'] = $data['mchnt_txn_dt'];
		$datas['cust_no'] = $data['cust_no'];
		$return = $this->fuyouRequestService->balanceAction($datas);
        if($return['resp_code'] == '0000')
        {
             $res['return'] = 1;
             $res['results'] = $return['results']['result'];
         }
         else
         {
             $res['return'] = 0;
             $res['errmsg'] = $return['msg'];
         }
         return $res;
	}
	/**
	 *换绑银行卡
	 *
	 */
	public function changebank($data)
	{
		$datas = array();
		$actionTypeApp = 'changecard';
		$datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn'];
		$datas['login_id'] = $data['login_id'];
		$datas['page_notify_url'] = $this->makeNoticeUrl($actionTypeApp);
		$this->fuyouRequestService->changeCard($datas);
	}
	/**
	 *查询换绑结果
	 *
	 */
	public function changecardresult($data)
	{
		$datas = array();
		$datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn'];
		$datas['login_id'] = $data['login_id'];
		$datas['txn_ssn'] = $data['txn_ssn'];
		$return = $this->fuyouRequestService->changeCardResult($datas);
        if($return['resp_code'] == '0000')
        {
             $res['return'] = 1;
             $res['results'] = $return;
         }
         else
         {
             $res['return'] = 0;
             $res['errmsg'] = $return['desc_code'];
         }
         return $res;
	}
	/**
	 *换绑银行卡
	 *
	 */
	public function usersetsms($data)
	{
		$datas = array();
		$actionTypeApp = 'changesms';
		$datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn'];
		$datas['login_id'] = $data['login_id'];
		$datas['busi_tp'] = $data['busi_tp'];
		$datas['page_notify_url'] = $this->makeNoticeUrl($actionTypeApp);
		$this->fuyouRequestService->smsConfig($datas);
	}
	/**
	 * 交易查询接口
	 */
	public function userquerycheck($data)
	{
		$datas = array();
		$datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn'];
		$datas['busi_tp'] = $data['busi_tp']; //交易类型
		$datas['start_day'] = $data['start_day']; //起始时间
		$datas['end_day'] = $data['end_day'];	//截止时间
		$datas['txn_ssn'] = $data['txn_ssn'];	//交易流水
		$datas['cust_no'] = $data['cust_no'];	//交易用户
		$datas['txn_st'] = $data['txn_st'];		//交易状态
		$datas['remark'] = $data['remark'];	//交易备注
		$datas['page_no'] = $data['page_no'];
		$datas['page_size'] = $data['page_size'];
		$result = $this->fuyouRequestService->queryTxn($datas);
		if($result['resp_code'] == "0000"){
			if($result['total_number'] > 0){
				if($result['results']['result']['txn_rsp_cd'] == "0000"){
					$res['return'] = 1;
             		$res['results'] = $result['results']['result']['rsp_cd_desc'];
             		return $res;
				}else{
					$res['return'] = 0;
             		$res['errmsg'] = $result['results']['result']['rsp_cd_desc'];
             		return $res;
				}
			}
		}
		$res['return'] = 2;
        $res['errmsg'] = $result['results']['result']['rsp_cd_desc'];
        return $res;
	}
	/**
	 *流标接口()
	 *
	 **/
	public function userpreAuthCancel($data)
	{
		$datas = array();
		//必填项
		$datas['mchnt_txn_ssn'] = $data['mchnt_txn_ssn']; //流水号
		$datas['out_cust_no'] = $data['out_cust_no'];//投标人
		$datas['in_cust_no'] = $data['in_cust_no']; //借款人
		$datas['contract_no'] = $data['contract_no'];//预授权合同号
		$datas['rem'] = '流标';//备注

        $return = $this->fuyouRequestService->preAuthCancel($datas);
        if($return['resp_code'] == '0000')
        {
             $res['return'] = 1;
             $res['contract_no'] = $return['contract_no'];
             $res['errmsg'] = "满标成功!";
         }
         else
         {
             $res['return'] = 0;
             $res['errmsg'] = $return['resp_desc'];
         }
         return $res;
	}

}