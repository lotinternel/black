<?php
namespace app\guanli\model;

use think\Model;
use think\Db;

class Slidepic extends Model
{
	
 protected $table = TABLE_SLIDEPIC;
 protected $searchtype=array('alt','name');
 
/**
  * 返回列表需要的数据
  * @param int $page数据开始位置
  * @param int $num 取几个数据
  * @param string $search 搜索关键词
  * @param string $sort排序
  */
 public function getlist($page=1,$num=25,$search=null,$sort='',$sortway='asc'){
 	$limit=(int)$page.','.(int)$num;
 
 	$querysql=Db::table($this->table)->limit($limit);
 	$conditionis=[];
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
 		
 			foreach($this->searchtype as $key=>$val){
 			$conditionis [$val] = [
 			'like',
 			'%' . $search . '%'
 					];
 		}
 		
 		return Db::table($this->table)->where ( $conditionis )->count();
 	}
 	return 0;
 }
 /**
  * 获取网站图片详细信息
  */
 public function getdetail($id){
 	
 	$res=$this->where('id',(int)$id)->find();
 	if($res){
 		if(ENABLE_SSL){
 			$res['full_url']=HTTPS_SERVER.'/images'.$res['image'];
 		}else{
 			$res['full_url']=HTTP_SERVER.'/images'.$res['image'];
 		}
 	}
 	return $res;
 	
 }
 /**
  * 保存图片数据
  * @param array $item
  * @return integer 新增数据的id
  */
 public function saveitem($item){
 
 	if(isset($item['id'])&&$item['id']){//更新
 		$this->allowField(['name','url','image','flag','sort','alt'])->save($item, ['id' => $item['id']]);
 	return $item['id'];
 	}else{
 		$this->data($item);
 		$this->allowField(true)->isUpdate(false)->save();
 		return $this->id;
 	}
 	
 }
/**
 * 根据id删除图片数据
 * @param int $id 图片数据id
 */
 public function deleteitem(int $id){
 	$this::destroy(['id' => $id]);
 }
 
}