<?php
/**
 * 网站前端图片管理
 * @author robert
 *
 */
namespace app\guanli\controller;

use app\guanli\controller\BasicController;
use app\common\model\Products;
use app\common\model\Categories;
use app\guanli\model\Slidepic;
use think\Request;

class ImagesController extends BasicController {
	


	public function index()
	{
		echo '999';
	}
	/**
	 * 获取轮播图片列表
	 */
	public function getlist(){
		$start = input ( '?post.start' ) && input ( 'post.start' ) ? input ( 'post.start' ) : 0;
		$length = input ( '?post.length' ) && input ( 'post.length' ) ? input ( 'post.length' ) : 25;
		
		$draw = input ( '?post.draw' ) && input ( 'post.draw' ) ? input ( 'post.draw' ) : 1;
		$search = input ( '?post.search' ) && input ( 'post.search' ) ? input ( 'post.search' ) : null;
		$sort=input ( '?post.sort' ) && input ( 'post.sort' ) ? input ( 'post.sort' ) : '';
		$sortway=input ( '?post.sortway' ) && input ( 'post.sortway' ) ? input ( 'post.sortway' ) : '';
		
		$slidemodel=new Slidepic();
		$list=$slidemodel->getlist($start,$length,$search,$sort,$sortway);
		if($search){
			$count=$slidemodel->getconcount($search);
		}else{
			$count=$slidemodel->count();
		}
		tablereturn(true, null, 0, $list, $draw, $count, $count);
	}

	
}

?>