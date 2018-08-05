<?php
namespace app\guanli\model;

use think\Db;
use think\Model;

class WorkersGroup extends Model
{
	
 protected $table = TABLE_WORKERS_GROUP;
 
     /**
      * 返回列表需要的数据
      * @param int $page数据开始位置
      * @param int $num 取几个数据
      * @param string $search 搜索关键词
      * @param string $sort排序
      */
     public function getlist($page=1,$num=25,$search=null,$sort='',$sortway='asc'){
         $limit=(int)$page.','.(int)$num;
         $querysql=Db::table($this->table)->limit($limit);
         $searchtype = array('id','name');
         if($search){
             foreach($searchtype as $key=>$val){
                 $conditionis [$val] = [
                     'like',
                     '%' . $search . '%'
                 ];
             }
             $querysql->whereOr ( $conditionis );
         }
         if($sort&&in_array($sortway,array('desc','asc'))){
             $sorts='';
             if(in_array($sort, array('id','name'))){
                 $sorts=$sort;
             }
             if($sorts){
                 $querysql->order($sorts.','.$sortway);
             }
         }
         $result=$querysql->select();
         
         return $result;
     }
     
     /**
      * 获取符合要求的数据条数
      */
     public function getconcount($search){
         if($search){
             foreach($this->searchtype as $key=>$val){
                 $conditionis [$val] = [
                     'like',
                     '%' . $search . '%'
                 ];
             }
             return Db::table($this->table)->whereOr ( $conditionis )->count();
         }
         return 0;
     }
     
     /**
      * 获取所有组，为select提供
      * */
     public function getAllGroups() {
         return Db::table($this->table)->field('id,group_name,menu,workers_authority')->select();
     }
     
     /**
      * 根据id获取详情
      * @param int $id
      * @return multitype:
      */
     public function getdetail($id){
         $conditionis=array('id'=>$id);
         $res=Db::table($this->table)->where( $conditionis )->find();
         if(isset($res['menu']) && $res['menu']){
             $res['menu'] = json_decode ( $res ['menu'], true ); // 转化成菜单数组
         }
         return $res;
     }
}