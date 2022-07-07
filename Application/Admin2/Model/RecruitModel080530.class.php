<?php
namespace Admin2\Model;
use Think\Model;

class RecruitModel extends Model{	
	
	public function getCardNo($unit,$job=0){
		 
 			
			$Model = M();
			$company = $Model->query("select * from tp_recruit_company where  id=$unit limit 0,1");
			$company=$company[0];
		 
			
 			$companysn=$company['regionsn'];//县区编号
			
			
			$User = M("Region"); 
			$city = $User->where(" region_id=".$company['city'])->find();
			$citysn=$city['no'];//县区编号
			$citysn2=$city['no2'];//守库押运刚县区编号
			
			//押运刚的地区编号使用另一个
			if ($job==5)
			{
			  $citysn=$citysn2;
			}
			
			//获取当前岗位是第几个岗位
			$map = array('company'=>$unit);
			$gang = D('Recruit')->where($map)->select();
			$gangsn=0;
			foreach ($gang as $k=>$g)
			{
			    if ($g['job']==$job)
				{
				  $gangsn=$k;
				}
			}
	 
			//岗位获取结束
			
			
      	   /* 
			$map = array('unit'=>$unit,'job'=>$job,'status'=>1);
			 */
			
            $map = array('unit'=>$unit,'job'=>$job);
			$map['no'] = array('neq','');
           
      
			$num = D('Resume')->where($map)->count();
 			
			$num=$gangsn*500+$num+1;
 			
			$num="00000".$num;
			$num=substr($num,-5);
			
			return $citysn.$companysn.$num;
 			
			 
	}
	
		public function getCardNo——bake($unit,$job=0){
		if($job==5)	$cityno = F('_common/cityno_'.$job);
		else $cityno = F('_common/cityno');
		
		$unit_list = F('_common/company');
		$company = $unit_list[$unit];
		$city_no = $cityno[$company['city']]['no'];
		$no = $city_no . $company['no'];
		$map = array('unit'=>$unit,'job'=>$job);
		$map['no'] = array('neq','');
		$num = D('Resume')->where($map)->count();
		if($num > 0) {
			$no = D('Resume')->where($map)->max('no');
			$no++;
			// if($num >=500) $no+= 501;
			// else $no++;
			return $no;
		}
		unset($map['job']);
		$total = D('Resume')->where($map)->count();		
		if($total==0) $num++;		
		if($total > 0 && $num==0) {
			$job==5?$num++:$num = 501;	//守库押运5  从0起 			
		}		
		$no .= str_pad($num,5,0,STR_PAD_LEFT);		
		return $no;
	}
	
	/*
	** 生成公司编号
	*/
	public function city_company(){
		$data = F('_common/city_company');
		if(!empty($data)) return $data;
		$unit_list = F('_common/company');
		$info = array();
		foreach($unit_list as $k=>$r){
			$info[$r['city']][$k] = $r;
		}
		foreach($info as $k=>$val) {
			$i = 1;
			foreach($val as $key=>$r){
				$no = str_pad($i,2,0,STR_PAD_LEFT);
				$info[$k][$key]['no'] = $no;
				$i++;
				// D('RecruitCompany')->where(array('id'=>$r['id']))->save(array('no'=>$no));
			}
		}
		F('_common/city_company',$info);
		return $info;
	}
}