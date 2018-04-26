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
		Db::table($this->table)->where('products_id',$id)->select();
	}
}