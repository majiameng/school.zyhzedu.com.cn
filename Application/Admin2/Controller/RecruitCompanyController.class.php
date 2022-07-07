<?php
namespace Admin2\Controller;
use Think\Controller;
class RecruitCompanyController extends AdminBaseController {
	protected $model;
	
	function _initialize() {
		$roleid = session('admin.role_id');
		// $city = session('admin.city');
	}
	
	/**
	** 招聘列表
	**/	
	public function index(){
		$where = $map = array();
		$keyword = I('get.keyword','','safe_replace');
    	if(strlen($keyword) > 0){			
			$map['name'] = array('like',"%".$keyword."%");
			$ids = D('RecruitCompany')->where($map)->getField('id',true);
			if(!empty($ids)) $where['company'] = array('in',$ids);
    	}  
		$model = D('Recruit');
		$count = $model->where($where)->count();//总页数		
    	$page  = $this->page($count, 15);
		$list = $model->where($where)
				->limit($page->firstRow.','.$page->listRows)
				->order('company asc')
				->select();	
        $this->assign('list',$list)
			->assign('job',D('Common/Recruit')->getJob())
			->assign('company',D('Common/Recruit')->getCompany())
			->assign('region',$this->getRegionList())
			->assign('pages', $page->show());
		$this->display('recruit');
	}
	
	/**
	** 招聘信息 -- 新增
	**/	
	public function recruitAdd(){
		$model = D('Recruit');
		if(IS_AJAX) {
			$data['company'] = I('post.company', 0, 'intval');
			$data['num'] = I('post.num',1,'intval');//人数	
			$data['job'] = I('post.job',0,'intval');	
			if(empty($data['company'])) $this->error('请选择招聘科室');
			if(empty($data['job'])) $this->error('请选择招聘岗位');
			if(empty($data['num'])) $this->error('请正确填写人数');
			$map = array(
				'company' 	=> $data['company'],
				'job' 		=> $data['job']
			);
			$info = $model->where($map)->find();
			if(!empty($info)) $this->error('招聘岗位已存在');
			$res = $model->data($data)->add();	
			D('Common/Recruit')->saveRecuitCache();//更新招聘缓存			
    		if($res) $this->success('添加成功！',U('RecruitCompany/index'));
    		$this->error('添加失败！');
			exit;
		}	
        $this->assign('job',D('Common/Recruit')->getJob())
			->assign('company',D('Common/Recruit')->getCompany());
		$this->display('recruitadd');
	}
	
	/**
	** 招聘信息 -- 编辑
	**/
    public function recruitEdit(){
		$model = D('Recruit');
		if(IS_AJAX) {
			$id   = I('post.id',0,'intval');
			$data['company'] = I('post.company', 0, 'intval');
			$data['num'] = I('post.num',1,'intval');
			$data['job'] = I('post.job',0,'intval');//编码
			if(empty($id)) $this->error('该记录不存在');
			if(empty($data['company'])) $this->error('请选择招聘科室');
			if(empty($data['job'])) $this->error('请选择招聘岗位');
			if(empty($data['num'])) $this->error('请正确填写人数');
			$map = array(
				'company' 	=> $data['company'],
				'job' 		=> $data['job'],
				'id' 		=> array('neq',$id),
			);
			$info = $model->where($map)->find();
			if(!empty($info)) $this->error('招聘岗位已存在');
			$map = array('id'=>$id);
			$res = $model->where($map)->save($data);
			D('Common/Recruit')->saveRecuitCache();//更新招聘缓存
    		if($res!==false) $this->success('更新成功！',U('RecruitCompany/index'));
    		$this->error('更新失败！');
			exit;
		}
		$id = I('get.id',0,'intval');
		if(empty($id)) $this->error('该记录不存在');
    	$map = array('id'=>$id);
		$info = $model->where($map)->find();
        $this->assign('info',$info)
			->assign('job',D('Common/Recruit')->getJob())
			->assign('company',D('Common/Recruit')->getCompany());
		$this->display('recruitedit');
    }
	
