<?php 

namespace Common\Model;
use Think\Model;
class RecruitJobModel extends Model{
	
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
 		//$company = D('RecruitCompany')->getField('id,name,regionsn,city');
		//return $company;
 		$company = $this->saveCompanyCache();
		$company = F('_common/company');
		if(empty($company)) {
			$company = $this->saveCompanyCache();
		}	
		return $company;
	}
	
	/*
	** 招聘信息
	*/
	
	public function saveRecuitCache(){		
		$job = $this->getJob();
		$company = $this->getCompany();
		$info = $this->getField('id,company,job,num');
		if(empty($info)) return $info;
		$data = array();
		foreach($info as $k=>$r){			
			$data['parent'][$r['company']] = $company[$r['company']]['name'];
			$data[$r['company']][$r['job']] = $job[$r['job']];
		}
		F('_common/recruit',$data);
		$this->setJs($data);
		unset($info);
		return $data;
	}
	
	public function setJs($list=array())
	{	
		if(!empty($list)){
			foreach($list as $k=>$val){
				$list[$k] = array_map(function($r){
					return urlencode($r);
				},$val);
			}
		}		
		$file       =  WEB_PATH . '/Public/js/recruit_list.js';
		$content = 'var recruitJson = '.urldecode(json_encode($list)).';';
		file_put_contents($file, $content);
	}
	
	/*
	** 招聘职位
	*/	
	
	public function saveJobCache(){
		$job = D('RecruitJob')->getField('id,name');
		F('_common/job',$job);
		return $job;
	}
	
	/*
	** 招聘企业
	*/
	public function saveCompanyCache(){
	    $company = D('RecruitCompany')->getField('id,name,regionsn,city');
		F('_common/company',$company);
		return $company;
	}
	
		public function saveCompanyCache_bake(){
		$company = D('RecruitCompany')->getField('id,name,no,city');
		F('_common/company',$company);
		return $company;
	}
	
	
	/*
	** PDF 导出
	*/
	public function pdf($html){		
		ob_start();		
		// include(dirname(__FILE__).'/res/exemple_zh_cn.php');
		
		// $content = ob_get_clean();

		// convert to PDF
		// require_once(dirname(__FILE__).'/../html2pdf.class.php');
		// vendor('Pdf.html2pdf','','.php');
		vendor('Pdf.html2pdf','','.class.php');		
		try
		{			
			$html2pdf = new \HTML2PDF('P', 'A4', 'en', true, 'UTF-8', array(0, 0, 0, 0));
			$html2pdf->setDefaultFont('javiergb');
			//$html2pdf->pdf->SetProtection(array('print'), 'spipu');设置密码
			$html2pdf->pdf->SetDisplayMode('fullpage');
			
			$html2pdf->writeHTML($html);
			$name = '准考证信息'.date('Y-m-d').'.pdf';
			$html2pdf->Output($name,'D');
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}
}
