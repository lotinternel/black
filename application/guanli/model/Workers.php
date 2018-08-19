<?php

namespace app\guanli\model;

use think\Model;
use think\Db;

class Workers extends Model {
    protected $table = TABLE_WORKERS;
    
    private $searchtype = array('id', 'name');
    
    protected $defaultPwd = '888888';  //默认密码，用于重置
    
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
	/**
	 * 根据菜单menu id取菜单项
	 * @param array $ids
	 */
	public function getmenunamebyids($ids){
		$res=Db::table(TABLE_WORKERS_MENU)->whereIn('id',$ids)->where('status',1)->select();
	$returnarr=[];
	foreach($res as $key=>$val){
		$returnarr[]=$val['name'];
	}
	return $returnarr;
	}
	
	/**
	 * 返回列表需要的数据
	 * @param int $page数据开始位置
	 * @param int $num 取几个数据
	 * @param string $search 搜索关键词
	 * @param string $sort排序
	 */
	public function getlist($page=1,$num=25,$search=null,$sort='',$sortway='asc'){
	    $limit=(int)$page.','.(int)$num;
	  
	    $querysql=Db::table($this->table)->alias('w')->field('w.id,w.name,w.type,w.group,wg.group_name')->join(TABLE_WORKERS_GROUP .' wg','w.group=wg.id', 'left')->limit($limit);
		//print_r($querysql);exit();
	    $searchtype = array('w.id','w.name');
	    if($search){
	        foreach($searchtype as $key=>$val){
	            $conditionis [$val] = [
	                'like',
	                '%' . $search . '%'
	            ];
	        }
	        $querysql->whereOr ( $conditionis );
	    }
	    if($sort&&in_array($sortway,array('desc','asc'))){
	        $sorts='';
	        if(in_array($sort, array('id','name'))){
	            $sorts='w.'.$sort;
	        }elseif(in_array($sort, array('id','name'))){
	            $sorts='wg.'.$sort;
	        }
	        if($sorts){
	            $querysql->order($sorts.','.$sortway);
	        }
	    }
	    $result = $querysql->select();

	    return $result;
	}
	/**
	 * 获取符合要求的数据条数
	 */
	public function getconcount($search){
	    if($search){
	        foreach($this->searchtype as $key=>$val){
	            $conditionis [$val] = [
	                'like',
	                '%' . $search . '%'
	            ];
	        }
	        return Db::table($this->table)->whereOr ( $conditionis )->count();
	    }
	    return 0;
	}
	
	
	/**
	 * 根据id获取详情
	 * @param int $id
	 * @return multitype:
	 */
	public function getdetail($id){
	    $conditionis=array('u.id'=>$id);
	    $res=Db::table($this->table)->alias('u')->field('u.id, u.id as workers_id,u.name,u.type,u.group,u.workers_permission as permission,u.workers_menu as menu, ug.group_name as group_name')->join(TABLE_WORKERS_GROUP .' ug','u.group=ug.id')->where( $conditionis )->find();
	    if($res){
	        if($res['menu']){
	            $menu_arr = json_decode ( $res ['menu'], true ); // 转化成菜单数组
	            $menus = Db::table(TABLE_WORKERS_MENU)->where('id', 'in', $menu_arr)->select();
	        }else{
	            $menus = array();
	        }
	        $res['menu']=$menus;
	        if($res['permission']){//如果权限不为空
	        	$permission_arr = json_decode ( $res['permission'], true );
	        	$permissions=Db::table(TABLE_WORKERS_PERMISSION)->where('workers_permission_id', 'in', $permission_arr)->select();
	        }else{
	        	$permissions=array();
	        }	 
	        $res['permission_detail']=$permissions;
	    }
	    return $res;
	}

	/**
	 * 按一定规则生成模型
	 * @return string $full model名称
	 */
	public function makemodel(){
	    $pre='M_';
	    $mid=time();
	    $lat='_'.rand(1,99);
	    $full=$pre.$mid.$lat;
	    return $full;
	}
	
	/**
	 * 重置密码
	 * @param int $id
	 * @return boolean
	 * */
	public function resetpwd($id) {
	    $newPwd = $this->makepassword(md5($this->defaultPwd));
	    return Db::table($this->table)->where('id', $id)->update(array('password'=>$newPwd));
	}
	/**
	 * 检查用户是否有某个权限
	 * @param int $userid
	 * @param string $permission
	 * @return bool
	 */
	public function checkpermission($userid,$permission){
		if(!$userid||!$permission){
			return false;
		}
		
	
		$res=Db::table($this->table)->field('workers_permission,type')->where('id',(int)$userid)->find();
		
		if($res){
			if($res['type']==1){//超级管理员不必检查
				return true;
			}
			
			$permissnamearr=Db::table(TABLE_WORKERS_PERMISSION)->field('workers_permission_id')->where('workers_permission_code',$permission)->find();
			
			if(!$permissnamearr){//说明指定的权限不存在
				return false;
			}
			
			$resarr=json_decode($res['workers_permission'],true);
			if(in_array($permissnamearr['id'],$resarr)){
				return true;
			}
		}
		return false;
	}
	
}