$(function(){

    function checkyzm(){
        var istrue = false;
        var yzm = $('#yzmReg').val();
            $.ajax({
                type:'post',
                url:url,
                data:{yzm:yzm},
                async: false,
                success:function(rs){
                    if(rs == 1){
                         istrue = true;
                    }else{
                         istrue = false;
                    }
                }
            })
        return istrue;

    }


    /*function checkYZM(){
        var istrue = false;
        var yzm = $(this).val();
        //alert(123);
        if(yzm !=''){
            $.ajax({
                type:'post',
                url:url,
                data:{yzm:yzm},
                async: false,
                success:function(rs){
                    if(rs == 1){
                        alert()
                        istrue = true;
                        /!*$('#yzmReg').next().html('验证码正确')
                         $('#yzmReg').next().css('color','green')*!/
                    }else{
                        istrue = false;
                        /!* $('#yzmReg').next().html('验证码错误')
                         $('#yzmReg').next().css('color','red')*!/
                    }
                }
            })
        }
    }*/


    /*$('#yzmReg').blur(function(){

        if(checkyzm()){
            layer.tips('验证码正确','#formReg');
        }else{
            layer.tips('验证码错误','#formReg');
        }
    })*/
    $('#yzmReg').focus(function () {


        $('#yzmReg').val("");
    })

    $('.code').click(function(){
        //alert(123);
        $('#codeCode').attr('src',codeReg)
    });
	$(function(){
		clearMsg();
	});
	function clearMsg(){
		$('.regLi li input').focus(function(){
			$(this).siblings('span').html(' ');
		});
	}	
	
    //检查 username 是否符合正则规则
    function checkUsername (){
        var istrue = false;
        var regu = /^[a-z0-9_-]{3,16}$/
        var re = new RegExp(regu);
        var username = $('#regRusername').val();
        var target = $('#regRusername').parent().find('span');
        if(username !=''){
            if (re.test(username)){
                $.ajax({
                    type:'post',
                    url:usernameCheck,
                    data:{username:username},
                    async: false,
                    success:function(rs){
                        //alert(rs);
                        if(rs == 0){                         
                           /* layer.tips('格式正确','.username');*/
                            istrue = true;
                        }else if(rs==1){
                            target.html('用户名已被占用');
                            /*layer.tips('用户名已被占用','.username');*/
                            istrue = false;
                        }else if(rs==2){
                            target.html('请填写用户名');
                           /* layer.tips('请填写内容','#regRusername');*/

                            istrue = false;
                        }
                    }
                })
                return istrue;
            }else{
                target.html('用户名格式错误');
               /* layer.tips('请重新起名','.username');*/
                istrue = false;
            }
        }else{
            target.html('请填写用户名');
            /*layer.tips('请填写内容','.username');*/
            istrue = false;
        }

    }

    //检查 密码 是否符合正则规则
    function checkPassword(){
        var istrue = false;
        var regu = /^[a-z0-9_-]{6,18}$/
        var re = new RegExp(regu);
        var password = $('#regRpassword').val();
        var target = $('#regRpassword').parent().find('span');
        if (!re.test(password)){
            target.html('密码要求6-13字母或数字');
			istrue = false;
            return false;
        }
		return true;
    }

    //确认密码
    $('#passwordRe').blur(function(){
        //alert(123);
        var password = $('#regRpassword').val();
        var passwordRe = $(this).val();
		var target = $(this).parent().find('span');
        //var username = $('#userspan').html();
        if(password!=passwordRe){
           target.html('两次密码不一致');
		   istrue = false;
        }
    })

    //检查 tel 是否符合正则规则
    function checkTel(){

        var regu = /^1[35846]\d{9}$/
        var re = new RegExp(regu);
        var tel = $('#regRtel').val();
		var target = $('#regRtel').parent().find('span');
        //alert(tel)
        if (!re.test(tel)){			
			target.html('手机格式错误');
			istrue = false;
            return false;
        }
		return true;
    }

    //检查 email 是否符合正则规则
    function checkEmail (){
        var istrue = false;
        var regu = /^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/
        var re = new RegExp(regu);
        var email = $('#regRemail').val();
        var target = $('#regRemail').parent().find('span');
        if(email !=''){
            if (re.test(email)){
                $.ajax({
                    type:'post',
                    url:emailCheck,
                    data:{email:email},
                    async: false,
                    success:function(rs){
                        //alert(rs);
                        if(rs == 0){
                            istrue = true;
                        }else if(rs==1){
                            target.html('此邮箱已被注册过');
                            /*layer.tips('此邮箱已被注册过','.email');*/
                            istrue = false;
                        }else if(rs==2){
                            target.html('请填写邮箱');
                           /* layer.tips('请填写内容','.email');*/
                            istrue = false;
                        }
                    }
                })
                return istrue;
            }else{
                target.html('邮箱格式错误');
              /*  layer.tips('格式错误','.email');*/
                istrue = false;
				return false;
            }
        }else{
            target.html('邮箱不能为空');
            /*layer.tips('不能为空','.email');*/
            istrue = false;
			return false;
        }

    }




    //AJAX传值提交库
    $('#formReg').click(function(){
        var istrue = false;
        var username = $('#regRusername').val();
        var password = $('#regRpassword').val();
        var passwordRe = $('#passwordRe').val();
        var tel = $('#regRtel').val();
        var mail = $('#regRemail').val();
        /*var sheng = $('.sheng').val();
        var city = $('.city').val();
        var area = $('.area').val();*/


            if(checkUsername() && checkPassword() &&checkEmail()&&checkTel() ){

                if(checkyzm()){

                    if(passwordRe == password){
                        $.ajax({
                            type:'post',
                            url:urlSubmit,
                            async: false,
                            data:{username:username,password:password,tel:tel,email:mail},
                            success:function(rs){
                                layer.tips('提交成功','#formReg');
                                parent.location.reload()
                                var istrue = true;

                            }
                        })
                    }else{
						$('#passwordRe').parent().find('span').html('确认密码不正确');
                        $('#passwordRe').val('');
                        $('select').val('');
                        $('span').text('');
                        var istrue = false;
                    }
                }else{
					$('#yzmReg').parent().find('span').html('验证码错误');
                    // layer.tips('验证码错误','#formReg');
                    var istrue = false;
                }

            }else{
			var istrue = false;
		}
    })


    //省市区ajax传值
   /* $('.sheng').change(function(){
        var sheng=$(this).val();
        //alert(123);
        $.ajax({
            type:'post',
            url:urlSheng,
            data:{sheng:sheng},
            dataType:'json',
            success:function(rs){
                console.log(rs);
                //alert(rs);
                var str='';
                var strArea='';
                for(var i=0; i<rs['regSheng'].length; i++){
                    str += '<option value="'+rs['regSheng'][i].region_id+'">'+rs['regSheng'][i].region_name+'</option>';
                }
                for(var v=0; v<rs['regArea'].length; v++){
                    strArea += '<option value="'+rs['regArea'][v].region_id+'">'+rs['regArea'][v].region_name+'</option>';
                }

                $('.city').html(str)
                $('.area').html(strArea)

            }
        })

    })*/
	/*$('.city').change(function(){
	 //alert(123);
	 var city=$(this).val();
	 $.ajax({
	 type:'post',
	 url:urlCity,
	 data:{city:city},
	 dataType:'json',
	 success:function(rs){
	 console.log(rs);
	 //alert(rs);
	 var str='';
	 for(var i=0; i<rs.length; i++){
	 str += '<option value="'+rs[i].region_id+'">'+rs[i].region_name+'</option>';
	 }
	 $('.area').html(str)
	 }
	 })
	 })*/






})
//input :hover
$(document).ready(function() {
        $('.regR input').hover(function() {
            $(this).css("border","1px #67baff solid");
        },function() {
            $(this).css("border","1px #48545c solid");
        });
    });

$(document).ready(function () {

//记住密码对勾
$(function () {
    $("input[type='checkbox']").click(function () {
        var flag=$(this).prop("checked");
        if(!flag){
            $(this).parent().css("background","url(./Public/imgages/shop/check1.png) no-repeat");
        }
        else{
            $(this).parent().css("background","url(./Public/imgages/shop/check2.png) no-repeat");
        }
    })
})



//username 失去焦点后,如果cookie有名字,则自动出现密码
$('#logRusername').blur(function(){
    var user = $(this).val();
    var password = $('#logRpassword').val();
    $.ajax({
        type:'post',
        url:urlRem,
        data:{user:user},
        /*dataType:'json',*/
        success:function(rs) {
            console.log(rs)
            if (rs['error'] == 1) {
                $('#logRpassword').val(rs['mes']);
                $('#che').attr("checked", true);
                $('#che').parent().css("background", "url(./Public/imgages/shop/check2.png) no-repeat");

            } else if (rs['error'] == 0) {
                $('password').val('');
                $('#che').attr("checked", false);
                $('#che').parent().css("background", "url(./Public/imgages/shop/check1.png) no-repeat");

            }
        }
    })
})

})


