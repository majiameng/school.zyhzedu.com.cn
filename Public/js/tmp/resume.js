$(function () {
    $('.resume:first').show().siblings().hide();

})


$(function () {
    $('#myScrollspy li').click(function () {
        $('#myScrollspy li').children().find('p').css('font-size','14px');
        $('#myScrollspy li').children().find('p').css('color','#444444');
        $('#myScrollspy li').children('span').removeClass('rectangle');
        $('#myScrollspy li').children('.arrow-right').css('display','block');
        $(this).children().find('p').css('font-size','16px');
        $(this).children().find('p').css('color','#337ab7');
        $(this).children('span').addClass('rectangle');
        $(this).children('.arrow-right').css('display','none');


    })
})
$(document).ready(function() {
    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i) ? true : false;
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i) ? true : false;
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i) ? true : false;
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i) ? true : false;
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Windows());
        }
    };
    if( isMobile.any() )
    {
        $('.resume').show();
    }
});