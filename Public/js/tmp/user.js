$(function () {

    //正则验证密码
    function checkPassword(){
        var regu = /^[a-z0-9_-]{6,18}$/
        var re = new RegExp(regu);
        var password = $('#userPass').val();
        if (re.test(password)){
            return true;
        }else{
            layer.tips('格式错误','#userPass');
            return false;
        }
    }


    //检查 username 是否符合正则规则
    function checkUsername(){
        var istrue = false;
        var regu = /^[a-z0-9_-]{3,16}$/
        var re = new RegExp(regu);
        var username = $('#userName').val();
        var userid= $('input[name="id"]').val();
        if(username !=''){
            if (re.test(username)){
                $.ajax({
                    type:'post',
                    url:usernameCheck,
                    data:{username:username,userid:userid},
                    async: false,
                    success:function(rs){
                        alert(rs);
                        /*if(rs == 0){
                            istrue = true;
                        }else if(rs==1){
                            layer.tips('用户名已被占用','#userName');
                            istrue = false;
                        }else if(rs==2){
                            layer.tips('请填写内容','#userName');
                            istrue = false;
                        }*/
                    }
                })
                return istrue;
            }else{
                layer.tips('请重新起名','#userName');
                istrue = false;
            }
        }else{
            layer.tips('请填写内容','#userName');
            istrue = false;
        }

    }
    $('#userName').blur(function () {
        checkUsername()
    })


    //检证邮箱
    //检查 email 是否符合正则规则
    function checkEmail (){
        var istrue = false;
        var regu = /^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/
        var re = new RegExp(regu);
        var email = $('#userEmail').val();
        var userid= $('input[name="id"]').val();
        if(email !=''){
            if (re.test(email)){

                $.ajax({
                    type:'post',
                    url:emailCheck,
                    data:{email:email,userid:userid},
                    async: false,
                    success:function(rs){
                       /* alert(rs);*/
                        if(rs == 0){
                            istrue = true;
                        }else if(rs==1){
                            layer.tips('此邮箱已被注册过','#userEmail');
                            istrue = false;
                        }else if(rs==2){
                            layer.tips('请填写内容','#userEmail');
                            istrue = false;
                        }
                    }
                })
                return istrue;
            }else{
                layer.tips('格式错误','#userEmail');
                istrue = false;
            }
        }else{
            layer.tips('不能为空','#userEmail');
            istrue = false;
        }

    }


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
    function checkfocusedInputTTel(){//母亲电话

        var regu =  /^((0\d{2,3}-\d{7,8})|(1[35784]\d{9}))$/;
        var re = new RegExp(regu);
        var name = $("#teacherTel").val();

        if (re.test(name)){
            //layer.tips('格式正确','#matherTel');
            return true;
        }else{
            //layer.tips('格式错误','#matherTel');
            return false;
        }

    }

    //提交修改
    $('#btn_user').click(function(){
        var birth = $('#birth').val();
        var address = $('#address').val();
        var school = $('#school').val();
        var classMe = $('#classMe').val();
        var major = $('#major').val();
        var score = $('#score').val();
        var teacher = $('#teacher').val();
        var teacherTel = $('#teacherTel').val();
        var father = $('#father').val();
        var fatherTel = $('#fatherTel').val();
        var mather = $('#mather').val();
        var matherTel = $('#matherTel').val();
        var personalTel = $('#personalTel').val();
        var userID= $.cookie('userID');


        if(checkfocusedInputFTel() || checkfocusedInputPTel() || checkfocusedInputMTel() || checkfocusedInputTTel()){
            $.ajax({
                type:'post',
                url:urlModify,
                data:{userID:userID,
                    birth:birth,
                    address:address,
                    school:school,
                    classMe:classMe,
                    major:major,

                    score:score,
                    teacher:teacher,
                    teacherTel:teacherTel,
                    father:father,
                    fatherTel:fatherTel,
                    mather:mather,
                    matherTel:matherTel,
                    personalTel:personalTel
                },
                success:function(rs) {
                    if(rs == 1){
                        layer.msg('修改成功');
                    }else{
                        layer.msg('修改失败');
                    }
                }
            })
        }else{
            layer.msg('电话格式不正确');

        }
    })
})




//判断是否已上传报名信息
$(function () {
    var userID = $.cookie('userID');

    $.ajax({
        url : urlTian,
        type:'POST',
        data:{userID:userID},
        success:function(rs){
            if(rs == 1){
                $('.accR').css('display','block')
                $('.user-en').css('display','block')
            }else{
                $('.accR').css('display','none')
                $('.user-en').css('display','none')
            }
        }
    })
})