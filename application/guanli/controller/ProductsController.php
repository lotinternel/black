<?php

namespace app\guanli\controller;

use app\guanli\controller\BasicController;
use app\common\model\Products;
use app\common\model\Categories;
use app\common\model\ProductsAttributes;
use app\common\model\ProductsOptions;
use think\Request;

class ProductsController extends BasicController {


	public function index()
	{
		
	}
	/**
	 * 获取产品列表
	 */
	public function getlist(){
		$start = input ( '?post.start' ) && input ( 'post.start' ) ? input ( 'post.start' ) : 0;
		$length = input ( '?post.length' ) && input ( 'post.length' ) ? input ( 'post.length' ) : 25;
		
		$draw = input ( '?post.draw' ) && input ( 'post.draw' ) ? input ( 'post.draw' ) : 1;
		$search = input ( '?post.search' ) && input ( 'post.search' ) ? input ( 'post.search' ) : null;
		$sort=input ( '?post.sort' ) && input ( 'post.sort' ) ? input ( 'post.sort' ) : '';
		$sortway=input ( '?post.sortway' ) && input ( 'post.sortway' ) ? input ( 'post.sortway' ) : '';
		$productmodel=new Products();
		$list=$productmodel->getlist($start,$length,$search,$sort,$sortway);
		if($search){
			$count=$productmodel->getconcount($search);
		}else{
			$count=$productmodel->count();
		}
		
		tablereturn(true, null, 0, $list, $draw, $count, $count);		 
	}
	/**
	 * 获取产品详细数据
	 * */
	public function getdetail(){
		$id = input ( '?post.id' ) && input ( 'post.id' ) ? (int)input ( 'post.id' ) : 0;
		if(!$id){
			msgput(false,'id is empty',1);
		}
		$productmodel=new Products();
		
		$res=$productmodel->getdetail($id);
		msgput(true,null,0,$res);
	}
	/**
	 * 取产品类目
	 * */
	public function getcateloguelist(){
		$categoriesmodel=new Categories();
		$result=$categoriesmodel->getcatlist();
		
		msgput(true,null,0,$result);
		
	}
	/**
	 * 删除产品属性
	 * */
	public function delproattr(){
		$pid = input ( '?post.pid' ) && input ( 'post.pid' ) ? (int)input ( 'post.pid' ) : 0;//产品id
		$options_id = input ( '?post.options_id' ) && input ( 'post.options_id' ) ? (int)input ( 'post.options_id' ) : 0;
		if(!$pid||!$options_id){
			msgput(false,lang('require_param'),1);
		}
		
		$proamodel=new ProductsAttributes();
		$proamodel->delproattr($pid, $options_id);//删除产品属性
		msgput(true);
	}
	/**
	 * 删除产品子属性
	 */
	public function delproattritem(){
		$pid = input ( '?post.pid' ) && input ( 'post.pid' ) ? (int)input ( 'post.pid' ) : 0;//产品id
		$options_values_id = input ( '?post.options_values_id' ) && input ( 'post.options_values_id' ) ? (int)input ( 'post.options_values_id' ) : 0;
		if(!$pid||!$options_values_id){
			msgput(false,lang('require_param'),1);
		}
		$proamodel=new ProductsAttributes();
		$proamodel->delprovalues($pid, $options_values_id);//删除产品属性
		msgput(true);
	}
	/**
	 * 配置属性图片
	 * 
	 */
	public function setattrimg(){
		$pid = input ( '?post.pid' ) && input ( 'post.pid' ) ? (int)input ( 'post.pid' ) : 0;//产品id
		$options_values_id = input ( '?post.options_values_id' ) && input ( 'post.options_values_id' ) ? (int)input ( 'post.options_values_id' ) : 0;
		$imgsrc=input ( '?post.imgsrc' ) && input ( 'post.imgsrc' ) ? input ( 'post.imgsrc' ) : null;
		if(!$pid||!$options_values_id){
			msgput(false,lang('require_param'),1);
		}
		if(!$imgsrc){
			msgput(false,lang('img param is null'),2);
		}
		
		$proamodel=new ProductsAttributes();
		$proamodel->updateproimg($pid,$options_values_id,$imgsrc);
		
		msgput(true);
	}
	/**
	 * 添加属性值
	 * @param $_POST['options_values_id'] int 属性option id
	 * @param $_POST['options_values'] string 属性值
	 * @param $_POST[pid''] int 产品id
	 * 
	 * */
	public function addoptionvalues(){
		$pid = input ( '?post.pid' ) && input ( 'post.pid' ) ? (int)input ( 'post.pid' ) : 0;//产品id
		$options_values = input ( '?post.options_values' ) && input ( 'post.options_values' ) ? input ( 'post.options_values' ) : null;//属性值string
		$options_id=input ( '?post.options_id' ) && input ( 'post.options_id' ) ? (int)input ( 'post.options_id' ) : 0;//产品属性id
		$options_values_img=input ( '?post.attributes_image' ) && input ( 'post.attributes_image' ) ? input ( 'post.attributes_image' ) : null;//属性值string
		if($options_values_img=='null'){
			$options_values_img=null;
		}
		$attributes_status=input ( '?post.attributes_status' ) && input ( 'post.attributes_status' ) ? (int)input ( 'post.attributes_status' ) : 1;//属性状态
		if(!$pid||!$options_values||!$options_id){
			msgput(false,lang('require_param'),1);
		}
		
		$proamodel=new ProductsAttributes();
		$option_values_arr=$proamodel->getoptionvalue($options_values);
		
		if($option_values_arr){//如果属性值存在
			$attributor_id=$proamodel->addproattrvalue($pid,$options_id,$option_values_arr['products_options_values_id'],$options_values_img,$attributes_status);
			$options_values_id=$option_values_arr['products_options_values_id'];
		}else{
			$options_values_id=$proamodel->addoptionvalue($options_values);
			$attributor_id=$proamodel->addproattrvalue($pid,$options_id,$options_values_id,$options_values_img,$attributes_status);
		}
		$res=array('products_attributes_id'=>$attributor_id,'options_values_id'=>$options_values_id);
		msgput(true,null,0,$res);		
	}
	/**
	 * 添加选项值
	 * 
	 */
	public function addoption(){
		$options_values = input ( '?post.options_values' ) && input ( 'post.options_values' ) ? input ( 'post.options_values' ) : null;//选项名称 string
		$type = input ( '?post.type' ) && input ( 'post.type' ) ? input ( 'post.type' ) : 0;
		$productoptionsmodel=new ProductsOptions();
		$option_id=$productoptionsmodel->getoption($options_values,$type);
		$data=array('products_options_id'=>$option_id);
		msgput(true,null,0,$data);
	}
}

?>