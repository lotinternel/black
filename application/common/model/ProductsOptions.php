<?php

namespace app\common\model;

use think\Model;
use think\Db;

class ProductsOptions extends Model
{
    protected $table = TABLE_PRODUCTSOPTIONS;
    /**
     * 
     * @param unknown $name
     * @param unknown $type
     */
    public function getoption($name,$type){
    	$res=Db::table($this->table)->where('products_options_name',$name)->where('products_options_type',$type)->find();
    	if(!$res){
    		$data=array('language_id'=>1,
    				'products_options_name'=>$name,
    				'products_options_type'=>$type
    		);
    		return $id=Db::table($this->table)->insertGetId($data);
    	}
    	return $res['products_options_id'];
    }
}