	/**
	** 招聘信息 -- 编辑
	**/
    public function recruitRemove(){
		$model = D('Recruit');
		if(IS_AJAX) {
			$id = I('post.id','0','intval');
			if(empty($id)) $this->error('该记录不存在');	
			$map = array('id'=>$id);
			$info = $model->where($map)->field('id')->find();
			if(empty($info)) $this->error('该记录不存在');
			$res = $model->where($map)->delete();
			D('Common/Recruit')->saveRecuitCache();//更新招聘缓存	
    		if($res) $this->success('操作成功！');
    		$this->error('操作失败！');
			exit;
		}
    }
	
	/**
	** 招聘公司列表
	**/
    public function company(){
		$where = array();
		$keyword = I('get.keyword','','safe_replace');
    	if(strlen($keyword) > 0){
			$where['name'] = array('like',"%".$keyword."%");
    	}  
		$model = D('RecruitCompany');
		$count = $model->where($where)->count();//总页数		
    	$page  = $this->page($count, 15);
		$list = $model->where($where)
				->limit($page->firstRow.','.$page->listRows)
				->order('listorder asc,city asc')
				->field('id,name,regionsn,city')
				->select();		
        $this->assign('list',$list)
			->assign('region',$this->getRegionList())
			->assign('pages', $page->show());
		$this->display('company');
    }
	
	/**
	** 招聘公司 -- 编辑
	**/
    public function companyEdit(){
		$model = D('RecruitCompany');
		if(IS_AJAX) {
			$id   = I('post.id',0,'intval');
			$data['name'] = I('post.name','','safe_replace');
			$data['city'] = I('post.city',0,'intval');
			$data['regionsn'] = I('post.regionsn','','trim');//编码
			if(empty($id)) $this->error('该记录不存在');
			if(empty($data['name'])) $this->error('请填写科室名称');
			if(empty($data['regionsn'])) $this->error('请填写单位编码');
			$map = array();
			$map['id'] 		 = array('neq',$id);
			$map['regionsn'] = $data['regionsn'];
			$map['city'] 	 = $data['city'];		
			$info = $model->where($map)->field('id')->find();
			if(!empty($info)) $this->error('编码重复');
			$map = array('id'=>$id);
			$res = $model->where($map)->save($data);
			D('Common/Recruit')->saveCompanyCache();//更新单位缓存
    		if($res!==false) $this->success('更新成功！',U('RecruitCompany/company'));
    		$this->error('更新失败！');
			exit;
		}
		$id = I('get.id',0,'intval');
		if(empty($id)) $this->error('该记录不存在');
    	$map = array('id'=>$id);
		$info = $model->where($map)->find();	
        $this->assign('info',$info)
			->assign('region',$this->getRegionList());
		$this->display('companyedit');
    }
	
	/**
	** 招聘公司 -- 新增
	**/
    public function companyAdd(){
		$model = D('RecruitCompany');
		if(IS_AJAX) {
			$data['name'] = I('post.name','','safe_replace');
			$data['city'] = I('post.city',0,'intval');
			$data['regionsn'] = I('post.regionsn','','trim');//编码
			if(empty($data['name'])) $this->error('请填写科室名称');
			if(empty($data['regionsn'])) $this->error('请填写单位编码');
			$map = array();
			$map['regionsn'] = $data['regionsn'];
			$map['city'] 	 = $data['city'];		
			$info = $model->where($map)->field('id')->find();
			if(!empty($info)) $this->error('编码重复');
			$res = $model->data($data)->add();	
			D('Common/Recruit')->saveCompanyCache();//更新单位缓存			
    		if($res) $this->success('添加成功！',U('RecruitCompany/company'));
    		$this->error('添加失败！');
			exit;
		}	
        $this->assign('region',$this->getRegionList());
		$this->display('companyadd');
    }
	
	/**
	** 招聘公司 -- 删除
	**/
    public function companyRemove(){
		$model = D('RecruitCompany');
		if(IS_AJAX) {
			$id = I('post.id','0','intval');
			if(empty($id)) $this->error('该记录不存在');	
			$map = array('id'=>$id);
			$info = $model->where($map)->field('id')->find();
			if(empty($info)) $this->error('该记录不存在');
			$res = $model->where($map)->delete();
			D('Common/Recruit')->saveCompanyCache();//更新单位缓存			
    		if($res) $this->success('操作成功！');
    		$this->error('操作失败！');
			exit;
		}
    }
	
