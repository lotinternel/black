<?php
/**
 * Workersgroup验证器
 * */
namespace app\guanli\validate;


use think\Validate;

class WorkersGroupVali extends Validate {
	protected $rule = [
	'name'=>'require',
	'menu'=>'require',
	];
	
}

?>