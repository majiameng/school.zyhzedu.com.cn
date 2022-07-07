<?php
namespace Admin2\Model;
use Think\Model;

class AuthModel extends Model{
    /*
	protected $_validate=array(
        array('name','require','分类名称不能为空',1,'regex',3),
        array('name','','分类名称已经存在！',0,'unique',1),
    );
	*/   
	
	
	public function getAuthList($roleid = 1){
		$list = F('_common/auth_menu_'.$roleid);
		$list = array();
		if(empty($list)){
			$ids = D('Role')->where('id='.$roleid)->getField('auth_ids');
			
			if(empty($ids)) return array();
			$where['id'] = array('in',$ids);
			$info = D('Auth')->where($where)->select();

			foreach($info as $k=>$r){
				$list[$r['id']] = $r;
				$list[$r['id']]['child'] = array();
			}
			foreach($list as $k=>$r){
				if($r['pid']>0) {				
					$list[$r['pid']]['child'][] = &$list[$k];
				}
			}
			F('_common/auth_menu_'.$roleid,$list);
		}
		return $list;
	}
	
	public function getAuthCache(){
		$list = F('_common/auth_list');
		if(empty($list)) {
			$list = $this->order('id asc')->select();			
			F('_common/authlist',$list);
		}
		return $list;		
	}
}