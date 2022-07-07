$(document).ready(function () {
    $('#resume-btn').click(function () {

        if($('#fo-agree').is(':checked')) {
            window.location.href="../index.php?m=Home&c=recruit&a=index"
        }else{
            layer.msg('请点击,已阅读');
        }
    })
})
