<?php

namespace app\guanli\controller;

use app\guanli\controller\BasicController;
use app\common\model\Products;
use app\common\model\Categories;
use think\Request;
use app\guanli\model\Workers;

class FilesController extends BasicController {
	


	public function index()
	{
		
	}
	
	public function upload(){
		
		$this->checkpermission('UF');//检查用户是否有上传文件的权限
		
		$file = request()->file('image');
	
		if($file){
			$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
			if($info){
				// 成功上传后 获取上传信息
				// 输出 jpg
				$extension=$info->getExtension();
				$smaillext=strtolower($extension);
				if(!in_array($smaillext,array('jpg','png','gif'))){
					msgput(false,'file type not support',1);
				}
				// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
				$savefile=$info->getSaveName();
				
				//把文件传输到主服务器目录下
				
				// 输出 42a79759f284b767dfcb2a0197904287.jpg
			$resultfile=$info->getFilename();
			msgput(true,null,0,array('file'=>$resultfile,'fileurl'=>$resultfile));
			
			}else{
				// 上传失败获取错误信息
				$msg=$file->getError();
				msgput(false,$msg,2);
			}
		}
		
	}
	
}

?>