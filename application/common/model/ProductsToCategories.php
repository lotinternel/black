<?php
namespace app\common\model;

use think\Model;
use think\Db;

class ProductsToCategories extends Model
{
	 protected $table = TABLE_PRODUCTSTOCATEGORIES;
	 
	/**
	 * 根据产品id更新categories id
	 * @param unknown $id
	 * @param unknown $cateid
	 */
	 public function updateprocate($id,$cateid){
	 	
	 	$this->where(['products_id' => $id])->delete();//删除旧的数据
	 		$this->data([
    'products_id'  =>  (int)$id,
    'categories_id' =>  (int)$cateid
]);
	 		$this->save();//保存新的数据
	 	
	 }
	 /**
	  * 
	  */
	 public function savecatelog($data){
	 	$this->data($data);
	 	return $this->allowField(true)->save();
	 	
	 }
	

	
}