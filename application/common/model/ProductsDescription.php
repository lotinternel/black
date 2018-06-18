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
    	$this->allowField(['products_name','products_description'])->save($data, ['id' => (int)$id]);
    }
}
