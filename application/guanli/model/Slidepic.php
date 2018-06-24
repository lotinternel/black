<?php
namespace app\guanli\model;

use think\Model;
use think\Db;

class Slidepic extends Model
{
	
 protected $table = TABLE_SLIDEPIC;
 
/**
  * 返回列表需要的数据
  * @param int $page数据开始位置
  * @param int $num 取几个数据
  * @param string $search 搜索关键词
  * @param string $sort排序
  */
 public function getlist($page=1,$num=25,$search=null,$sort='',$sortway='asc'){
 	$limit=(int)$page.','.(int)$num;
 	$searchtype=array('alt');
 	$querysql=Db::table($this->table)->limit($limit);
 	
 	if($search){
 		foreach($this->searchtype as $key=>$val){
 			$conditionis [$val] = [
 			'like',
 			'%' . $search . '%'
 					];
 		}
 		$querysql->whereOr ( $conditionis );
 	}
 	if($sort&&in_array($sortway,array('desc','asc'))){
 				
 			$querysql->order($sort.','.$sortway);
 	
 	}
 	$result=$querysql->select();
 	foreach($result as $k=>&$v){
 		if(ENABLE_SSL){
 			$v['full_url']=HTTPS_SERVER.'/images'.$v['image'];
 		}else{
 			$v['full_url']=HTTP_SERVER.'/images'.$v['image'];
 		}
 		
 	}
 return $result;
 }
 
 /**
  * 获取符合要求的数据条数
  * @param string $search搜索关键词
  */
 public function getconcount($search){
 	if($search){
 		
 			$conditionis[] = ['alt',
 			'like',
 			'%' . $search . '%'
 					];
 		
 		return Db::table($this->table)->where ( $conditionis )->count();
 	}
 	return 0;
 }
 
}