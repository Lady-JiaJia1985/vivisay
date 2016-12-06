/**
 * 后台PC端通用js
 * Created by DELL on 2016/6/7.
 */
/* *
 * 弱提示
 * param string message     提示信息
 * param int timeout        显示时间ms
 * param function callback  回调函数
 */
function vToast(message, timeout, callback){
    var vtoast = $('#v-toast');
    timeout = timeout || 1500;  //默认1.5s
    vtoast.find('.vivi-modal-msg').text(message);
    vtoast
        .modal({
            keyboard: false,    //forbid keyboard
            backdrop: 'static'  //"static" for a backdrop which doesn't close the modal on click.
        })
        .on('hidden.bs.modal', function () {
            //do something when modal hidden
            if(callback){
                callback();
            }
        });
    setTimeout(function(){
        vtoast.modal('hide')
    }, timeout)
}


/* alert 提示
 * param string message     提示信息
 * param function callback  回调函数
 */
function bsAlert(message, callback){
    var bsAlert = $('#bs-alert');
    bsAlert.find('.modal-body p').text(message);
    bsAlert.modal({
        keyboard: false,    //forbid keyboard
        backdrop: 'static'  //"static" for a backdrop which doesn't close the modal on click.
    }).
    on('hidden.bs.modal', function () {
        //do something when modal hidden
        if(callback){
            callback();
        }
    });
}

/* confirm 提示
 * param string message     提示信息
 * param string id          id
 */
function bsConfirm(message, id, action){
    var confirmObj = $('#bs-confirm');
    action = action || 'common';  //common 为普通行为
    confirmObj.find('input[name=id]').val(id);
    confirmObj.find('input[name=action]').val(action);
    confirmObj.find('.modal-body p').text(message);
    confirmObj.modal({
        keyboard: false,    //forbid keyboard
        backdrop: 'static'  //"static" for a backdrop which doesn't close the modal on click.
    }).on('hidden.bs.modal', function(){
        confirmObj.find('input[name=id]').val('');
        confirmObj.find('.modal-body p').text('');
    })
}