	/**
	** 招聘岗位列表 
	**/
	public function jobList(){
		$where = array();
		$keyword = I('get.keyword','','safe_replace');
    	if(strlen($keyword) > 0){
			$where['name'] = array('like',"%".$keyword."%");
    	}  
		$model = D('RecruitJob');
		$count = $model->where($where)->count();//总页数		
    	$page  = $this->page($count, 15);
		$list = $model->where($where)
				->limit($page->firstRow.','.$page->listRows)
				->order('listorder asc')				
				->select();		
        $this->assign('list',$list)
			->assign('pages', $page->show());
		$this->display('joblist');
	}
	/**
	** 招聘岗位 -- 编辑
	**/
    public function jobEdit(){
		$model = D('RecruitJob');
		if(IS_AJAX) {
			$id   = I('post.id',0,'intval');
			$data['name'] = I('post.name','','safe_replace');
			$data['desc'] = I('post.desc','','trim');//编码
			if(empty($id)) $this->error('该记录不存在');
			if(empty($data['name'])) $this->error('请填写岗位名称');
		
			$map = array();
			$map['id'] 		 = $id;		
			$info = $model->where($map)->field('id')->find();
			if(empty($info)) $this->error('该记录不存在');
			$map = array('id'=>$id);
			$res = $model->where($map)->save($data);
			D('Common/Recruit')->saveJobCache();//更新岗位缓存
    		if($res!==false) $this->success('更新成功！',U('RecruitCompany/joblist'));
    		$this->error('更新失败！');
			exit;
		}
		$id = I('get.id',0,'intval');
		if(empty($id)) $this->error('该记录不存在');
    	$map = array('id'=>$id);
		$info = $model->where($map)->find();	
        $this->assign('info',$info)
			->assign('region',$this->getRegionList());
		$this->display('jobedit');
    }
	
	/**
	** 招聘岗位 -- 新增
	**/
    public function jobAdd(){
		$model = D('RecruitJob');
		if(IS_AJAX) {
			$data['name'] = I('post.name','','safe_replace');
			$data['desc'] = I('post.desc','','trim');//编码
			if(empty($data['name'])) $this->error('请填写岗位名称');
			$res = $model->data($data)->add();	
			D('Common/Recruit')->saveJobCache();//更新岗位缓存			
    		if($res) $this->success('添加成功！',U('RecruitCompany/joblist'));
    		$this->error('添加失败！');
			exit;
		}	
		$this->display('jobadd');
    }
	
	/**
	** 招聘岗位 -- 删除
	**/
    public function jobRemove(){
		$model = D('RecruitJob');
		if(IS_AJAX) {
			$id = I('post.id','0','intval');
			if(empty($id)) $this->error('该记录不存在');	
			$map = array('id'=>$id);
			$info = $model->where($map)->field('id')->find();
			if(empty($info)) $this->error('该记录不存在');
			$res = $model->where($map)->delete();	
			D('Common/Recruit')->saveJobCache();//更新岗位缓存
    		if($res) $this->success('操作成功！');
    		$this->error('操作失败！');
			exit;
		}
    }
	
	/**
	** 省市地区
	**/	
	public function region(){
		$map = array();
		//$map['parent_id'] = 10;  //河北省
       $map['parent_id'] =  array('in',array(10,27));  //河北省 天津
		$keyword = I('get.keyword','','safe_replace');
    	if(strlen($keyword) > 0){			
			$map['region_name'] = array('like',"%".$keyword."%");
    	}  
		$model = D('Region');
		$count = $model->where($map)->count();//总页数		
    	$page  = $this->page($count, 15);
		$list = $model->where($map)
				->limit($page->firstRow.','.$page->listRows)
				->order('region_id asc')
				->select();	
        $this->assign('list',$list)
			->assign('pages', $page->show());
		$this->display('region');
	}
	
