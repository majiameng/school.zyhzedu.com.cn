<?php 

namespace Common\Model;
use Think\Model;
class RecruitCompanyModel extends Model{
	
	public function getJob(){
	    $job = D('RecruitJob')->getField('id,name');
		return $job;
		$job = F('_common/job');
		if(empty($job)) {
			$job = $this->saveJobCache();
		}	
		return $job;
	}
	
	public function getCompany(){
 		$company = D('RecruitCompany')->getField('id,name,regionsn,city');
		//return $company;
 		$company = $this->saveCompanyCache();
		$company = F('_common/company');
		if(empty($company)) {
			$company = $this->saveCompanyCache();
		}	
		return $company;
	}
	
}
