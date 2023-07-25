/** layuiAdmin.std-v1.0.0 LPPL License By http://www.layui.com/admin/ */ ;
layui.define(function(e) {
    var i = (layui.$, layui.layer, layui.laytpl, layui.setter, layui.view, layui.admin);
    i.events.logout = function() {
        i.req({
            url: "/admin/login/logout",
            type: "get",
            data: {},
            done: function(e) {
                i.exit(function() {
                    location.href = "admin/login"
                })
            }
        })
    }, e("common", {})


    /*i.req({
        url:'/admin/admin/adminCommand',
        type:'POST',
        async: false,
        done: function(res) {
            if (res.code == 1 && res.status == 'fail'){
                layer.prompt({
                    formType: 3,
                    placeholder: '输入口令',
                    title: '设置口令',
                }, function(value, index, elem){
                    if( layui.$('#confirm_pass').val()===""){
                        layer.tips("请填写注销人", layui.$('#confirm_pass'));
                        return;
                    }
                    i.req({
                        url:'/admin/admin/setCommand',
                        type:'POST',
                        data:{command:value, confirm_command:layui.$('#confirm_pass').val()},
                        done: function(res) {
                            if ( res.status == 'ok'){
                                layer.close(index);
                            }
                            layer.msg(res.msg, {icon: res.status == 'ok' ? 1 : 2, time: 1500});
                        },
                        error: function (e){}
                    });
                });
                layui.$(".layui-layer-content").append("<br/><input type=\"text\" id= \"confirm_pass\" class=\"layui-input\" placeholder=\"确认口令\"/>")
            }
        },
        error: function (e){}
    });
    i.req({
        url:'/admin/admin/adminBalance',
        type:'POST',
        done: function(res) {
            if (res.code == 1 && res.status == 'ok'){
                if (res.balance < 100){
                    layer.confirm('您的余额不足100，请及时充值！', {
                        btn: ['确定']
                    });
                }
            }
        },
        error: function (e){}
    });*/

});

function getCookie(c_name)
{
    if(document.cookie.length>0){
        c_start=document.cookie.indexOf(c_name + "=");
        if(c_start!=-1){
            c_start=c_start + c_name.length+1 ;
            c_end=document.cookie.indexOf(";",c_start);
            if(c_end==-1) {
                c_end=document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start,c_end));
        }
    }
    return "";
}
