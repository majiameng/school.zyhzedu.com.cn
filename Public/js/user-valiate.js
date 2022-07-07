$(document).ready(function(){
	$('#profileEdit').validate({
		errorElement:"div",
		errorClass:"pwdtip",
		rules: {
			nickname:{
				required : true,
				rangelength : [3, 16],
			},
			tel: {
				required: true,
				isMobile: true,
			},	
			email : {
				required : true,
				email : true,
				/*
				remote : {
					url : "index.php/home/login/emailCheck",
					type : "post",
				}
				*/
			}		
		},
		messages: {
			nickname: {
				required : "昵称不能为空",
				rangelength : "昵称长度为3~16位",
			},
			tel : {
				required:'请输入手机',
				isMobile:'手机号码格式错误',
                //remote:"两次密码输入不一致"
            },
			email : {
				required:'请输入邮箱',
				email:'请输入正确邮箱',				
            }
		},
		errorPlacement : function(error, element) {
			error.appendTo(element.parent());
		},
		submitHandler:function(form) {
			var obj = $('#profile_edit_btn');
			$(form).ajaxSubmit({
				type:'post',
				url:'/index.php/home/user/editper',
				dataType:'json',
				success:function(json){					
					obj.attr('disabled',false).val("保存");
					if(json.status==1) {
						layer.msg('保存成功', {icon: 6});					
						return false;
					}					
					layer.msg(json.info, {icon: 5});
					return false;
				},   
				beforeSubmit:function(){
					obj.attr('disabled',true).val('请稍等...');
				}
			});
			return false;
		}

	});
	
	$('#user_pwd_edit').validate({
		errorElement:"div",
		errorClass:"pwdtip",
		rules: {
			userpwd:{
				required : true,
				rangelength : [6, 16],
				checkpwd : true
			},
			confirmpwd: {
				required: true,
				rangelength: [6, 16],
				equalTo: "#newpwd"
			},	
			newpwd : {
				required : true,
				checkpwd : true,
				rangelength: [6, 16],
			}		
		},
		messages: {
			userpwd: {
				required : "密码不能为空",
				rangelength : "密码长度为6~12位",
				checkpwd:'密码要求6-13字母或数字'
			},
			confirmpwd : {
				required:'请输入确认密码',
				rangelength:'密码长度为6~12位',
                equalTo:"两次密码输入不一致"
            },
			newpwd : {
				required:'请输入新密码',
				checkpwd:'密码要求6-13字母或数字',
				rangelength:'密码长度为6~12位',
            }
		},
		errorPlacement : function(error, element) {
			error.appendTo(element.parent());
		},
		submitHandler:function(form) {
			var obj = $('#user_pwd_btn');
			$(form).ajaxSubmit({
				type:'post',
				url:'/index.php/home/user/pwdmanage',
				dataType:'json',
				success:function(json){					
					obj.attr('disabled',false).val("保存");
					if(json.status==1) {
						layer.msg('密码修改成功', {icon: 6});
						setTimeout(function(){
							form.reset();
						},1500);
						return false;
					}					
					layer.msg(json.info, {icon: 5});
					return false;
				},   
				beforeSubmit:function(){
					obj.attr('disabled',true).val('请稍等...');
				}
			});
			return false;
		}

	});
	
	$('#UserPwdReset').validate({
		errorElement:"div",
		errorClass:"reg-tishi",
		rules: {
			userpwd:{
				required : true,
				rangelength : [6, 16],
				checkpwd : true
			},
			confirmpwd: {
				required: true,
				rangelength: [6, 16],
				equalTo: "#userpwd"
			},	
			verifycode : {
				required : true,
			}		
		},
		messages: {
			userpwd: {
				required : "密码不能为空",
				rangelength : "密码长度为6~12位",
				checkpwd:'密码要求6-13字母或数字'
			},
			confirmpwd : {
				required:'请输入重复密码',
				rangelength:'密码长度为6~12位',
                equalTo:"两次密码输入不一致"
            },						
			verifycode : {
				required : "请输入验证码",
			}
		},
		errorPlacement : function(error, element) {
			error.appendTo(element.parent().parent());
		},
		submitHandler:function(form) {
			var obj = $('#btnFindEmail');
			$(form).ajaxSubmit({
				type:'post',
				url:'/index.php/home/login/findPassword',
				dataType:'json',
				success:function(json){
					if(json.status==1) {
						layer.msg('密码重置成功 请重新登录', {icon: 6});
						setTimeout(function(){
							location.href='/index.php/home/login/index';
						},1500);
						return false;
					}
					if(json.info.code==2) {
						layer.msg('验证码错误', {icon: 5});
					}else{						
						layer.msg(json.info, {icon: 5});
					}
					$('#captchaimg').click();
					obj.attr('disabled',false).val("找回密码");
					return false;
				},   
				beforeSubmit:function(){
					obj.attr('disabled',true).val('请稍等...');
				}
			});
			return false;
		}

	});
	
	$('#FindByEmail').validate({
		errorElement:"div",
		errorClass:"reg-tishi",
		rules: {
			username:{
				required:true,
				checkusername:true,				
				rangelength : [3, 12],					
			},
			email:{
				required:true,
				email: true
			},
			verifycode : {
				required : true,
			}		
		},
		messages: {
			username:{
				required:'请输入账号',
				checkusername:'请输入3-12字母、数字',				
				rangelength:'请输入3-12字母、数字',
			},
			email: {
				required : "请输入邮箱",
				email : " 请输入正确格式的邮箱",
			},			
			verifycode : {
				required : "请输入验证码",
			}
		},
		errorPlacement : function(error, element) {
			error.appendTo(element.parent().parent());
		},
		submitHandler:function(form) {
			var obj = $('#btnFindEmail');
			$(form).ajaxSubmit({
				type:'post',
				url:'/index.php/home/login/sendEmail',
				dataType:'json',
				success:function(json){
					if(json.status==1) {
						layer.msg('邮件发送成功 请注意查收', {icon: 6});
						setTimeout(function(){
							location.href=location.href;
						},1500);
						return false;
					}
					if(json.info.code==2) {
						layer.msg('验证码错误', {icon: 5});
					}else{						
						layer.msg(json.info, {icon: 5});
					}
					$('#captchaimg').click();
					obj.attr('disabled',false).val("找回密码");
					return false;
				},   
				beforeSubmit:function(){
					obj.attr('disabled',true).val('请稍等...');
				}
			});
			return false;
		}

	});
	
	$('#FindByName').validate({
		errorElement:"span",
		rules: {
			username:{
				required:true,
				rangelength : [3, 12]
			},
			verifycode : {
				required : true,
				remote : {
					url : "index.php/home/register/yzm",
					type : "get",
				}
			}		
		},
		messages: {
			// required : "密码不能为空",
			username:{
				required:'帐户不能为空',
				rangelength:'请输入3-12字母、数字'
			},			
			verifycode : {
				required : "验证码不能为空",
				remote : "验证码不正确"
			}
		},
		errorPlacement : function(error, element) {
			error.appendTo(element.parent());
		},
		submitHandler:function(form) {
			var obj = $('#btnFindName');
			$(form).ajaxSubmit({
				type:'post',
				url:'/index.php/home/login/findName',
				dataType:'json',
				// data:$(form).serialize(),
				// timeout:1000,
				success:function(json){
					if(json.status==1) {
						layer.msg('邮件发送成功 请注意查收', {icon: 6});
						setTimeout(function(){
							location.href=location.href;
						},1500);
						return false;
					}
					layer.msg(json.info, {icon: 5});				
					
					obj.attr('disabled',false).val("找回密码");
					return false;
				},   
				beforeSubmit:function(){
					obj.attr('disabled',true).val('请稍等...');
				}
			});
			return false;
		}

	});
	
	$('#userLogin').validate({
		errorClass:"tishi",
		errorElement:"div",
		// ignore:'#remember',
		rules: {
			username:{
				required:true,
				rangelength : [3, 12]
			},
			userpwd:{
				required : true,
			},
			code:{
				required : true,
			},		
		},
		messages: {
			// required : "密码不能为空",
			username:{
				required:'帐户不能为空',
				rangelength:'请输入3-12字母、数字'
			},			
			userpwd: {
				required : "密码不能为空",
			},			
			code: {
				required : "验证码不能为空",
			}
		},
		errorPlacement : function(error, element) {
			error.appendTo(element.parent().parent());
		},
		submitHandler:function(form) {
			var obj = $('#loginBtn');
			$(form).ajaxSubmit({
				type:'post',
				url:'/index.php/home/login/login',
				dataType:'json',
				success:function(json){
					if(json.status==1) {
						location.href=json.url;return false;
					}
					else if(json.info.code && json.info.code==2){
						layer.msg('验证码错误',{icon:5});						
					}else{						
						layer.msg(json.info,{icon:5});
					}
					$('#captchaimg').click();
					obj.attr('disabled',false).val("登录");
					return false;
				},   
				beforeSubmit:function(){
					obj.attr('disabled',true).val('正在登录 请稍等...');
				}
			});
			return false;
		}

	});


	$("#UserRegister").validate({		
		errorElement:"div",
		errorPlacement : function(error, element) {
			error.appendTo(element.parent().parent());
		},
		errorClass: "reg-tishi",
		rules: {
			username:{
				required:true,
				rangelength : [3, 12],
				checkusername:true,
				remote:{
					url : "index.php/home/login/userCheck",
					type : "post",
				}
			},
			userpwd:{
				required : true,
				rangelength : [6, 16],
				checkpwd : true
			},
			confirmpassword: {
				required: true,
				rangelength: [6, 16],
				equalTo: "#userPwd"
			},			
			email: {
				required : true,
				email : true,
				remote : {
					url : "index.php/home/login/emailCheck",
					type : "post",
				}
			},
			verifycode : {
				required : true,
			}
		},
		messages: {
			username:{
				required:'账号不能为空',
				checkusername:'请输入3-12字母、数字',
				rangelength:'请输入3-12字母、数字',
				remote : "该用户名已注册"
			},			
			userpwd: {
				required : "密码不能为空",
				rangelength : "密码长度为6~12位",
				checkpwd:'密码要求6-13字母或数字'
			},
			confirmpassword : {
				required:'请输入确认密码',
				rangelength:'密码长度为6~12位',
                equalTo:"两次密码输入不一致"
            },
            email: {
				required : "邮箱不能为空",
				email : " 请输入正确格式的邮箱",
				remote : "该邮箱已经注册"
			},
			/*
			mobile : {
				required : "手机号码不能为空",
				isMobile : "请填写正确的手机号码",
				remote 	 : '该手机号码已经注册'
			},
			*/
			verifycode : {
				required : "验证码不能为空",
				remote : "验证码不正确"
			}
		},
		submitHandler:function(form) {
			var obj = $('#regiserBtn');
			$(form).ajaxSubmit({
				type:'post',
				url:'/index.php/home/login/register',
				dataType:'json',
				success:function(json){
					if(json.status==1) {
						layer.msg('注册成功 请登录',{icon:6});						
						setTimeout(function(){
							location.href=json.url;
						},2000);
						return false;
					}
					else if(json.info.code && json.info.code==2){
						layer.msg('验证码错误',{icon:5});						
					}else{						
						layer.msg(json.info,{icon:5});
					}
					$('#captchaimg').click();
					obj.attr('disabled',false).val("注册");
					return false;
				},   
				beforeSubmit:function(){
					obj.attr('disabled',true).val('正在注册 请稍等...');
				}
			});
			return false;
		}
	});
	$('#codeCode,.verifyImg').click(function(){
        $(this).attr('src','index.php/home/register/code?t='+Math.random())
    });
	 $('.captcha').on('click', function(){
		$('#captchaimg').get(0).src='/index.php/home/login/code?r='+Math.random(1, 10000);
	});
});
