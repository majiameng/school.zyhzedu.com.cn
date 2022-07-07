<?php 
namespace Common\Model;
use Think\Model;
class RedisModel extends Model{
	// protected $tableName = 'User_address';
	public $config;
	public function __construct() {
       $this->autoCheckFields = false;
	   $config = array('type'=>'Redis','auth'=>C('REDIS_AUTH'),'prefix'=>'recruit_');
	   if(!class_exists('redis')) {
			$config = array('type'=>'File','prefix'=>'recruit_','expire'=>1800);
	   }
	   $this->config = $config;
        parent::__construct();
		
    }
	public function getCache($name){	
        $redis_pref = C('REDIS_PREF'); //前缀
      	$name = $redis_pref . $name;
		return S($name,'',$this->config);
	}
	
	public function setCache($name,$value){
       $redis_pref = C('REDIS_PREF'); //前缀
      	$name = $redis_pref . $name;
		S($name,$value,$this->config);
		return true;
	}
	public function deleteCache($name){
       $redis_pref = C('REDIS_PREF'); //前缀
      	$name = $redis_pref . $name;
		S($name,null);
		return true;
	}
	
	public function clearCache($name){
		if(!class_exists('redis')) return true;
		$redis = new \Redis();
		$host = C('REDIS_HOST');
		$port = C('REDIS_PORT');
		$pass = C('REDIS_AUTH');
		$con = $redis->connect($host,$port);
		if($con) {
			$redis->auth($pass);
		}
        $redis_pref = C('REDIS_PREF'); //前缀
        $keys = $redis_pref . '*';
		$redis->delete($redis->keys($keys));
		//$redis->flushdb();
		return true;
	}
	
	public function clearData($name){
		$model = new \Think\Model();
		$tables = array(
			//'user',
			'resume',
			'resume_edu',
			'resume_family',
			'resume_skill',
			'resume_work',
		);
		$sql = '';
		foreach($tables as $r){
			if(empty($r))  continue;
			$table = $this->backquote($model->tablePrefix . $r);   
			$sql.="TRUNCATE TABLE {$table};";
		}
		$res = $model->execute($sql);
		return true;
	}
	private function backquote($str) {
        return "`{$str}`";
    }
	
}

?>