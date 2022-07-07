$(function () {
    $(".inner").attr('ontouchstart', 'hover(this)');//hover效果
    $(".inner").attr('ontouchend', 'mouseout(this)');//秒除hover
})
function mouseout(obj) {
    var className = "hover";
    var _ecname = obj.className;
    if (_ecname.length == 0) return;
    if (_ecname == className) {
        obj.className = "";return;
    }
    if (_ecname.match(new RegExp("(^|\s)" + className + "(\s|$)")))
        obj.className = _ecname.replace((new RegExp("(^|\s)" + className + "(\s|$)")), " ");
}
function hover(obj) {
    if (!obj) return;
    var className = "hover"
    var _ecname = obj.className;
    if (_ecname.length == 0) {
        obj.className = className;return;
    }
    if (_ecname == className || _ecname.match(new RegExp("(^|\s)" + className + "(\s|$)")))
        return;
    obj.className = _ecname + " " + className;
}