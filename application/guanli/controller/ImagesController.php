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
use think\Loader;

class ImagesController extends BasicController {
	


	public function index()
	{

	}
	/**
	 * 获取轮播图片列表
	 */
	public function getlist(){
		$this->checkpermission('QI');//检查用户是否有上传文件的权限
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
	/**
	 * 获取图片详细数据
	 * @param int $_POST['id']
	 * */
	public function getdetail(){
		$id = input ( '?post.id' ) && input ( 'post.id' ) ? (int)input ( 'post.id' ) : 0;
		if(!$id){
			msgput(false,'id is empty',1);
		}
		$slidemodel=new Slidepic();
	
		$res=$slidemodel->getdetail($id);
		msgput(true,null,0,$res);
	}
	/**
	 * 保存图片
	 */
	public function save(){
		$data = input ( 'post.' );
		$validate = Loader::validate('ImagesVali');
		if(!$validate->check($data)){//验证提交的数据
				
			msgPut(false,$validate->getError(),1);
		}
		$imagemodel=new Slidepic();
		
		
		$id=$imagemodel->saveitem($data);
		msgput(true,null,0,$id);
	}
	/**
	 * 根据id删除数据
	 */
	public function deleteitem(){
		$id = input ( '?post.id' ) && input ( 'post.id' ) ? (int)input ( 'post.id' ) : 0;
		if(!$id){
			msgput(false,'id is empty',1);
		}
		$imagemodel=new Slidepic();
		$imagemodel->deleteitem($id);
		msgput(true);
	}
	

	
}

?>