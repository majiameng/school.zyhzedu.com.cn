$(document).ready(function(){
	// var len = $('#resume-info').length;
	if($('#resume-info').length > 0){
		$('#resume-info').validate({
			// debug:true,
			errorElement:"div",
			errorClass:"col-md-1 err",
			rules: {
				name:{
					required : true,
				},
				idCard:{
					required : true,
					isIdCard : true,
					remote : {
						url : "/index.php/home/public/checkResumeCard",
						type : "post",
					}
				},
				birth:{
					required : true,
				},
				house_address:{
					required : true,
				},
				political_status:{
					required : true,
				},
				nation:{
					required : true,
				},
				unit:{
					required : true,
				},
				job:{
					required : true,
				},
				mobile:{
					required : true,
					isMobile:true,
				},
				email:{
					required : true,
					email : true,
				},
				agree:{
					required : true,
				}	
			},
			messages: {
				name: {
					required : "请填写姓名",
				},
				idCard: {
					required : "请填写身份证",
					isIdCard : '身份证格式错误',
					remote : '身份证已被占用',
				},
				birth: {
					required : "请填写",
				},
				house_address: {
					required : "请填写",
				},
				political_status: {
					required : "请填写",
				},
				nation: {
					required : "请填写民族",
				},
				unit: {
					required : "请选择报考单位",
				},
				job: {
					required : "请选择报考岗位",
				},
				mobile:{
					required : '请填写',
					isMobile : '格式错误',
				},email:{
					required : '请填写',
					email : '格式错误',
				},
				agree: {
					required : "请阅读并同意招聘公告",
				}
			},
			errorPlacement : function(error, element) {
				error.insertAfter(element.parent());
			},		
			submitHandler:function(form) {
				var val = $('#previewImg').val();
				var src = $('#previewImg').data('src');	
				if(val.length==0  && (typeof(src)=='undefined' || src.length==0)) {
					layer.msg('请上传头像', {icon: 5});
					return false;
				}
						
				var obj = $('#resume-btn');
				$(form).ajaxSubmit({
					type:'post',
					url:'/index.php/home/recruit/resume',
					dataType:'json',
					success:function(json){
						if(json.status==1) {
							location.href = json.url;
							/*
							layer.msg('报名成功，请等待审核', {icon: 6});
							setTimeout(function(){
								location.href = json.url;
							},1800);
							*/
							return false;
						}
						layer.msg(json.info, {icon: 5});
						obj.attr('disabled',false).val("保存");
						return false;
					},   
					beforeSubmit:function(){
						obj.attr('disabled',true).val('请稍等...');
					}
				});
				return false;
			}
		});
	}
	$('#save_confirm_btn').click(function(){
		var obj = $(this);
		obj.attr('disabled',true).val('请稍等...');
		$.post('/index.php/home/recruit/confirm',function(json){
			if(json.status==1) {
				layer.msg('报名成功，请等待审核', {icon: 6});
				setTimeout(function(){
					location.href='/index.php/home/review/index';
				},1800);
			}else{
				layer.msg('服务器繁忙 请重试', {icon: 5});
				obj.attr('disabled',false).val("确认并保存");
			}
		});
	});
	 $('.captcha').on('click', function(){
		$('#captchaimg').get(0).src='/index.php/home/login/code?r='+Math.random(1, 10000);
	});
	
});


var Recruit = {
	listInfo: function(pid, target) {
		if(!recruitJson[pid]) return false;
		var val = recruitJson[pid],selectstr='';
		var selectid = $(target).data('select');
		if(!pid) pid = 0;
		var str = '<option value="">请选择</option>';			
		for(var i in val){
			if(i==selectid) {
				selectstr= 'selected';
			}
			else selectstr= '';
			str += '<option value="'+i+'" '+selectstr+'>'+val[i]+'</option>';
		}
		$(target).html(str);
	},
	init: function(obj, childObj){
		Recruit.listInfo('parent', obj);
		Recruit.childSelect(obj, childObj);			
		Recruit.bind(obj, childObj);
	}, 
	childSelect: function(obj, childObj) {		
		var val = $(obj).data('select');
		if(val == undefined) return false;
		Recruit.listInfo(val, childObj);
	},
	bind: function(obj, childObj) {
		$(obj).change(function(){
			var val = $(this).val();
			// $(inputObj).val('');
			Recruit.listInfo(val, childObj);				
		});
	}
};