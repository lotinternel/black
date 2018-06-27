<?php
/**
 * Products验证器
 * */
namespace app\guanli\validate;


use think\Validate;

class ImagesVali extends Validate {
	protected $rule = [
	'name'=>'require',
	'image'=>'require',
	'flag'=>'require',
	'sort'  => 'min:0',
	];
	
}

?>