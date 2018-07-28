<?php
namespace app\common\model;

use think\Model;
use think\Db;
use think\Env;

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
 public function getlist($page=1,$num=25,$search=null,$sort='',$sortway='asc',$catalogue=0){
 	$limit=(int)$page.','.(int)$num;
 	$searchtype=array('p.products_model','pd.products_name');
 	$querysql=Db::table($this->table)->alias('p')->field('p.products_id,p.products_quantity,p.products_model,p.products_image,p.products_price,pd.products_name')->join(TABLE_PRODUCTS_DESCRIPTION .' pd','p.products_id=pd.products_id and pd.language_id=1')->limit($limit);
 	
 	if($catalogue>0){
 		$querysql->where('p.master_categories_id',$catalogue);
 	}
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
 	foreach($result as $k=>&$v){
 		if(Env::get('site.ssl')){
 			$v['products_image_url']=HTTPS_SERVER.'/images/'.$v['products_image'];
 		}else{
 			$v['products_image_url']=HTTP_SERVER.'/images/'.$v['products_image'];
 		}
 		
 	}
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
 /**
  * 根据产品id获取产品细节
  * @param unknown $id
  * @return multitype:
  */
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
 			$res['full_products_image']=HTTPS_SERVER.'/'.DIR_WS_IMAGES.HTTPS_SERVER.'/'.DIR_WS_IMAGES.$res['products_image'];
 			}else{
 				$res['full_products_image']=HTTP_SERVER.'/'.DIR_WS_IMAGES.$res['products_image'];
 			}
 		}

 		//取产品小图
 		$productimage=new Productimage();
 		$imagearr=$productimage->field(array('id'=>'image_id','image','num'))->where('product_id',$id)->select();
 		if($imagearr){
 			foreach($imagearr as $key=>&$val){
 				$val['fullurl']=CDNDOMAIN.'images'.$val['image'];
 			}
 			$res['image_list']=$imagearr;
 		} 		

 		//获取产品属性
 		$productattributes=new ProductsAttributes();
 		$attrlist=$productattributes->getproattr($id);
 		$res['attr']=$attrlist;
 		$mesttagmodel=new MetaTagsProductsDescription();
 		
 		$metaarr=$mesttagmodel->getmetabypid($id);
 		$res['meta']=$metaarr;
 	}
 	return $res;
 }
 /**
  * 通过id更新产品基本信息
  * @param unknown $id
  * @param unknown $data
  */
 public function updateitembyid($id,$data){
 	$data['products_last_modified']=array('exp', 'NOW()');
 	$this->allowField(['products_type','products_quantity','products_model','products_image','products_price','products_virtual','products_last_modified','products_weight','products_status','master_categories_id','origin_url','commission_rate'])->save($data, ['id' => (int)$id]);
 	
 }
 /**
  * 保存产品基本信息
  * @param array $data
  */
 public function saveitem($data){
 	$data['products_date_added']=$data['products_last_modified']=array('exp', 'NOW()');
 
 	//$this->allowField(['products_type','products_quantity','products_model','products_image','products_price','products_virtual','products_last_modified','products_weight','products_status','master_categories_id','origin_url','commission_rate','products_date_added'])->save($data);
 	$this->data($data);
 	$this->allowField(['products_type','products_quantity','products_model','products_image','products_price','products_virtual','products_last_modified','products_weight','products_status','master_categories_id','origin_url','commission_rate','products_date_added'])->save();
 	return $this->products_id;
 }
 /**
  * 按一定规则生成模型
  * @return string $full model名称
  */
 public function makemodel(){
 	$pre='M_';
 	$mid=time();
 	$lat='_'.rand(1,99);
 	$full=$pre.$mid.$lat;
 	return $full;
 }
 
 /**
  * 根据id删除产品数据
  * @param integer $id
  * @return boolean
  */
 public function deleteit($id){
 	$this::destroy(['products_id' => $id]);//删除基本数据
 	$productdescmodel=new ProductsDescription();
 	$productdescmodel->where('products_id',$id)->delete();//删除产品描述
 	$metatagsproductdesc=new MetaTagsProductsDescription();
 	$metatagsproductdesc->where('products_id',$id)->delete();//删除产品meta tag
 	$producttocatelogue=new ProductsToCategories();
 	$producttocatelogue->where('products_id',$id)->delete();//删除产品和目录的对应关系
 	$proamodel=new ProductsAttributes();
 	$proamodel->where('products_id',$id)->delete();
 	return true;
 }
 /**
  * 批量删除
  * @param array $ids
  * @return boolean
  */
 public function bathdelete($ids){
 	$this::destroy($ids);//删除基本数据
 	$productdescmodel=new ProductsDescription();
 	$productdescmodel->whereIn('products_id',$ids)->delete();//删除产品描述
 	$metatagsproductdesc=new MetaTagsProductsDescription();
 	$metatagsproductdesc->whereIn('products_id',$ids)->delete();//删除产品meta tag
 	$producttocatelogue=new ProductsToCategories();
 	$producttocatelogue->whereIn('products_id',$ids)->delete();//删除产品和目录的对应关系
 	$proamodel=new ProductsAttributes();
 	$proamodel->whereIn('products_id',$ids)->delete();
 	return true;
 }

 
 
}