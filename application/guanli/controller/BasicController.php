<?php

namespace app\guanli\controller;

use think\Controller;
use think\Lang;
use think\Cache;
use think\Config;
use app\common\model\Token;
use think\Request;
class BasicController extends BaseController{
	
	
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
		}else{
			$msg=lang('no_token');
			msgput(false,$msg,2);
		}
	
	}
	
	
	
}

?>