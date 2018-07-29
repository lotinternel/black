<?php

namespace app\common\model;

use think\Model;
use think\Db;

class ProductsDescription extends Model
{
    protected $table = TABLE_PRODUCTS_DESCRIPTION;
    /**
     * 根据产品id更新产品标题和描述
     * @param unknown $id
     * @param unknown $data
     */
    public function updateitemdesc($id,$data){
    	if(isset($data['products_description'])&&$data['products_description']&&ENABLE_CDNFILE){
    		$data['products_description']=htmlspecialchars_decode($data['products_description'], ENT_COMPAT);
    		$uploadaws=new UploadAws();
    		
    		$data['products_description']=$uploadaws->process_remote_images($data['products_description']);
    		
    		$data['products_description']=htmlspecialchars($data['products_description'], ENT_COMPAT, CHARSET, TRUE);
    	}    
    		
    	$this->allowField(['products_name','products_description'])->save($data, ['id' => (int)$id]);
    }
    
    public function savedesc($data,$language_id=1){
    	if(!isset($data['language_id'])){
    		$data['language_id']=$language_id;
    	}
    	if(isset($data['products_description'])&&$data['products_description']&&ENABLE_CDNFILE){
    	$uploadaws=new UploadAws();
    	$data['products_description']=$uploadaws->process_remote_images($data['products_description']);
    	}
    	$this->data($data);
    	$this->allowField(['products_id','language_id','products_name','products_description'])->save();
    	
    }
}
