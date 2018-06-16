<?php
/**
 * Products验证器
 * */
namespace app\guanli\validate;


use think\Validate;

class ProductsVali extends Validate {
	protected $rule = [
	'products_description'=>'require',
	'products_image'  =>  'require',
	'products_name'=>'require',
	'products_price'=>'require|float',
	'products_quantity'=>'require',
	'products_weight'=>'require|float',
	'products_virtual'=>'number',
	'products_status'=>'number',
	'master_categories_id'=>'number',
	];
	
}

?>