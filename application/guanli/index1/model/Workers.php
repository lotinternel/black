<?php
namespace app\index\model;

use think\Model;

class Workers extends Model
{

	public function makepassword($password){
		$newpassword=md5(base64_encode($password));
		return $newpassword;
	}
	
	/**
	 * @param int $group_id 用户所属组的id
	 */
	public function getmenu($group_id){
		
	}
}