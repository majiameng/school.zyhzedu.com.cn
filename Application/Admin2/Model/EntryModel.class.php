<?php 
namespace Admin2\Model;
use Think\Model;
class EntryModel extends Model{
	// protected $tableName = 'projects_from';
	/*
	public function __construct() {
       $this->autoCheckFields = false;
        parent::__construct();
    }
	*/
	
	/*
	** 招生途径
	*/ 
	
	public function getStudentList($condition,$isemail=false){
		$order = isset($condition['order'])?$condition['order']:'id desc';
		$field = isset($condition['field'])?$condition['field']:'*';
		$where = isset($condition['where'])?$condition['where']:'';
		$limit = isset($condition['limit'])?$condition['limit']:'20';
		
		$list = $this->order($order)
					->where($where)
					->limit($limit)
					->field($field)
					->select();
		if(empty($list)) return array();
		
		foreach($list as $k=>$r){
			$email = '';
			if($r['userid']){				
				$email = D('user')->where('id='.$r['userid'])->getField('email');				
			}
			$list[$k]['email'] = $email?:'';
		}
		return $list;
	}	
	

}
?>