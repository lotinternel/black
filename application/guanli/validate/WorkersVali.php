<?php
/**
 * Workers验证器
 * */
namespace app\guanli\validate;


use think\Validate;

class WorkersVali extends Validate {
	protected $rule = [
	   'name'=>'require',
	   'type'=>'require',
	   'group'=>'require',
	];
	
}

?>