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
