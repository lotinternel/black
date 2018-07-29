<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Log;
use think\App;
// 应用公共文件
function msgput($status, $msg = null, $code = 0, $data = null) {
	header("Content-type: application/json; charset=utf-8");
	echo json_encode ( array (
			'status' => $status,
			'msg' => $msg,
			'code' => ( int ) $code,
			'data' => $data
	) );
	exit ();
}
function is_https() {
	if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
		return true;
	} elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
		return true;
	} elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
		return true;
	}
	return false;
}

/**
 * 获取自定义的header数据
 */
function get_all_headers(){

    // 忽略获取的header数据
    $ignore = array('host','accept','content-length','content-type');

    $headers = array();

    foreach($_SERVER as $key=>$value){
        if(substr($key, 0, 5)==='HTTP_'){
            $key = substr($key, 5);
            $key = str_replace('_', ' ', $key);
            $key = str_replace(' ', '-', $key);
            $key = strtolower($key);

            if(!in_array($key, $ignore)){
                $headers[$key] = $value;
            }
        }
    }

    return $headers;

}
/**
 * datatable return json
 * datatable 返回值封装
 */
function tablereturn($status, $msg = null, $code = 0, $data, $draw, $recordsTotal, $recordsFiltered) {
	die ( json_encode ( array (
			'status' => $status,
			'msg' => $msg,
			'code' => $code,
			'data' => $data,
			'draw' => $draw,
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered
	)
	) );
}
/**
 * 下载图片
 * @param string $image 图片地址
 * @throws Exception
 * @return NULL|string
 */
function download_image($image)
{
	if(!$image||!filter_var($image,FILTER_VALIDATE_URL)){//URL不合法
		return null;
	}
	$time=time();
	//$error_log = DIR_FS_CATALOG . 'logs/uploadimage_'.$time.'.log';//设置错误日志

	try{//下载图片
		$ch = curl_init($image);
		if (!$ch) {
			throw new Exception("Could not init curl.");
		}
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT,60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
			
		if (!$response) {
			throw new Exception("curl_exec error: ". curl_error($ch));
		}
		// Then, after your curl_exec call:
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		curl_close($ch);
		$header = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
		if (!preg_match("/Content-Type: (.*)\r\n/i", $header, $match)) {
			throw new Exception("No content type for image: $image.");
		}
			
		$type = str_replace(";charset=UTF-8","",$match[1]);
			
		switch (strtolower($type)) {

			case "image/gif":
				$ext = "gif";
				break;
			case "image/jpg":
			case "image/jpeg":
				$ext = "jpg";
				break;
			case "image/png":
				$ext = "png";
				break;
			case "image/bmp":
				$ext = "bmp";
				break;
			default:
				$ext = false;
				break;
		}
			
		if($ext!=false){

			$time=time();
			$rand = mt_rand(1,10000).mt_rand(1,1000);
			$day=date('Ymd',time());
			$dirs=$day.'/';

			$new = $dirs.$time. $rand . "." . $ext;

			$base = IMAGE_PATH.$new;
			$dirname = dirname($base);

			if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
				throw new Exception($dirname." create file path faile");
			}
			$fp = fopen($base, "wb");
			if (!$fp) {
				throw new Exception("File: ".$base." Directory not writable.");
			}
			fwrite($fp, $body);
			fclose($fp);
			return $new;
		}else{
			return null;
		}
	}catch(Exception $e){
		$msg=date('Y-m-d H:i:s') . ' upload image error:' . $e->getMessage() . PHP_EOL;
		Log::record($msg);	
			
		//error_log(date('Y-m-d H:i:s') . ' upload image error:' . $e->getMessage() . PHP_EOL, 3, $error_log);
		return false;
	}
	return false;
}
