<?php

namespace app\guanli\controller;

use app\guanli\controller\BasicController;
use think\Request;
use think\Loader;
use think\Log;
use app\guanli\model\Workers;
use app\guanli\model\WorkersGroup;
use app\guanli\model\WorkersMenu;
use app\guanli\model\WorkerPermission;

class WorkersController extends BasicController {
	
	
	
	public function index() {
		
	}
	
	/**
	 * 获取用户列表
	 */
	public function getlist() {
		$start = input ( '?post.start' ) && input ( 'post.start' ) ? input ( 'post.start' ) : 0;
		$length = input ( '?post.length' ) && input ( 'post.length' ) ? input ( 'post.length' ) : 25;
		$draw = input ( '?post.draw' ) && input ( 'post.draw' ) ? input ( 'post.draw' ) : 1;
		$name = input ( '?post.name' ) && input ( 'post.name' ) ? input ( 'post.name' ) : '';
		$search = input ( '?post.search' ) && input ( 'post.search' ) ? input ( 'post.search' ) : null;
		$sort = input ( '?post.sort' ) && input ( 'post.sort' ) ? input ( 'post.sort' ) : '';
		$sortway = input ( '?post.sortway' ) && input ( 'post.sortway' ) ? input ( 'post.sortway' ) : '';
		$workermodel = new Workers ();
		$list = $workermodel->getlist ( $start, $length, $search, $sort, $sortway );
		if ($search) {
			$count = $workermodel->getconcount ( $search );
		} else {
			$count = $workermodel->count ();
		}
		
		tablereturn ( true, null, 0, $list, $draw, $count, $count );
	}
	
	/**
	 * 获取详细数据
	 */
	public function getdetail() {
		$id = input ( '?post.id' ) && input ( 'post.id' ) ? ( int ) input ( 'post.id' ) : 0;
		if (! $id) {
			msgput ( false, 'id is empty', 1 );
		}
		$workermodel = new Workers ();
		
		$res = $workermodel->getdetail ( $id );
		msgput ( true, null, 0, $res );
	}
	
	/**
	 * 更新
	 */
	public function save() {
		$data = input ( 'post.' );
		$validate = Loader::validate ( 'WorkersVali' );
		if (! $validate->check ( $data )) { // 验证提交的数据
			
			msgPut ( false, $validate->getError (), 1 );
		}
		Log::record ( var_export ( $data, true ) );
		
		$newmenu = json_encode ( $data ['new_menus'], JSON_NUMERIC_CHECK );
		unset ( $data ['new_menus'] );
		$newpermission = json_encode ( $data ['new_permission'], JSON_NUMERIC_CHECK );
		unset ( $data ['new_permission'] );
		
		$workerfield = array (
				'id',
				'name',
				'password',
				'type',
				'group' 
		);
		$workermodel = new Workers ();
		$workerData = array ();
		foreach ( $workerfield as $key ) {
			if (isset ( $data [$key] ) && ! empty ( $data [$key] )) {
				if ($key == 'password') {
					$data [$key] = $workermodel->makepassword ( $data [$key] );
				}
				$workerData [$key] = $data [$key];
			}
		}
		$workerData ['workers_menu']=$newmenu;
		$workerData['workers_permission']=$newpermission;
		if (isset ( $data ['id'] ) && $data ['id']) { // 如果存在id，更新操作
		                                       // 更新基本信息
			$result = $workermodel->update ( $workerData, [ 
					'id' => ( int ) $data ['id'] 
			] );
		} else {
			if(!$data ['password']){//如果密码为空
				msgPut ( false, lang ( 'password_empty' ), 1 );
			}
			$result = $workermodel->save ( $workerData );
			
			if (! $result) {
				msgPut ( false, lang ( 'add_detail_fail' ), 1 );
			}
		}
		
		// 如果menu的选项有变化，而group未变，则全局修改组信息
		// $workersgroup = new WorkersGroup();
		// $groupObj = $workersgroup->get(['id'=>(int)$data['group']]);
		// $oldmenu = $groupObj->menu;
		// if ($oldmenu != $newmenu) {
		// $workersgroup->update(['menu'=>$newmenu], ['id'=>(int)$data['group']]);
		// }
		
		msgput ( true, null, 0, $result );
	}
	/**
	 * 删除
	 */
	public function delete() {
		if (! Request::instance ()->isDelete ()) { // 判断是否为delete请求
			msgPut ( false, lang ( 'method error' ), 1 );
		}
		$id = ( int ) Request::instance ()->param ( 'id' );
		if (! $id) {
			msgPut ( false, lang ( 'id_is_error' ), 2 );
		}
		$workermodel = new Workers ();
		$workermodel->destroy ( [ 
				'id' => $id 
		] );
		msgput ( true );
	}
	/**
	 * 批量删除
	 */
	public function deletes() {
		if (! Request::instance ()->isDelete ()) { // 判断是否为delete请求
			msgPut ( false, lang ( 'method error' ), 1 );
		}
		$ids = Request::instance ()->param ( 'ids' );
		if (! $ids) {
			msgPut ( false, lang ( 'id_is_error' ), 2 );
		}
		$idarr = json_decode ( $ids, true );
		if (! $idarr) {
			msgPut ( false, lang ( 'json decode ids error' ), 3 );
		}
		msgput ( true );
		$workermodel = new Workers ();
		$workermodel->destroy ( $idarr );
		msgput ( true );
	}
	
	/**
	 * 重置密码
	 * 
	 * @author zhangyuanyuan
	 * @param int $id        	
	 * @return
	 *
	 */
	public function resetpwd() {
		$id = ( int ) Request::instance ()->param ( 'id' );
		if (! $id) {
			msgPut ( false, lang ( 'id_is_error' ), 2 );
		}
		$workermodel = new Workers ();
		$workermodel->resetpwd ( $id );
		msgput ( true );
	}
	
