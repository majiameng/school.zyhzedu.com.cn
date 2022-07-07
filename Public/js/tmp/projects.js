/**
 * Created by Administrator on 2017/3/17.
 */
/*验证码*/
$(document).ready(function() {
    $('#code').click(function () {
        $('#code').attr("src", codeUrl + "#" + Math.random())
    })
})
function checkYzm(){
        var istrue = false;
        //alert(132);
        var yzm = $('#yzm').val();
        var length = $('#yzm').val().length;		
        if(length==4){
            $.ajax({
                type:'post',
                url:yzmUrl,
                data:{yzm:yzm},
                async:false,
                success:function(rs){
                    //alert(rs);
                    if(rs == 1){
                        istrue = true;
                    }else{
                        istrue = false;
                    }

                }
            })
        }
        return istrue;



}

//多图上传
$(document).ready(function(){
    //响应文件添加成功事件
    $("#inputfile").change(function(){
        //创建FormData对象
        var data = new FormData();
        //为FormData对象添加数据
        $.each($('#inputfile')[0].files, function(i, file) {
            data.append('upload_file'+i, file);
        });
        $(".loading").show();    //显示加载图片
        //发送数据
        $.ajax({
            url : mutiUrl,
            type:'POST',
            data:data,
            cache: false,
            contentType: false,        //不可缺参数
            processData: false,        //不可缺参数
            success:function(data){
                //alert(data);

                data = $(data).html();
                //第一个feedback数据直接append，其他的用before第1个（ .eq(0).before() ）放至最前面。
                //data.replace(/&lt;/g,'<').replace(/&gt;/g,'>') 转换html标签，否则图片无法显示。
                if($("#feedback").children('img').length == 0) $("#feedback").append(data.replace(/&lt;/g,'<').replace(/&gt;/g,'>'));
                else $("#feedback").children('img').eq(0).before(data.replace(/&lt;/g,'<').replace(/&gt;/g,'>'));
                $(".loading").hide();    //加载成功移除加载图片
            },
            error:function(){
                alert('上传出错');
                $(".loading").hide();    //加载失败移除加载图片
            }
        });
    });
});

