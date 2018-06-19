<?php
namespace app\common\model;

use think\Model;
use think\Db;

class Productimage extends Model
{
	
 protected $table = TABLE_PRODUCTIMAGE;
 
 /**
  * 根据产品id更新产品图片
  * @param unknown $id
  * @param unknown $imagelist
  */
 public function updateimage($id,$imagedata){
 	$imagelist=$this->where('product_id',$id)->select();
 	$easyarr=array();
 	foreach($imagedata as $a=>$v){
 		$easyarr[]=$v['image'];
 	}
 	foreach($imagelist as $key=>$val){//先删除不存在的数据
 		if(!in_array($val['image'], $imagelist)){ 			 		
 			$this::destroy(['id' => $val['id']]);
 		}
 	}
 	foreach($imagedata as $a=>$b){
 		$res=$this->where('image',$b['image'])->find();
 		if($res){//存在就更新num
 			$this->save([
 					'num' =>$b['num']
 					],['id' => $res['id']]);
 		}else{//不存在就添加
 			$this->data(['product_id'=>$id,'image'=>$b['image'],'num'=>$b['num']])->isUpdate(false)->save();
 		}
 	}
 	
 }
 /**
  * 保存多条产品图片
  * @param unknown $list
  */
 public function saveproimglist($list){
 	$this->saveAll($list);
//  	foreach($list as $key=>$val){
//  		Db::table($this->table)
//  		->data(['name'=>'tp','score'=>1000])
//  		->insert();
//  	}
 }
 
 

 
}