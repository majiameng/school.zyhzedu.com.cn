
$(document).ready(function(){
	$('#FindByEmail').validate({
		errorElement:"span",
		// ignore:'#remember',
		rules: {
			email:{
				required:true,
				email: true
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
			 email: {
				required : "邮箱不能为空",
				email : " 请输入正确格式的邮箱",
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
			var obj = $('#btnFindEmail');
			$(form).ajaxSubmit({
				type:'post',
				url:'/index.php/home/login/findEmail',
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
	
	$('#UserLogin').validate({
		errorElement:"span",
		// ignore:'#remember',
		rules: {
			username:{
				required:true,
				rangelength : [3, 12]
			},
			userpwd:{
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
			}
		},
		errorPlacement : function(error, element) {
			error.appendTo(element.parent());
		},
		submitHandler:function(form) {
			var obj = $('#loginBtn');
			$(form).ajaxSubmit({
				type:'post',
				url:'/index.php/home/login/login',
				dataType:'json',
				// data:$(form).serialize(),
				// timeout:1000,
				success:function(json){
					if(json.status==1) {
						location.href=json.url;return false;
					}
					layer.tips(json.info,'#loginUsername');
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


	$("#registerUser").validate({
		errorElement:"span",
		// errorClass: "msg-tip",
		rules: {
			username:{
				required:true,
				rangelength : [3, 12],
				checkusername:true,
				remote:{
					url : "index.php/home/register/usernameCheck",
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
				rangelength: [3, 12],
				equalTo: "#userPwd"
			},			
			email: {
				required : true,
				email : true,
				remote : {
					url : "index.php/home/register/emailCheck",
					type : "post",
				}
			},
			mobile : {
				required : true,
				isMobile : true,
				remote : {
					url : "index.php/home/register/checkMobile",
					type : "post",
				}
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
			username:{
				required:'账户不能为空',
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
			mobile : {
				required : "手机号码不能为空",
				isMobile : "请填写正确的手机号码",
				remote 	 : '该手机号码已经注册'
			},
			verifycode : {
				required : "验证码不能为空",
				remote : "验证码不正确"
			}
		},
		errorPlacement : function(error, element) {
			error.appendTo(element.parent());
		}
	});
		/*
	submitHandler: function(form){ 
		  $('#regiserBtn').attr('disabled',true).val('请稍等....');     
	   	  alert(46554);return false;//$(form).ajaxSubmit();     
	   } ,

	 */
	$('#codeCode,.verifyImg').click(function(){
        $(this).attr('src','index.php/home/register/code?t='+Math.random())
    });

	$("#UserLogin").validate({
		rules: {
			logRusername: {
				required : true,
			},
			logRpassword: {
				required : true,
				rangelength : [6, 12]
			}
		},
		messages: {
			logRusername: {
				required : "账号不能为空",				
			},
			logRpassword: {
				required : "密码不能为空",
				rangelength : "密码长度为6~12位"
			}
		},
		errorPlacement : function(error, element) {
			alert(element.attr('class'));
			layer.tips(error,element);
		}
	});
	
});
