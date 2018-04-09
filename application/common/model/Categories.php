<?php
namespace app\common\model;

use think\Model;
use think\Db;

class Categories extends Model
{
	 protected $table = TABLE_CATEGORIES;
	 protected $categoriesdesc=TABLE_CATEGORIES_DESCRIPTION;
	 
	 public function getcatlist(){
	 	//取出所有子目录
	 	$res=Db::table($this->table)->alias('c')->field('c.categories_id,c.categories_image,c.categories_status,c.parent_id,cd.categories_name')->join($this->categoriesdesc .' cd','c.categories_id=cd.categories_id','LEFT')->where('c.parent_id','<>','0')->select();
	 	$parentlist=$this->getparentlist();
	
	 	foreach($res as $key=>&$val){//加入父类

	 		if(isset($parentlist[$val['parent_id']])){
	 			$val['parent']=$parentlist[$val['parent_id']];
	 		}else{//父目录不存在，则子目录也不显示
	 			unset($val);
	 		}
	 	}
	 	
	 	return $res;
	 }
	 /**
	  *取出所有父目录 
	  * */
	 public function getparentlist(){
	 	$res=Db::table($this->table)->alias('c')->field('c.categories_id,c.categories_image,c.categories_status,c.parent_id,cd.categories_name')->join($this->categoriesdesc .' cd','c.categories_id=cd.categories_id','LEFT')->where('c.parent_id','0')->select();
	 	$result=array();
	 	foreach($res as $key=>$val){
	 		$result[$val['categories_id']]=$val['categories_name'];
	 	}
	 	return  $result;
	 }

	
}