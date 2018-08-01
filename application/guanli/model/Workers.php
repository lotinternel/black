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
	 * 返回列表需要的数据
	 * @param int $page数据开始位置
	 * @param int $num 取几个数据
	 * @param string $search 搜索关键词
	 * @param string $sort排序
	 */
	public function getlist($page=1,$num=25,$search=null,$sort='',$sortway='asc'){
	    $limit=(int)$page.','.(int)$num;
	    $querysql=Db::table($this->table)->alias('w')->field('w.id,w.name,w.type,w.group,wg.name as group_name')->join(TABLE_WORKERS_GROUP .' wg','w.group=wg.id', 'left')->limit($limit);
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
	    $res=Db::table($this->table)->alias('u')->field('u.id, u.id as workers_id,u.name,u.type,u.group,ug.name as group_name,ug.menu')->join(TABLE_WORKERS_GROUP .' ug','u.group=ug.id')->where( $conditionis )->find();
	    if($res){
	        if($res['menu']){
	            $menu_arr = json_decode ( $res ['menu'], true ); // 转化成菜单数组
	            $menus = Db::table(TABLE_WORKERS_MENU)->where('id', 'in', $menu_arr)->select();
	        }else{
	            $menus = array();
	        }
	        $res['menu']=$menus;
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
	
}