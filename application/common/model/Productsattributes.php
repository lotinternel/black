<?php
namespace app\common\model;

use think\Model;
use think\Db;

class Productsattributes extends Model
{
	 protected $table = TABLE_PRODUCTSATTRIBUTES;
	 
	/**
	 * 通过产品id获取产品属性
	 * */
	public  function getproattr($id){
		$list=Db::table($this->table)->alias('pa')->where('products_id',$id)->join(TABLE_PRODUCTSOPTIONS.' pp','pa.options_id=pp.products_options_id','left')->select();
	return $list;
	}
}