	/**
	** 省市地区 -- 新增
	**/	
	public function regionAdd(){
		$model = D('Region');$pid = 10; //河北省
		if(IS_AJAX) {
			$data['parent_id'] = I('post.parent_id', 0, 'intval');
			$data['region_name'] = I('post.region_name','','trim');
			$data['no']  = I('post.no','','trim');//编码		
			$data['no2'] = I('post.no2','','trim');//备用编码		
			if(empty($data['region_name'])) $this->error('请填写地区名称');
			if(empty($data['no'])) $this->error('请填写编码');
			$map = array(
				'no' 	=> $data['no'],
			);
			$info = $model->where($map)->field('region_id')->find();
			if(!empty($info)) $this->error('编码已存在');
			if(strlen($data['no2']) > 0 ){ //备用编码 不为空
				$map = array(
					'no2' 	=> $data['no2'],
				);
				$info = $model->where($map)->field('region_id')->find();
				if(!empty($info)) $this->error('备用编码已存在');
			}
			$map = array(
				'region_name' 	=> $data['region_name'],
			);
			$info = $model->where($map)->field('region_id')->find();
			if(!empty($info)) $this->error('地区名称已存在');	
			$res = $model->data($data)->add();
			D('Admin2/Region')->cacheAll();//更新地区缓存	
    		if($res!==false) $this->success('更新成功！',U('RecruitCompany/region'));
    		$this->error('更新失败！');
			exit;
		}
		$map = array('region_id' => $pid);//河北省
		$region = D('Region')->where($map)->getField('region_id,region_name'); //地区列表
        $this->assign('region',$region);
		$this->display('regionadd');
	}
	
	/**
	** 省市地区 -- 编辑
	**/
    public function regionEdit(){
		$model = D('Region');$pid = 10; //河北省
		if(IS_AJAX) {
			$id   = I('post.id',0,'intval');
			// $data['parent_id'] = I('post.parent_id', 0, 'intval');
			// $data['region_name'] = I('post.region_name','','trim');
			$data['no']  = I('post.no','','trim');//编码
			$data['no2'] = I('post.no2','','trim');//备用编码
			if(empty($id)) $this->error('该记录不存在');		
			// if(empty($data['region_name'])) $this->error('请填写地区名称');
			if(empty($data['no'])) $this->error('请填写编码');
			$map = array(
				'no' 		=> $data['no'],
				'region_id' => array('neq',$id),
			);
			$info = $model->where($map)->field('region_id')->find();
			if(!empty($info)) $this->error('编码已存在');
			if(strlen($data['no2']) > 0){//备用编码 不为空
				$map = array(
					'no2' 		=> $data['no2'],
					'region_id' => array('neq',$id),
				);
				$info = $model->where($map)->field('region_id')->find();
				if(!empty($info)) $this->error('备用编码已存在');
			}
			
			/*
			//验证名称是否重复
			$map = array(
				'region_name' 	=> $data['region_name'],
				'region_id' 			=> array('neq',$id),
			);
			$info = $model->where($map)->field('region_id')->find();
			if(!empty($info)) $this->error('地区名称已存在');
			*/
			$map = array('region_id'=>$id);
			$res = $model->where($map)->save($data);
			D('Admin2/Region')->cacheAll();//更新地区缓存	
    		if($res!==false) $this->success('更新成功！',U('RecruitCompany/region'));
    		$this->error('更新失败！');
			exit;
		}
		$id = I('get.id',0,'intval');
		if(empty($id)) $this->error('该记录不存在');
    	$map = array('region_id'=>$id);
		$info = $model->where($map)->find();
		$map = array('region_id' => $pid);//河北省
		$region = D('Region')->where($map)->getField('region_id,region_name'); //地区列表
        $this->assign('info',$info)
			->assign('region',$region);
		$this->display('regionedit');
    }
	
	/**
	** 省市地区 -- 编辑
	**/
    public function regionRemove(){
		$model = D('Region');
		if(IS_AJAX) {
			$id = I('post.id','0','intval');
			if(empty($id)) $this->error('该记录不存在');	
			$map = array('region_id'=>$id);
			$info = $model->where($map)->field('region_id')->find();
			if(empty($info)) $this->error('该记录不存在');
			$res = $model->where($map)->delete();
			D('Admin2/Region')->cacheAll();//更新地区缓存	
    		if($res) $this->success('操作成功！');
    		$this->error('操作失败！');
			exit;
		}
    }
	
	/**
	** 地区列表 pid  默认 10河北省 27天津
	**/
	public function getRegionList($pid = array( 10,27)) {
        $map = array();
        $map = array('parent_id' => $pid);
      	if(is_array($pid)) $map['parent_id'] = array('in', $pid);	
		$list = D('Region')->where($map)->getField('region_id,region_name'); //地区列表
		return $list;
	}
}