$(function () {
    //检查 姓名 是否符合正则规则
    function checkName(){

        var regu = /^([\u4e00-\u9fa5]{1,20}|[a-zA-Z\.\s]{1,20})$/
        var re = new RegExp(regu);
        var name = $('input[name="name"]').val();

        if (re.test(name)){
            //layer.tips('格式正确','#name');
            return true;
        }else{
            //layer.tips('格式错误','#name');
            return false;
        }

    }
    /*$('input[name="name"]').blur(function(){
        //alert(123);
        checkName()

    })*/

    //检查 身份证号 是否符合正则规则
    function checkCard(){

        var regu = /(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}$)/
        var re = new RegExp(regu);
        var name = $("#focusedInputCard").val();

        if (re.test(name)){
            //layer.tips('格式正确','#focusedInputCard');
            return true;
        }else{
            //layer.tips('格式错误','#focusedInputCard');
            return false;
        }

    }
    $('#focusedInputCard').blur(function(){
        //alert(123);
        checkCard()

    })
    //检查 联系电话 是否符合正则规则
    function checkfocusedInputPTel(){//本人电话

        var regu =  /^((0\d{2,3}-\d{7,8})|(1[35784]\d{9}))$/;
        var re = new RegExp(regu);
        var name = $("#focusedInputPTel").val();

        if (re.test(name)){
            //layer.tips('格式正确','#focusedInputPTel');
            return true;
        }else{
            //layer.tips('格式错误','#focusedInputPTel');
            return false;
        }

    }

    function checkfocusedInputFTel(){//父亲电话

        var regu =  /^((0\d{2,3}-\d{7,8})|(1[35784]\d{9}))$/;
        var re = new RegExp(regu);
        var name = $("#fatherTel").val();

        if (re.test(name)){
            //layer.tips('格式正确','#fatherTel');
            return true;
        }else{
            //layer.tips('格式错误','#fatherTel');
            return false;
        }

    }
    function checkfocusedInputMTel(){//母亲电话

        var regu =  /^((0\d{2,3}-\d{7,8})|(1[35784]\d{9}))$/;
        var re = new RegExp(regu);
        var name = $("#matherTel").val();

        if (re.test(name)){
            //layer.tips('格式正确','#matherTel');
            return true;
        }else{
            //layer.tips('格式错误','#matherTel');
            return false;
        }

    }




    //AJAX传值提交库
    $('#submitProjects').click(function(){


        var istrue = false;
        var type = $('input[name="typeid"]').val();
        var name = $('input[name="name"]').val();
        var sex = $('input[name="sex"]:checked').val();
        var card = $('input[name="card"]').val();
        var birth = $('input[name="birth"]').val();
        var personalTel = $('input[name="personalTel"]').val();
        var school = $('input[name="school"]').val();
        var schoolTel = $('input[name="schoolTel"]').val();
        var schoolClass = $('input[name="class"]').val();
        var optionsRadiosinsel = $('input[name="optionsRadiosinsel"]:checked').val();
        var major = $('input[name="major"]').val();
        var optionsRadiosinMajor = $('input[name="optionsRadiosinMajor"]:checked').val();
        var father = $('input[name="father"]').val();
        var fatherTel = $('input[name="fatherTel"]').val();
        var mather = $('input[name="mather"]').val();
        var matherTel = $('input[name="matherTel"]').val();
        var teacher = $('input[name="teacher"]').val();
        var teacherTel = $('input[name="teacherTel"]').val();
        var address = $('input[name="address"]').val();
        var road = $('input[name="road"]:checked').val();
        var roadName = $('input[name="roadName"]').val();
        var otherRoad = $('input[name="otherRoad"]').val();
        /*var email = $('input[name="email"]').val();*/
        var report = $('input[name="repord"]').val();
        var from = $('input[name="from"]').val();
        var score = parseInt($('input[name="score"]').val());
        var agreen = $(':checkbox[name="agreen"]:checked').length;
        var username = $.cookie('username');
        /*alert(username);*/
	var obj = $(this);
	submit_disable(obj, true);
    if(checkName()) {
        if (checkCard()) {
            if (checkfocusedInputFTel() || checkfocusedInputPTel() || checkfocusedInputMTel()) {					
					if(school.length==0) {
						submit_disable(obj, false);
						layer.msg('请填写在读学校',{icon:5});
						return false;
					}
					if(isNaN(score) || score <= 0) {
						submit_disable(obj, false);
						layer.msg('请填写成绩',{icon:5});
						return false;
					}
					if(typeof(road)=='undefined' || road <= 0) {
						submit_disable(obj, false);
						layer.msg('请选择了解学校途径',{icon:5});
						return false;
					}
					if(agreen == 0) {
						submit_disable(obj, false);
						layer.msg('请阅读并同意报名协议',{icon:5});
						return false;
					}
                    if (checkYzm()) {
                        $.ajax({
                            type: 'post',
                            url: url,
							dataType:'json',
                            async: false,
                            data: {
                                type: type,
                                name: name,
                                sex: sex,
                                card: card,
                                birth: birth,
                                personalTel: personalTel,
                                school: school,
                                schoolTel: schoolTel,
                                schoolClass: schoolClass,
                                optionsRadiosinsel: optionsRadiosinsel,
                                major: major,
                                optionsRadiosinMajor: optionsRadiosinMajor,
                                father: father,
                                fatherTel: fatherTel,
                                mather: mather,
                                matherTel: matherTel,
                                teacher: teacher,
                                teacherTel: teacherTel,
                                address: address,
                                road: road,
                                roadName: roadName,
                                otherRoad: otherRoad,
                                /*email: email,*/
                                report: report,
                                from: from,
                                score:score,
                                username:username,
                                typecur:typecur
                            },
                            success: function (rs) {
								if(rs.code==3){
									location.href="index.php/Home/Login/index";
									var istrue = false;
									return false;
								}
								if(rs.code==4){
									layer.msg('您已经报过名',{icon:5});
									setTimeout(function(){										
										location.href= '/';
									},1500);
									var istrue = false;
									return false;
								}
                                if (rs.code == 1) {
									// layer.msg('报名成功 请等待审核',{icon:6});
									$('#baoming-tip h3').html(rs.num);
									layer.config({
										extend: 'default/myskin.css',
									}).open({
										type: 1,
										title: false,
										closeBtn: false,
										shadeClose: true,
										skin: 'mytip-class',
										content: $('#baoming-tip'),
										area: ['422px', '264px']
									});
									setTimeout(function(){									
										location.href= urlLogin;
									},266000);
                                    var istrue = true;
                                } else {
									submit_disable(obj, false);
                                    layer.tips('提交失败', '#submitProjects');
                                    var istrue = false;
                                }

                                //location.href= urlLogin;
                            }
                        })
                    } else {
						submit_disable(obj, false);
						layer.msg('验证码错误',{icon:5});
						// layer.tips('验证码错误', '#submitProjects');
						var istrue = false;
						$('#code').attr('src','index.php/home/Projects/code?t='+Math.random());
                    }

            } else {        
				submit_disable(obj, false);
				layer.msg('本人或父母电话至少填写一项',{icon:5});
				// layer.tips('本人或父母电话至少填写一项', '#submitProjects');
				var istrue = false;
            }
        } else {
			submit_disable(obj, false);
			layer.msg('请正确填写身份证号',{icon:5});
           // layer.tips('请正确填写身份证号', '#submitProjects');
           var istrue = false;
        }
    } else{
		submit_disable(obj, false);
		layer.msg('请正确填写姓名',{icon:5});
		// layer.tips('请正确填写姓名', '#submitProjects');
        var istrue = false;
    }
    })

})

function submit_disable(obj,status){
	if(status) obj.attr('disabled',true).val('请稍等...');
	else obj.attr('disabled',false).val('提交');
}


//判断是否已上传报名信息
$(function () {
    var userID = $.cookie('userID');
	if(typeof(urlTian) == 'undefined') return false;
    $.ajax({
        url : urlTian,
        type:'POST',
        data:{userID:userID,typeID:typeID},
        success:function(rs){
            if(rs == 1){
                $('.pro-tab').css('display','none')
                $('.container_non').css('display','block')
            }else{
                $('.pro-tab').css('display','block')
                $('.container_non').css('display','none')
            }
        }
    })
   
	
})
