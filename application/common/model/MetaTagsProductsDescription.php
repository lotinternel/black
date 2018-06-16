<?php

namespace app\common\model;

use think\Model;
use think\Db;

class MetaTagsProductsDescription extends Model
{
    protected $table = TABLE_META_TAGS_PRODUCTS_DESCRIPTION;
    /**
     * 根据产品id返回meta
     * @param int $product_id 产品id
     * @param int $langid 语言id
     * @return array meta数组
     */
    public function getmetabypid($product_id,$langid=1){
    	return Db::table($this->table)->field('metatags_title,metatags_keywords,metatags_description')->where('products_id',$product_id)->where('language_id',$langid)->find();
    }
    
}
