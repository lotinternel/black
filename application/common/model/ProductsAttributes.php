<?php
namespace app\common\model;

use think\Model;
use think\Db;

class ProductsAttributes extends Model
{
	 protected $table = TABLE_PRODUCTSATTRIBUTES;
	 
	/**
	 * 通过产品id获取产品属性
	 * */
	public  function getproattr($id){
		$list=Db::table($this->table)->alias('pa')->field('pa.products_attributes_id,pa.options_id,pa.options_values_id,pa.attributes_image,pp.products_options_name,po.products_options_values_name,pa.attributes_status,pp.products_options_type,pot.products_options_types_name')->where('products_id',$id)->join(TABLE_PRODUCTSOPTIONS.' pp','pa.options_id=pp.products_options_id','left')->join(TABLE_PRODUCTSOPTIONSVALUES.' po','pa.options_values_id=po.products_options_values_id and po.language_id=1','left')->join(TABLE_PRODUCTSOPTIONSTYPES.' pot','pp.products_options_type=pot.products_options_types_id','left')->order('pa.products_options_sort_order')->select();
	$result=array();
	foreach($list as $key=>&$val){
		
	
			if($val['attributes_image']){
				if(ENABLE_SSL){
					$val['fullimage']=HTTPS_SERVER.'/'.DIR_WS_IMAGES.$val['attributes_image'];
				}else{
					$val['fullimage']=HTTP_SERVER.'/'.DIR_WS_IMAGES.$val['attributes_image'];
				}
			
			}else{
				$val['fullimage']=null;
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
	public function addproattrvalue($pid,$options_id,$options_values_id,$options_values_img){
		$data=array('products_id'=>$pid,
				'options_id'=>$options_id,
				'options_values_id'=>$options_values_id,
				''
		);
		
	}
	
	
	

}