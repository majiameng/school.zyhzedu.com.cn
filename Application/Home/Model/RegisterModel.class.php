<?php 
namespace Home\Model;
use Think\Model;
class RegisterModel extends Model{
	
		protected $tableName = 'user';
		function jiami($passwordJiami){
			return md5(md5($passwordJiami).'*&^');
		}
}

?>