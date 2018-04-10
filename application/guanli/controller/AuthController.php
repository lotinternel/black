<?php

namespace app\guanli\controller;

use app\guanli\controller\BaseController;
use app\guanli\model\Workers;
use \Firebase\JWT\JWT;
use app\common\model\Token;
use think\Request;
use think\Config;

class AuthController extends BaseController {
	private $privateKey = <<<EOD
-----BEGIN PRIVATE KEY-----
MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAJl2dVgEx4CLTPNW
vDiPscK/ld+dKbMdmwBICko5MbKZAhbVJ8R25a76UcRG64eDtQAEETLK9LHtr2CF
ETbXn2gOT6wj5HJxp5OkL9NYNByBL1yt16Yg8Q6j6B/BmrMqaoYTF0/qr/B4cH4A
GEgkwAZOcYk3+J6rAlBpCSH9o3BVAgMBAAECgYAdg4a10tV7h07ZTckNJ7WMOsRc
KSGn7P2uZCx2ceF486xPL9diFWu+5y1qjikl+tKImj+kgRvmTEv6SB0zauyhDjpE
03hTYvn0Y9DlfBYiRS31tgJcPVW012Zj0w9oj3tF9YwhiDvD+y9RE6rKucaobPVm
dGXWXiE5qX/zl4180QJBAMv0mb9DKQxzdiJ7pUk5P46ZeMYmIgjqbsoJhnToXAi2
Ys44t6bixcCjpRDzl3PV06WjmXDRwxJhFcTwS9+fbocCQQDAn2rVITAkgY2//exP
5tREt/Q8WSh07faY49BvUnrmzhtKWgXv2ZfE0EEcEu4uaKZwoLITwk2AQ815ktfA
kCVDAkEAsPSHFL/TdJ8U5zQ6Iv7Nmw6jD+Bz9SJZf2emRfZ4K4L61Qu2o8/rXYle
JQgD5pemKvd3oMAOLPsY5SbL3bi5LwJAPAm0V8fnZImI8B2qKWFuKhkYJDM5+/Ar
242uavRPYF8/fFZA4Xh16J9sm95+pLJzpklAGA6I74Cyq8EMRHXpVwJBAJeCoEih
tL6GHS1fRY7REoUJs93NaKJzlHfxeBEuIi66LjDHqvS4pJ9NkfLDniIKlpOTK1BL
y8lFgo7ncVMXl/A=
-----END PRIVATE KEY-----
EOD;
	
	private $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCZdnVYBMeAi0zzVrw4j7HCv5Xf
nSmzHZsASApKOTGymQIW1SfEduWu+lHERuuHg7UABBEyyvSx7a9ghRE2159oDk+s
I+RycaeTpC/TWDQcgS9crdemIPEOo+gfwZqzKmqGExdP6q/weHB+ABhIJMAGTnGJ
N/ieqwJQaQkh/aNwVQIDAQAB
-----END PUBLIC KEY-----
EOD;
	
	
	
	function __construct() {
		parent::__construct();
		$request = \think\Request::instance ();
		$this->getlang ( $request->controller () );
	
	}
	public function index()
	{
		
	}
	//验证token是否过期有效
	public function info()
	{
		
		//msgput(false,null,3,$token);
	//$heads=get_all_headers();//获取自定义头部
		$token=Request::instance()->header('Authorization');
		
	if($token){//解析token
		$tokenmodel=new Token();
		
		$res=$tokenmodel->validtoken($token);
		if(!$res){//如果token无效
			$msg=lang('invalid_token');
			msgput(false,$msg,2);
		}
		//如果token有效返回用户信息
		//$worker=new Workers();
		//$worker->field('name')->where('worker_id',$res['worker_id'])->find();
		msgput(true);
	}else{
		$msg=lang('no_token');
		msgput(false,$msg,2);
	}
	}
	public function getauth(){
		$username=input('?post.username')&&input('post.username')?input('post.username'):null;
		$password=input('?post.password')&&input('post.password')?input('post.password'):null;
		if(!$username||!$password){
			$msg=lang('lack_var');
			msgput(false, $msg, 1);
		}
		//生成新密码
		$worker=new Workers();

		$newpassword=$worker->makepassword($password);
		
		$res=$worker->where('name',$username)->where('password',$newpassword)->find();
		if($res){//如果用户存在
			if(is_https()){
				$prot='https://';
			}else{
				$prot='http://';
			}
			
			
			
			$create_time=time();
			$exprire_time=$create_time+604800;//配置一周到期
			$token = array(
					"iss" => $prot.$_SERVER['HTTP_HOST'],
					"aud" => $prot.$_SERVER['HTTP_HOST'],
					"iat" => $create_time,       //生成时间
					"exp" =>$exprire_time,  //到期时间
					"name"=>$username,
			);
			
		
			JWT::$leeway = 60;
			$jwt = JWT::encode($token, $this->privateKey);//生成jwt
			$tokenmodel=new Token();
			$tokenmodel->data([  //保存到数据库
					'value'  =>  $jwt,
					'worker_id' =>  $res['id'],
					'create_time'=>date('Y-m-d H:i:s',$create_time),
					'expire_time'=>date('Y-m-d H:i:s',$exprire_time)
					]);
			$tokenmodel->save();
			
			$menuarr=$worker->getmenubygroupid($res['group']);
			if(Config::get('app_debug')){
				$fileapi=HTTP_SERVER.'/fileapi/';
				$ueapi=HTTP_SERVER.'/fileapi/';
			}else{
				$fileapi=HTTPS_SERVER.'/ueapi/';
				$ueapi=HTTP_SERVERS.'/ueapi/';
			}
			
			
			//$decoded = JWT::decode($jwt, $key, array('HS256'));
			msgput(true, null, 0,array('token'=>$jwt,'menu'=>$menuarr,'fileapi'=>$fileapi,'ueapi'=>$ueapi));
		}else{
			$msg=lang('pass_error');
			msgput(false, $msg, 2);
		}
		
	}
}

?>