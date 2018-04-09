<?php

namespace app\guanli\model;

use think\Model;

class Workers extends Model {
	public function makepassword($password) {
		$newpassword = md5 ( base64_encode ( $password ) );
		return $newpassword;
	}
	
	/**
	 *
	 * @param int $group_id
	 *        	组的id
	 */
	public function getmenubygroupid($group_id) {
		$workgroup = new WorkersGroup ();
		$menu_json = $workgroup->field ( 'menu' )->where ( 'id', ( int ) $group_id )->find ();
		$resultmenu = array ();
		if ($menu_json) { // 如果该组存在
			
			$menu_arr = json_decode ( $menu_json ['menu'], true ); // 转化成菜单数组
			
			$workmenu = new WorkersMenu ();
			$allmenu = array ();
			$allmenuobj = $workmenu->where ( 'status', 1 )->select (); // 取出所有菜单
			foreach ( $allmenuobj as $key => $value ) {
				$allmenu [$value ['id']] = $value ['name'];
			}
			foreach ( $menu_arr as $mk => $mv ) {
				if (isset ( $allmenu [$mv] ) && $allmenu [$mv]) {
					$resultmenu [] = $allmenu [$mv];
				}
			}
		}
		return $resultmenu;
	}
}