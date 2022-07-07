<?php
	
	function password($password){
		return md5(md5($password).'*&^');
	}
	
	/**
	 * 对用户的密码进行加密
	 * @param $password
	 * @param $encrypt //传入加密串，在修改密码时做认证
	 * @return array/password
	 */
	function password1($password, $encrypt='') {
		$pwd = array();
		$pwd['encrypt'] =  $encrypt ? $encrypt : create_randomstr();
		$pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
		return $encrypt ? $pwd['password'] : $pwd;
	}
	/**
	 * 生成随机字符串
	 * @param string $lenth 长度
	 * @return string 字符串
	 */
	function create_randomstr($lenth = 6) {
		return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
	}

	/**
	 * 检查密码长度是否符合规定
	 *
	 * @param STRING $password
	 * @return 	TRUE or FALSE
	 */
	function is_password($password) {
		$strlen = strlen($password);
		if($strlen >= 6 && $strlen <= 20) return true;
		return false;
	}
	
	/*
	** @param type 邮件标题 
	*/
	function user_send_email ($email,$content,$type=1){
		$typeArry = array(
            1=>'找回密码',
            2=>'报名信息审核通知'
        );
		$title	  = $typeArry[$type];
		return send_mail($email,$title,$content);		
	}
	
	/*
	** @param AddAttachment("/var/tmp/file.tar.gz"); // 添加附件
	** @param AddAddress("收件人email","收件人姓名")
	** @param AltBody 邮件正文不支持HTML的备用显示
	*/
	function send_mail($to, $title, $content) {     
		import('Org.Util.Mail.phpmailer');     
		$mail = new PHPMailer(); //实例化		
		$mail->IsSMTP(); // 启用SMTP
		$mail->Host		=	C('MAIL_HOST');
		$mail->SMTPAuth = 	C('MAIL_SMTPAUTH');
		$mail->Username = 	C('MAIL_USERNAME');
		$mail->Password = 	C('MAIL_PASSWORD') ;
		$mail->From 	= 	C('MAIL_FROM');	 //发件人地址
		$mail->FromName =  	C('MAIL_FROMNAME'); //发件人姓名
		$mail->CharSet	=	C('MAIL_CHARSET');
		$mail->Subject 	=	$title; 		//邮件主题
		$mail->Body 	= 	$content; 	//邮件内容
		$mail->WordWrap = 50; 				//设置每行字符长度
		$mail->IsHTML(C('MAIL_ISHTML')); 
		$mail->AddAddress($to);
        $mail->Port = 465;
        $mail->SMTPDebug = false;
        $mail->SMTPSecure = "ssl";
		$mail->AltBody	= strip_tags($content);
		if($mail->Send()) return true;
		return $mail->ErrorInfo;//错误原因

	}
	
	/*
	** 生成随机码
	** @param len 随机码长度
	*/
	function rand_code(){				
		$len = 6;
		$code = mt_rand(10000,999999);
		if(strlen($code)<$len) $code = str_pad($code,$len,mt_rand(0,9));
		return $code;
	}	

	/**
	 * 安全过滤函数
	 *
	 * @param $string
	 * @return string
	 */
	function safe_replace($string) {
		$string = trim($string);
		$string = str_replace('"','&quot;',$string);
		$string = str_replace('<','&lt;',$string);
		$string = str_replace('>','&gt;',$string);		
		$arry 	= array(';','"',"'",'%20','%27','%2527','*','%','{','}','\\','select','delete','insert','truncate','update');
		$string = str_replace($arry,'',$string);
		return $string;
	}
	
	function get_apply_type(){
		$list = F('_common/applyType');
		if(!empty($list)) return $list;
		return D('Common/Cache')->setApplyTypeCache();
		
	}	

	function get_class_cache(){
		$list = F('_common/class');
		if(!empty($list)) return $list;
		return D('Common/Cache')->saveClassCache();
		
	}	

	function get_road_type(){
		return array(1=>'辅导学校老师介绍',2=>'QQ、微信、微博等',3=>'招生机构',4=>'其他',5=>'报纸');
		
	}

	function get_sex_status(){
		return array(1=>'男',0=>'女');
	}

	function get_majortype(){
		return array('理','文');
	}

	function get_accepted_status(){
		return array('未审核','是','否');
	}

	function get_pay_status(){
		return array('否','是');
	}
	
	function get_degree(){
		return	array(
				1=>'学士学位',
				2=>'硕士学位',
				3=>'博士学位',
				4=>'无',
			);
	}
	
	/**
	* 转换字节数为其他单位
	*
	*
	* @param	string	$filesize	字节大小
	* @return	string	返回大小
	*/
	function sizecount($filesize) {
		if ($filesize >= 1073741824) {
			$filesize = round($filesize / 1073741824 * 100) / 100 .' GB';
		} elseif ($filesize >= 1048576) {
			$filesize = round($filesize / 1048576 * 100) / 100 .' MB';
		} elseif($filesize >= 1024) {
			$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
		} else {
			$filesize = $filesize.' Bytes';
		}
		return $filesize;
	}
	
	function birthday($birthday,$now='2017-8-28'){
		if(empty($now)) $now = time();
		else $now = strtotime($now);
		$birthday = str_replace('.','-',$birthday);		
		$birthday = date('Y-m-d',strtotime($birthday));	
		list($year,$month,$day) = explode("-",$birthday);
		$year_diff  = date("Y",$now) - $year;
		$month_diff = date("m",$now) - $month;
		$day_diff   = date("d",$now) - $day;
		if ($day_diff < 0 || $month_diff < 0) $year_diff--;
		
		return $year_diff;
	}
	
	if(!function_exists('array_column')){ 
		function array_column($input, $columnKey, $indexKey=null){
			$columnKeyIsNumber  = (is_numeric($columnKey))? true:false; 
			$indexKeyIsNull     = (is_null($indexKey))? true :false; 
			$indexKeyIsNumber   = (is_numeric($indexKey))? true:false; 
			$result             = array(); 
			foreach((array)$input as $key=>$row){ 
				if($columnKeyIsNumber){ 
					$tmp= array_slice($row, $columnKey, 1); 
					$tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
				}else{ 
					$tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
				} 
				if(!$indexKeyIsNull){ 
					if($indexKeyIsNumber){ 
					  $key = array_slice($row, $indexKey, 1); 
					  $key = (is_array($key) && !empty($key))?current($key):null; 
					  $key = is_null($key)?0:$key; 
					}else{ 
					  $key = isset($row[$indexKey])?$row[$indexKey]:0; 
					} 
				} 
				$result[$key] = $tmp; 
			} 
			return $result; 
		}
	}
	
	/*
	** 格式化时间
	*/
	function format_date($date){
		if(empty($date)) return $date;
		return str_replace('.', '-', $date);
	}
?>