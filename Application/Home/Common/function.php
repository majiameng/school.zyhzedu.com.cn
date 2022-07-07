<?php
	/*
	** 检查审核时间 5分钟内不显示审核结果
	*/
	function check_audit_time($audit_time){
		$remind_time = C('SHOW_AUDIT_EXPIRE'); //5分钟
		if((time()-$audit_time)<=$remind_time) return true;
		return false;
	}
?>