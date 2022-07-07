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





$(function () {
    //检查 密码 是否符合正则规则
    $('#ReCord').blur(function () {
        var regu = /^[a-z0-9_-]{6,18}$/
        var re = new RegExp(regu);
        var password = $('#ReCord').val();
        if (re.test(password)){
            layer.tips('格式正确','#ReCord');
        }else{
            layer.tips('格式错误','#ReCord');

        }
    })

    //确认密码
    function checkCordP(){
        var password = $('#ReCord').val();
        var passwordRe = $('#ReCorded').val();
        if( passwordRe != ''){
            if(password==passwordRe){
                return true;
            }else{
                return false;
            }
		}else{
            layer.tips('密码不能为空','#ReCorded');
		}
    }

    $('#ReCorded').blur(function(){

        if(checkCordP()){
            layer.tips('密码正确','#ReCorded');
        }else{
            layer.tips('密码错误','#ReCorded');
        }
    })


    //提交修改密码

    $('#btnReCord').click(function () {
        if(checkCordP()){
            var password = $('#ReCord').val();
            var userId = $('#getId').val();
            var time = $('#getT').val();

            $.ajax({
                type: 'post',
                url: cordURL,
                data: {password: password,userId:userId,time:time},
                async: false,
                success: function (rs) {
                    if(rs.status == 1){					   
						layer.alert('密码修改成功', {icon: 6});
						setInterval(function(){
							self.location='index.php';
						},1500);                        
					}else{
						layer.alert(rs.info,{icon:5});
						setInterval(function(){
							layer.closeAll();
						},1500);
						
					}
                }
            })

        }
    })
})

