/**
 * 移动端通用js
 * Created by DELL on 2016/6/7.
 */
//自适应字体大小
! function(e) {
    function t() {
        var t = n.clientWidth,
            r = "}";
        !navigator.userAgent.match(/Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i) && t > 1024 && (t = 640, r = ";max-width:" + t + "px;margin-right:auto!important;margin-left:auto!important;}"), e.rem = t / 16, /ZTE U930_TD/.test(navigator.userAgent) && (e.rem = 1.13 * e.rem), /Android\s+4\.4\.4;\s+M351\s/.test(navigator.userAgent) && (e.rem = e.rem / 1.05), i.innerHTML = "html{font-size:" + e.rem + "px!important;}body{font-size:" + 12 * (t / 320) + "px"
    }
    var n = document.documentElement,
        i = document.createElement("style");
    n.firstElementChild.appendChild(i),
        e.addEventListener("resize", function() {
            t()
        }, !1),
        e.addEventListener("pageshow", function(e) {
            e.persisted && t()
        }, !1),
        t();
}(window);
// 错误信息弹出
function errorMessage(mess,obj){
    if ($(".error_tips").size()>0) {
        return ;
    }
    var $errorObj=$('<p class="error_tips">'+mess+'</p>');
    $("body").append($errorObj);
    $errorObj.css({
        'margin-left':-$errorObj.outerWidth()/2
    });
    setTimeout(function(){
        $errorObj.addClass("show");
    },100);
    setTimeout(function(){
        $errorObj.remove();
    },3000);
}

//弹框
function showBpopup(bpopupSelector){
    var maskerObj = document.createElement("div");
    maskerObj.style.cssText = " position: fixed;top: 0;left: 0;right: 0;bottom: 0;background-color: rgba(0,0,0,0.5);z-index: 1;";
    var bodyObj = document.getElementsByTagName("body")[0];
    bodyObj.appendChild(maskerObj);
    var bpopupObj = bodyObj.querySelector(bpopupSelector);
    bpopupObj.style.display = 'block';
    var closeObj = bpopupObj.querySelector(".close");
    if (closeObj) {
        closeObj.addEventListener("click",function(){
            bodyObj.removeChild(maskerObj);
            bpopupObj.style.display = 'none';
        },false)
    }
}