<?php

namespace app\guanli\controller;

use app\guanli\controller\BasicController;
use app\common\model\Products;
use app\common\model\Categories;
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
	 * 更新产品属性
	 * */
	public function updateproattr(){
		$pid = input ( '?post.pid' ) && input ( 'post.pid' ) ? input ( 'post.pid' ) : 0;//产品id
		$attrid = input ( '?post.attrid' ) && input ( 'post.attrid' ) ? input ( 'post.attrid' ) : 0;
		if(!$pid||!$attrid){
			msgput(false,lang('require_param'),1);
		}
		
		
	}
}

?>