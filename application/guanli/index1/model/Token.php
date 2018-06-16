<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Token extends Model
{
	 protected $table = TABLE_TOKEN;
/**
 * @param string $token
 * 验证token是否有效
 */
public function validtoken($token){
	$now=date('Y-m-d H:i:s',time());
	$map['value']=$token;
	$map['expire_time']  = ['>',$now];
	$map['create_time']=['<',$now];
	$res=Db::table($this->table)->field('worker_id')->where($map)->find();
	return $res;
}
	
}