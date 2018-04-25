<?php
namespace app\common\model;

use think\Model;
use think\Db;

class Products extends Model
{
	
 protected $table = TABLE_PRODUCTS;
 private $pavailsort=array('products_id','products_quantity','products_model','products_price');
 private $pdavailsort=array('products_name');
 private $searchtype=array('p.products_model','pd.products_name');
 
 /**
  * 返回列表需要的数据
  * @param int $page数据开始位置
  * @param int $num 取几个数据
  * @param string $search 搜索关键词
  * @param string $sort排序
  */
 public function getlist($page=1,$num=25,$search=null,$sort='',$sortway='asc'){
 	$limit=(int)$page.','.(int)$num;
 	$searchtype=array('p.products_model','pd.products_name');
 	$querysql=Db::table($this->table)->alias('p')->field('p.products_id,p.products_quantity,p.products_model,p.products_image,p.products_price,pd.products_name')->join(TABLE_PRODUCTS_DESCRIPTION .' pd','p.products_id=pd.products_id and pd.language_id=1')->limit($limit);
 	
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
 		$sorts='';
//  		$pavailsort=array('products_id','products_quantity','products_model','products_price');
//  		$pdavailsort=array('products_name');
 		if(in_array($sort, $this->pavailsort)){
 			$sorts='p.'.$sort;
 		}elseif(in_array($sort, $this->pdavailsort)){
 			$sorts='pd.'.$sort;
 		}
 		if($sorts){
 			$querysql->order($sorts.','.$sortway);
 		}
 	}
 	$result=$querysql->select();
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
 		return Db::table($this->table)->alias('p')->join(TABLE_PRODUCTS_DESCRIPTION .' pd','p.products_id=pd.products_id and pd.language_id=1')->whereOr ( $conditionis )->count();
 	}
 	return 0;
 }
 public function getdetail($id){
 	$conditionis=array('p.products_id'=>$id);
 	$res=Db::table($this->table)->alias('p')->join(TABLE_PRODUCTS_DESCRIPTION .' pd','p.products_id=pd.products_id and pd.language_id=1')->where( $conditionis )->find(); 	
 	if($res){
 		$res['products_description']=htmlspecialchars_decode(stripslashes($res['products_description']), ENT_COMPAT|ENT_SUBSTITUTE);
 		if($res['products_status']){
 			$res['products_status']=true;
 		}else{
 			$res['products_status']=false;
 		}
 		if($res['products_image']){//返回图像绝对地址
 			
 			if(ENABLE_SSL){
 			$res['full_products_image']=HTTP_SERVER.'/'.DIR_WS_IMAGES.HTTPS_SERVER.'/'.DIR_WS_IMAGES.$res['products_image'];
 			}else{
 				$res['full_products_image']=HTTP_SERVER.'/'.DIR_WS_IMAGES.$res['products_image'];
 			}
 		}
 		//获取产品属性
 		$productattributes=new Productsattributes();
 		
 		
 	}
 	return $res;
 }
 
}