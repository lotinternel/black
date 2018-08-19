<?php

namespace app\guanli\controller;

use think\Controller;
use think\Lang;
use think\Cache;
use think\Config;
use app\common\model\Token;
use think\Request;
use app\guanli\model\Workers;
class BasicController extends BaseController{
	protected $workerid;
	
	function __construct() {
		
		parent::__construct();
		//验证auth token是否有效
		$token=Request::instance()->header('Authorization');
		if($token){
			$tokenmodel=new Token();
			$res=$tokenmodel->validtoken($token);
			if(!$res){//如果token无效
				$msg=lang('invalid_token');
				msgput(false,$msg,2);
			}
		$this->workerid=$res['worker_id'];
		}else{
			$msg=lang('no_token');
			msgput(false,$msg,2);
		}
	
	}
	
	public function checkpermission($perrmission=''){
		
		$workermodel=new Workers();
		$res=$workermodel->checkpermission($this->workerid,$perrmission);
		if(!$res){
			msgput(false,'user not have this permission',99);
		}
	}
	
	
	
}

?>