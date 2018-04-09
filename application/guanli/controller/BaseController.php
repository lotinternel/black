<?php

namespace app\guanli\controller;

use think\Controller;
use think\Lang;
use think\Cache;
use think\Config;
use think\Request;
class BaseController {
	function __construct() {
		
		$method = $_SERVER['REQUEST_METHOD'];
		
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: Authorization,Content-Type");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		
		if($method == "OPTIONS") {
			die();
		}
		
	}
	
	public function index()
	{

	}
	/**
	 * 载入控制器对应的语言包
	 *
	 * @param
	 *        	$control控制器名
	 *
	 */
	protected function getlang($control) {
		if (! $control) {
			return false;
		}
		$control = strtolower ( $control );
		$langSet = Lang::detect ();
		$cookie_prefix=Config::get('cookie.prefix');
		
		if ($langSet) {
				
			setcookie ( $cookie_prefix."language", $langSet, time () + 3600 * 24 * 30, '/' );
				
			$request = \think\Request::instance ();
			$langdir = APP_PATH . $request->module () . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $control . DIRECTORY_SEPARATOR . $langSet . '.php';
			if (file_exists ( $langdir )) {
				
				Lang::load ( $langdir );
			}
		}
	}
	
}

?>