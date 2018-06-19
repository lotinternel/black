<?php

namespace app\guanli\controller;

use app\guanli\controller\BasicController;
use app\common\model\Products;
use app\common\model\Categories;
use app\common\model\ProductsAttributes;
use app\common\model\ProductsOptions;
use think\Request;
use think\Loader;
use app\guanli\validate\ProductsVali;
use app\common\model\ProductsDescription;
use app\common\model\MetaTagsProductsDescription;
use app\common\model\Productimage;
use app\common\model\ProductsToCategories;

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

		$attributes_status=input ( '?post.attributes_status' ) && input ( 'post.attributes_status' ) ? input ( 'post.attributes_status' ) : 'true';//属性状态
		if(!$pid||!$options_values||!$options_id){
			msgput(false,lang('require_param'),1);
		}
		if($attributes_status=='true'||$attributes_status=='1'){
			$attributes_status=1;
		}else{
			$attributes_status=0;
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
	/**
	 * 修改产品属性状态
	 */
	public function chattrstatus(){
		$attr = input ( '?post.attr' ) && input ( 'post.attr' ) ? (int)input ( 'post.attr' ) : 0;//选项名称 string
		$status = input ( '?post.status' ) && input ( 'post.status' ) ? input ( 'post.status' ) : 'true';//状态值
		if(!$attr){
			msgput(false,lang('attributor is null'),1);
		}
		$attrstatu=1;
		if($status=='true'){
			$attrstatu=1;
		}else{
			$attrstatu=0;
		}
		$proamodel=new ProductsAttributes();
		$proamodel->updatestatus($attr,$attrstatu);
		msgput(true);
	}
	/**
	 * 更新产品
	 * 
	 */
	public function save(){
		$data = input ( 'post.' );
		if($data['products_status']=='true'){
			$data['products_status']=1;
		}else{
			$data['products_status']=0;
		}
		$validate = Loader::validate('ProductsVali');
		if(!$validate->check($data)){//验证提交的数据
			
			msgPut(false,$validate->getError(),1);
		}
		
		$productmodel=new Products();
		$productdescmodel=new ProductsDescription();
		$metatagsproductdesc=new MetaTagsProductsDescription();
		$producttocatelogue=new ProductsToCategories();
	
		if(isset($data['products_id'])){//如果存在产品id，更新操作
			$productmodel->updateitembyid($data['products_id'],$data);//更新产品基本信息
			$productdescmodel->updateitemdesc($data['products_id'],$data);//更新产品描述
			$metatagsproductdesc->updatemetabyid($data['products_id'],$data['meta']);//更新产品seo信息
			
			$producttocatelogue->updateprocate($data['products_id'],$data['master_categories_id']);
		
		if(isset($data['image_list'])){//如果配置了图片列表
			$productimage=new Productimage();
			
			$productimage->updateimage($data['products_id'],$data['image_list']);
		}
		}else{
			if(!isset($data['products_model'])||!$data['products_model']){//如果不存在model则配置model
				$data['products_model']=$productmodel->makemodel();
			}
			$productid=$productmodel->saveitem($data);//添加产品基本信息
			if(!$productid){
				msgPut(false,lang('add_detail_fail'),1);
			}
			$data['products_id']=$productid;
			$data['language_id']=1;
			$productdescmodel->savedesc($data);
			
			$metatagsproductdesc->savemeta($data);
			$producttocatelogue->updateprocate($data['products_id'],$data['master_categories_id']);
			if(isset($data['image_list'])){//如果配置图片
				$productimage=new Productimage();
				$productimage->updateimage($data['products_id'],$data['image_list']);//保存多条产品图片数据
			}
		}
		$res=array('products_id'=>$data['products_id']);
		msgput(true,null,0,$res);
	}
}

?>