	/**
	 *
	 * @author zhangyuanyuan
	 *         获取管理员类型
	 *        
	 *        
	 */
	public function getworkertypelist() {
		$result = array (
				1 => '超级管理员',
				2 => '普通管理员' 
		);
		msgput ( true, null, 0, $result );
	}
	
	/**
	 * 获取所有分组
	 */
	public function getallgroups() {
		$group = new WorkersGroup ();
		$result = $group->getAllGroups ();
		msgput ( true, null, 0, $result );
	}
	
	/**
	 * 获取所有有效menus
	 */
	public function getallmenus() {
		$menu = new WorkersMenu ();
		$result = $menu->getList ( 1 );
		msgput ( true, null, 0, $result );
	}
	
	/**
	 * 根据group_id获取menus
	 */
	public function getgroupmenus() {
		$result = array ();
		$group_id = ( int ) Request::instance ()->param ( 'group' );
		if (! $group_id) {
			msgput ( true, null, 0, $result );
		}
		$group = new WorkersGroup ();
		$result = $group->get ( [ 
				'id' => ( int ) $group_id 
		] );
		if (isset ( $result->menu ) && ! empty ( $result->menu )) {
			$result = json_decode ( $result->menu );
		}
		msgput ( true, null, 0, $result );
	}
	
	/**
	 * 添加组，并分配菜单权限
	 */
	public function savegroup() {
		$data = input ( 'post.' );
		$data ['menu'] = json_encode ( $data ['menu'], JSON_NUMERIC_CHECK );
		$data ['workers_authority'] = '[1]'; // 这个字段暂时没有用到，当操作者存吧
		$validate = Loader::validate ( 'WorkersGroupVali' );
		if (! $validate->check ( $data )) { // 验证提交的数据
			
			msgPut ( false, $validate->getError (), 1, $data );
		}
		Log::record ( var_export ( $data, true ) );
		
		$group = new WorkersGroup ();
		$groupid = $group->save ( $data );
		msgput ( true, null, 0, $groupid );
	}
	
	/**
	 * 获取组列表
	 */
	public function getgrouplist() {
		$start = input ( '?post.start' ) && input ( 'post.start' ) ? input ( 'post.start' ) : 0;
		$length = input ( '?post.length' ) && input ( 'post.length' ) ? input ( 'post.length' ) : 25;
		$draw = input ( '?post.draw' ) && input ( 'post.draw' ) ? input ( 'post.draw' ) : 1;
		$name = input ( '?post.name' ) && input ( 'post.name' ) ? input ( 'post.name' ) : '';
		$search = input ( '?post.search' ) && input ( 'post.search' ) ? input ( 'post.search' ) : null;
		$sort = input ( '?post.sort' ) && input ( 'post.sort' ) ? input ( 'post.sort' ) : '';
		$sortway = input ( '?post.sortway' ) && input ( 'post.sortway' ) ? input ( 'post.sortway' ) : '';
		$workergroupmodel = new WorkersGroup ();
		$list = $workergroupmodel->getlist ( $start, $length, $search, $sort, $sortway );
		if ($search) {
			$count = $workergroupmodel->getconcount ( $search );
		} else {
			$count = $workergroupmodel->count ();
		}
		
		tablereturn ( true, null, 0, $list, $draw, $count, $count );
	}
	
	/**
	 * 删除组
	 */
	public function deletegroup() {
		if (! Request::instance ()->isDelete ()) { // 判断是否为delete请求
			msgPut ( false, lang ( 'method error' ), 1 );
		}
		$id = ( int ) Request::instance ()->param ( 'id' );
		if (! $id) {
			msgPut ( false, lang ( 'id_is_error' ), 2 );
		}
		$groupmodel = new WorkersGroup ();
		$groupmodel->destroy ( [ 
				'id' => $id 
		] );
		msgput ( true );
	}
	
	/**
	 * 获取组详细数据
	 */
	public function getgroupdetail() {
		$id = input ( '?post.id' ) && input ( 'post.id' ) ? ( int ) input ( 'post.id' ) : 0;
		if (! $id) {
			msgput ( false, 'id is empty', 1 );
		}
		$workergroupmodel = new WorkersGroup ();
		
		$res = $workergroupmodel->getdetail ( $id );
		msgput ( true, null, 0, $res );
	}
	
	/**
	 * 添加组并分配权限
	 */
	public function addmenutogroup() {
		$data = input ( 'post.' );
		$validate = Loader::validate ( 'WorkersGroupVali' );
		if (! $validate->check ( $data )) { // 验证提交的数据
			
			msgPut ( false, $validate->getError (), 1 );
		}
		Log::record ( var_export ( $data, true ) );
		
		$data ['workers_authority'] = '[1]'; // 这个字段暂时没有用到，当操作者存吧
		$data ['menu'] = $data ['menu'] ? json_encode ( $data ['menu'], JSON_NUMERIC_CHECK ) : '';
		
		$workergroupmodel = new WorkersGroup ();
		if (isset ( $data ['id'] ) && $data ['id']) { // 如果存在id，更新操作
		                                       // 更新基本信息
			$result = $workergroupmodel->update ( $data, [ 
					'id' => ( int ) $data ['id'] 
			] );
		} else {
			$result = $workergroupmodel->save ( $data );
			if (! $result) {
				msgPut ( false, lang ( 'add_detail_fail' ), 1 );
			}
		}
		
		msgput ( true, null, 0, $result );
	}
	
	/**
	 * 获取权限列表
	 */
	public function getallpermission() {
		$workerpermodel = new WorkerPermission ();
		$result = $workerpermodel->select ();
		msgput ( true, null, 0, $result );
	}
}

?>