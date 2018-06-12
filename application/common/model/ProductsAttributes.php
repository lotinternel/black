<?php
namespace app\common\model;

use think\Model;
use think\Db;

class ProductsAttributes extends Model
{
	 protected $table = TABLE_PRODUCTSATTRIBUTES;
	protected $productsoptionsvalues_table =TABLE_PRODUCTSOPTIONSVALUES;
	 
	/**
	 * 通过产品id获取产品属性
	 * */
	public  function getproattr($id){
		$list=Db::table($this->table)->alias('pa')->field('pa.products_attributes_id,pa.options_id,pa.options_values_id,pa.attributes_image,pp.products_options_name,po.products_options_values_name,pa.attributes_status,pp.products_options_type,pot.products_options_types_name')->where('products_id',$id)->join(TABLE_PRODUCTSOPTIONS.' pp','pa.options_id=pp.products_options_id','left')->join(TABLE_PRODUCTSOPTIONSVALUES.' po','pa.options_values_id=po.products_options_values_id and po.language_id=1','left')->join(TABLE_PRODUCTSOPTIONSTYPES.' pot','pp.products_options_type=pot.products_options_types_id','left')->order('pa.products_options_sort_order')->select();
	$result=array();
 	
	foreach($list as $key=>&$val){
			if($val['attributes_status']==1){
				$val['attributes_status']=true;
			}else{
				$val['attributes_status']=false;
			}
	
			if($val['attributes_image']){
				if(ENABLE_SSL){
					$val['fullimage']=HTTPS_SERVER.'/'.DIR_WS_IMAGES.$val['attributes_image'];
				}else{
					$val['fullimage']=HTTP_SERVER.'/'.DIR_WS_IMAGES.$val['attributes_image'];
				}
			
			}else{
				$val['fullimage']=null;
			}
			if($val['attributes_status']==1){
				$val['attributes_status']=true;
			}else{
				$val['attributes_status']=false;
			}
	
		$result['attrlist'][$val['options_id']][]=$val;
		$result['optlist'][$val['options_id']]=$val['products_options_name'];
	}
	
	//处理options
// 	$result['optlist']=array();
// 	foreach($result['attrlist'] as $k=>$v ){
// 		$result['optlist'][$result['optlist']]
// 	}
	return $result;
	}
	
	/**
	 * 移除产品属性
	 * @param int $pid products_id 产品ID
	 * @param int $attid products_attributes_id
	 * 
	 * */
	public function delproattr($pid,$attid){
		Db::table($this->table)->where('products_id',(int)$pid)->where('options_id',(int)$attid)->delete();
	return true;
	}
	

	/**
	 *删除产品属性值 
	 *@param int $pid product id 产品id
	 *@param int $options_values_id 产品值id
	 * */
	public function delprovalues($pid, $options_values_id){
		Db::table($this->table)->where('products_id',(int)$pid)->where('options_values_id',(int)$options_values_id)->delete();
		return true;
		
	}
	
	/**
	 * 更新产品属性的图片
	 * @param int $pid 产品id
	 * @param int $option_values_id 产品属性值id
	 * @param string $imgsrc 图片地址
	 */
	public function updateproimg($pid,$option_values_id,$imgsrc){
		$data=array('attributes_image'=>$imgsrc);
		$res=Db::table($this->table)->where('products_id',(int)$pid)->where('products_attributes_id',(int)$option_values_id)->update($data);
		return $res;
	}
	/**
	 * 添加产品属性到属性选项
	 * @param unknown $pid 产品id
	 * @param unknown $option_values_id 产品属性选项id
	 * @param unknown $options_values  产品属性值
	 */
	public function addproattrvalue($pid,$options_id,$options_values_id,$attributes_img,$attributes_status=1){
		$data=array('products_id'=>$pid,
				'options_id'=>$options_id,
				'options_values_id'=>$options_values_id,
				'attributes_image'=>$attributes_img,
				'attributes_status'=>$attributes_status
		);
		$res=Db::table($this->table)->where($data)->find();
		
		if(empty($res)){//如果不存在
			
			$result=Db::table($this->table)->insertGetId($data);
			
			return $result;
		}
	
		return $res['products_attributes_id'];
	}
	/**
	 * 判断属性值是否存在
	 * @param string $vales_name 属性值名
	 * @return array 属性值数组
	 */
	public function getoptionvalue($vales_name){
		$data=array('products_options_values_name'=>$vales_name);
		$res=Db::table($this->productsoptionsvalues_table)->where($data)->find();
		return $res;
	}
	/**
	 * 添加属性值
	 * @param unknown $values_name
	 * @param number $language_id
	 */
	public function addoptionvalue($values_name,$language_id=1){
		$maxproductvalue_id=Db::table($this->productsoptionsvalues_table)->max('products_options_values_id');
		$products_options_values_id=$maxproductvalue_id+1;
		$data=array('products_options_values_id'=>$products_options_values_id,'language_id'=>$language_id,'products_options_values_name'=>$values_name);
		$id=Db::table($this->productsoptionsvalues_table)->insertGetId($data);
		
		return $products_options_values_id;
	}
	/**
	 * 更新产品属性
	 * @param int $attrid 属性 products_attributes_id
	 * @param int $status 
	 */
	public function updatestatus($attrid,$status){
		$data=array('attributes_status'=>$status);
		return Db::table($this->table)->where('products_attributes_id',$attrid)->update($data);
	}
	
	
	

}