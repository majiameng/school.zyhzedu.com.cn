
$(function () {
    var userID = $.cookie('userID');
    $.ajax({
        type:'post',
        url:scheduleUrl,
        data:{userID:userID},
        async: false,
        success:function(rs){
            if(rs == 1){
                $('.schedu-down').css('display','block')
                $('.schedu-con').css('display','block')
                $('.schedu-down-2').css('display','none')
                $('.schedu-down-3').css('display','none')
                $('.schedu-notice').css('display','block')
                $('.circle').addClass('ci-1')

            }else if(rs == 0){
                $('.schedu-con').css('display','block')
                $('.schedu-down').css('display','none')
                $('.schedu-notice').css('display','none')
            }else if(rs == 2){
                $('.schedu-con').css('display','block')
                $('.schedu-down-2').css('display','block')
                $('.schedu-down').css('display','none')
                $('.schedu-notice').css('display','none')
                $('.circle').addClass('ci-1')
            }else if(rs == 3){
                $('.schedu-down-3').css('display','block')
                $('.schedu-down').css('display','none')
                $('.schedu-notice').css('display','none')
                $('.schedu-con').css('display','none')
            }

        }
    })
})
