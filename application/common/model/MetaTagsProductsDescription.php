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
    
    /**
     * 根据产品id更新产品meta
     * @param int $id
     * @param array $meta
     */
    public function updatemetabyid($id,$data,$language_id=1){
    	return $this->allowField(['metatags_title','metatags_keywords','metatags_description'])->save($data, ['products_id' => (int)$id,'language_id'=>$language_id]);
    }
    /**
     * 
     */
    public function savemeta($id,$metadata,$language_id=1){
    	
    	$resdata=array('products_id'=>(int)$id,'language_id'=>$language_id);
    	$resdata['metatags_title']=isset($metadata['metatags_title'])?$metadata['metatags_title']:null;
    	$resdata['metatags_keywords']=isset($metadata['metatags_keywords'])?$metadata['metatags_keywords']:null;
    	$resdata['metatags_description']=isset($metadata['metatags_description'])?$metadata['metatags_description']:null;
    	$this->data($resdata);
    	return $this->allowField(true)->save();
    }
    
}
