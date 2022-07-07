$(document).ready(function(){ //li单双CSS

    $(".new li img:even").addClass(function(n){
        return 'displayNone';
    });
    $(".new li .newP:even").addClass(function(n){
        return 'marginTop';
    });


});
$(document).ready(function(){ //截取字符串长度
    $('.newP:odd').each(function(){
        var obj = $('p',this);
        var content = obj.text();
        if(content.length>50) {
            obj.text(content.substr(0,34)+"......");
        }
    });
    $('.newP:even').each(function(){
        var obj = $('p',this);
        var content = obj.text();
        if(content.length>50) {
            obj.text(content.substr(0,120)+"......");
        }
    });

});


jQuery.fn.limit=function(){
    var self = $("div[limit]");
    self.each(function(){
        var objString = $(this).text();
        var objLength = $(this).text().length;
        var num = $(this).attr("limit");
        if(objLength > num){
            $(this).attr("title",objString);
            objString = $(this).text(objString.substring(0,num) + "...");
        }
    })
}
$(function(){
    $(document.body).limit();
})


