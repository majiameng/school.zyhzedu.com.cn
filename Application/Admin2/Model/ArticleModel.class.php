<?php
namespace Admin2\Model;
use Think\Model;
class ArticleModel extends Model{
    protected $_validate=array(
		//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
		//验证条件 0 存在字段就验证（默认） 1 必须验证 2 不为空的时候验证 
		//验证时间self::MODEL_INSERT或者1 self::MODEL_UPDATE或者2 self::MODEL_BOTH或者3全部情况下验证（默认） 
        array('category_id','require','请选择分类',1),
    	array('title','require','请填写标题',1),
        //array('username','','用户名已经存在！',0,'unique',1),
		//array('password','require','密码不能为空',1,'regex',1),
		//array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
		
	
    );
	
	
	protected $_auto = array (
		  //array('status','1'),  // 新增的时候把status字段设置为1
		  //array('password','md5',3,'function') , // 对password字段在新增和编辑的时候使md5函数处理
		  //array('name','getName',3,'callback'), // 对name字段在新增和编辑的时候回调getName方法
		  //array('update_time','time',2,'function'), // 对update_time字段在更新的时候写入当前时间戳
		  array('editor','getAdmin',3,'callback') , //对editor字段在新增和编辑的时候使用该类的getAdmin方法处理
	);
	
	function getAdmin($a){
        return session('admin.username');
    }
	
	
	
	
    





}