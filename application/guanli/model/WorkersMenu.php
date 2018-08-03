<?php
namespace app\guanli\model;

use think\Model;
use think\Db;

class WorkersMenu extends Model
{
	
 protected $table = TABLE_WORKERS_MENU;
 
     public function getList($status = '') {
         $select = Db::table($this->table);
         if (!empty($status)) $select->where('status', $status);
         $allmenuobj = $select->select();
         
         $allmenu = array();
         foreach ( $allmenuobj as $key => $value ) {
             $allmenu [$value ['id']] = $value;
         }
         return $allmenu;
         
     }
     
     /**
      * 给登录菜单加载用
      * */
     public function getMenus() {
         $allmenuobj = Db::table($this->table)->where('status', 1)->select();
         $allmenu = array();
         foreach ( $allmenuobj as $key => $value ) {
             $allmenu [] = $value['name'];
         }
         return $allmenu;
         
     }
     
}