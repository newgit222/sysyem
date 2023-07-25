/*
 *  +----------------------------------------------------------------------
 *  | 狂神系统系统 [ WE CAN DO IT JUST THINK ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2018 http://www.iredcap.cn All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed ( https://www.apache.org/licenses/LICENSE-2.0 )
 *  +----------------------------------------------------------------------
 *  | Author: Brian Waring <BrianWaring98@gmail.com>
 *  +----------------------------------------------------------------------
 */

layui.define(["table", "form"],
    function(e) {
        var t = layui.$,
            i = layui.table,
            u = layui.util,
            n = layui.form;

        i.render({
            elem: "#app-admin-user-manage",
            url: "userList",
            //自定义响应字段
            response: {
                statusCode: 1 //数据状态一切正常的状态码
            },
            cols: [
                [{
                    type: "checkbox",
                    fixed: "left"
                }, {
                    field: "id",
                    width: 80,
                    title: "ID",
                    sort: !0
                }, {
                    field: "username",
                    title: "登录名"
                }, {
                    field: "nickname",
                    title: "昵称"
                },{
                    field: "email",
                    title: "邮箱"
                },
                {
                    field: "balance",
                    width: 100,
                    title: "余额"
                },
                     {
                     title: "状态",
                    width: 500,
                    align: "center",
                      width: 120,
                     toolbar: "#table-admin-status"
                     },
                {
                    field: "create_time",
                    title: "加入时间",
                    sort: !0,
                    templet: function(d) {return u.toDateString(d.jointime*1000); }
                }, {
                    field: "status",
                    title: "审核状态",
                    templet: "#buttonTpl",
                    minWidth: 80,
                    align: "center"
                }, {
                    title: "操作",
                    width: 600,
                    align: "center",
                    fixed: "right",
                    toolbar: "#table-useradmin-admin"
                }]
            ],
            text: "对不起，加载出现异常！"
        }),
        i.on("tool(app-admin-user-manage)",
            function(e) {
                var d = e.data;
                if ("del" === e.event) {

                    if (getCookie('admin_check_command_ok')){
                        layer.confirm("确定删除此管理员？",
                            function (d) {
                                console.log(e),
                                    t.ajax({
                                        url: 'userDel?id=' + e.data.id,
                                        method: 'POST',
                                        success: function (res) {
                                            if (res.code == 1) {
                                                e.del()
                                            }
                                            layer.msg(res.msg, {icon: res.code == 1 ? 1 : 2, time: 1500});
                                            layer.close(d); //关闭弹层
                                        }
                                    });
                            })
                    }else{
                        layer.prompt({
                                formType: 1,
                                title: "敏感操作，请验证安全码"
                            },
                            function (d, i) {
                                layer.close(i),
                                    t.ajax({
                                        url: '/admin/api/checkOpCommand?command='+ d,
                                        method:'POST',
                                        success:function (res) {
                                            if (res.code == 1){
                                                //口令正确
                                                layer.close(i); //关闭弹层
                                                layer.confirm("确定删除此管理员？",
                                                    function (d) {
                                                        console.log(e),
                                                            t.ajax({
                                                                url: 'userDel?id=' + e.data.id,
                                                                method: 'POST',
                                                                success: function (res) {
                                                                    if (res.code == 1) {
                                                                        e.del()
                                                                    }
                                                                    layer.msg(res.msg, {icon: res.code == 1 ? 1 : 2, time: 1500});
                                                                    layer.close(d); //关闭弹层
                                                                }
                                                            });
                                                    })
                                            }else{
                                                layer.msg(res.msg,{icon:2,time:1500});
                                                layer.close(i); //关闭弹层
                                            }
                                        }
                                    });

                            });
                    }


                }else if ("auth" === e.event) {
                    t(e.tr);
                    layer.open({
                        type: 2,
                        title: "管理授权",
                        content: "userAuth.html?id="+ d.id,
                        maxmin: !0,                             area: ['80%','60%'],
                        btn: ["确定", "取消"],
                        yes: function(f, t) {
                            var l = window["layui-layer-iframe" + f],
                                r = "app-admin-user-auth-submit",
                                n = t.find("iframe").contents().find("#" + r);
                            l.layui.form.on("submit("+ r +")",
                                function(t) {
                                    var l = t.field;
                                    layui.$.post("userAuth",l,function (res) {
                                        if (res.code == 1){
                                            i.render(),
                                                layer.close(f)
                                        }
                                        layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                                    });
                                }),
                                n.trigger("click")
                        },
                        success: function(e, t) {}
                    })
                }
                else  if ("edit" === e.event) {
                    t(e.tr);
                    layer.open({
                        type: 2,
                        title: "编辑管理员",
                        content: "userEdit.html?id="+ d.id,
                        maxmin: !0,                             area: ['80%','60%'],
                        btn: ["确定", "取消"],
                        yes: function(f, t) {
                            var l = window["layui-layer-iframe" + f],
                                r = "app-admin-user-back-submit",
                                n = t.find("iframe").contents().find("#" + r);
                            l.layui.form.on("submit(" + r + ")",
                                function(d) {
                                    var l = d.field;
                                    layui.$.post("userEdit",l,function (res) {
                                        if (res.code == 1){
                                            //更新数据表
                                            e.update({
                                                username: l.username,
                                                nickname: l.nickname,
                                                email: l.email,
                                                status: l.status
                                            }),i.render(),
                                                layer.close(f)
                                        }
                                        layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                                    });
                                }),
                                n.trigger("click")
                        },
                        success: function(e, t) {}
                    })
                }else  if ("restGoogle" === e.event) {
                    console.log(e.tr)
                    layer.confirm("确定重置Google验证码？",{
                        btn: ['确定', '取消'],
                        yes: function () {
                            var l = {id: e.data.id};
                            layui.$.post("restGoogle",l ,function (res) {
                                if (res.code == 1){
                                    i.reload('app-admin-user-manage');
                                }
                                layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                            });
                        },
                        no: function () {
                            layer.msg('服务器错误', {icon: 2,time: 1500});
                        }
                    })
                } else if ('rate' === e.event){
                    t(e.tr);
                    layer.open({
                        type: 2,
                        title: "设置费率",
                        content: "editRate.html?id="+ d.id,
                        maxmin: !0,
                        area: ['80%', '60%'],
                        btn: ["确定", "取消"],
                        yes: function(f, t) {
                            var l = window["layui-layer-iframe" + f],
                                r = "app-admin-user-back-rate-submit",
                                n = t.find("iframe").contents().find("#" + r);
                            l.layui.form.on("submit(" + r + ")",
                                function(d) {
                                    var l = d.field;
                                    layui.$.post("editRate",l,function (res) {
                                        if (res.code == 1){
                                            i.reload('app-admin-user-manage'), layer.close(f)
                                        }
                                        layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                                    });
                                }),
                                n.trigger("click")
                        },
                        success: function(e, t) {}
                    })
                } else if ('change_balance' === e.event){
                    t(e.tr);
                    layer.open({
                        type: 2,
                        title: "增减余额",
                        content: "changeBalance.html?id="+ d.id,
                        maxmin: !0,
                        area: ['80%', '60%'],
                        btn: ["确定", "取消"],
                        yes: function(f, t) {
                            var l = window["layui-layer-iframe" + f],
                                r = "app-admin-change-balance-submit",
                                n = t.find("iframe").contents().find("#" + r);
                            l.layui.form.on("submit(" + r + ")",
                                function(d) {
                                    var l = d.field;
                                    layui.$.post("changeBalance",l,function (res) {
                                        if (res.code == 1){
                                            i.reload('app-admin-user-manage');
                                            layer.close(f);
                                        }
                                        layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500}, function () {
                                            window.location.href = document.location;
                                        });
                                    });
                                }),
                                n.trigger("click")
                        },
                        success: function(e, t) {}
                    })
                }else if (e.event === 'viewConfig') {
                    layer.open({
                        type: 2,
                        title: '查看配置',
                        content: 'viewConfig.html?id='+ d.id,
                        maxmin: !0,
                        area: ['100%', '100%'],
                        btn: ["取消"],
                        success: function(e, t) {}
                    })
                }
            }),

            i.render({
                elem: "#app-admin-user-stat-manage",
                url: "getUserListStat",
                //自定义响应字段
                response: {
                    statusCode: 1 //数据状态一切正常的状态码
                },
                cols: [
                    [{
                        type: "checkbox",
                        fixed: "left"
                    }, {
                        field: "username",
                        title: "网站名称"
                    },{
                        field: "success_number",
                        title: "成功订单数量"
                    },{
                        field: "success_amount",
                        title: "成功订单金额（元）"
                    }],

                ],
                page: !0,
                limit: 10,
                limits: [10, 15, 20, 25, 30],
                text: "对不起，加载出现异常！"
            }),


        i.render({
            elem: "#app-admin-user-role",
            url: "groupList",
            //自定义响应字段
            response: {
                statusCode: 1 //数据状态一切正常的状态码
            },
            cols: [
                [{
                    type: "checkbox",
                    fixed: "left"
                }, {
                    field: "id",
                    width: 80,
                    title: "ID",
                    sort: !0
                }, {
                    field: "name",
                    width: 100,
                    title: "角色名"
                }, {
                    field: "rules",
                    title: "拥有权限"
                }, {
                    field: "describe",
                    title: "具体描述"
                }, {
                    title: "操作",
                    width: 200,
                    align: "center",
                    fixed: "right",
                    toolbar: "#table-admin-user-role"
                }]
            ],
            text: "对不起，加载出现异常！"
        }),
        i.on("tool(app-admin-user-role)",
            function(e) {
                var d = e.data;
                if ("del" === e.event) layer.confirm("确定删除此角色？",
                    function(d) {
                        t.ajax({
                            url: 'groupDel?id='+ e.data.id,
                            method:'POST',
                            success:function (res) {
                                if (res.code == 1){
                                    e.del()
                                }
                                layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                                layer.close(d); //关闭弹层
                            }
                        });
                    });
                else if ("auth" === e.event) {
                    t(e.tr);
                    layer.open({
                        type: 2,
                        title: "角色授权",
                        content: "menuAuth.html?id="+ d.id,
                        maxmin: !0,                             area: ['80%','60%'],
                        btn: ["确定", "取消"],
                        yes: function(f, t) {
                            var l = window["layui-layer-iframe" + f],
                                r = "app-admin-user-role-submit";
                            n = t.find("iframe").contents().find("#" + r);
                            l.layui.form.on("submit("+ r +")",
                                function(t) {
                                    var l = t.field;
                                    console.log(l)
                                    layui.$.post("menuAuth",l,function (res) {
                                        if (res.code == 1){
                                            i.render(),
                                                layer.close(f)
                                        }
                                        layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                                    });
                                }),
                                n.trigger("click")
                        },
                        success: function(e, t) {}
                    })
                }
                else if ("edit" === e.event) {
                    t(e.tr);
                    layer.open({
                        type: 2,
                        title: "编辑角色",
                        content: "groupEdit.html?id="+ d.id,
                        maxmin: !0,                             area: ['80%','60%'],
                        btn: ["确定", "取消"],
                        yes: function(f, t) {
                            var l = window["layui-layer-iframe" + f],
                                r = "app-admin-user-role-submit";
                                n = t.find("iframe").contents().find("#" + r);
                            l.layui.form.on("submit("+ r +")",
                                function(t) {
                                    var l = t.field;
                                    layui.$.post("groupEdit",l,function (res) {
                                        if (res.code == 1){
                                            //更新数据表
                                            e.update({
                                                name: l.name,
                                                describe: l.describe
                                            }),i.render(),
                                                layer.close(f)
                                        }
                                        layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                                    });
                                }),
                                n.trigger("click")
                        },
                        success: function(e, t) {}
                    })
                }
            }),

        i.render({
            elem: "#app-admin-recharge-manage",
            url: "getRechargeList",
            //自定义响应字段
            response: {
                statusCode: 1 //数据状态一切正常的状态码
            },
            cols: [
                [{
                    type: "checkbox",
                    fixed: "left"
                }, {
                    field: "id",
                    width: 80,
                    title: "ID",
                    sort: !0
                }, {
                    field: "usdt_address",
                    width: 200,
                    title: "USDT地址"
                }, {
                    field: "usdt_num",
                    title: "usdt 数量"
                }, {
                    field: "amount",
                    title: "充值金额"
                },{
                    field: "status",
                    title: "充值状态",
                    templet: "#statusTpl",
                },{
                    field: "create_time",
                    title: "创建时间"
                }]
            ],
            page:!0,
            text: "对不起，加载出现异常！"
        }),
        i.render({
                elem: "#app-admin-bill-manage",
                url: "getBillList",
                //自定义响应字段
                response: {
                    statusCode: 1 //数据状态一切正常的状态码
                },
                cols: [
                    [{
                        type: "checkbox",
                        fixed: "left"
                    }, {
                        field: "admin_id",
                        width: 100,
                        title: "管理员ID",
                    },{
                        field: "jl_class",
                        width: 80,
                        title: "流水类别",
                        templet: function (d){
                            var str = '---';
                            if (d.jl_class == 1){
                                str = 'usdt充值';
                            }else if(d.jl_class == 2){
                                str = '管理员账变';
                            }else if(d.jl_class == 3){
                                str = '订单完成';
                            }else if(d.jl_class == 4){
                                str = '代付订单完成'
                            }

                            return str;
                        }
                    }, {
                        field: "info",
                        width: 250,
                        title: "备注",
                    },{
                        field: "amount",
                        width: 200,
                        title: "金额",
                        templet: function (d){
                            let str = d.amount;
                            if (d.jc_class == '-'){
                                str = '<span style="color: red">'+d.jc_class+str+'</span>'
                            }else if(d.jc_class == '+') {
                                str = '<span style="color:green">'+d.jc_class+ str + '</span>'
                            }
                            return str;

                        }
                    }, {
                        field: "pre_amount",
                        title: "变动前金额"
                    }, {
                        field: "last_amount",
                        title: "变动后金额"
                    },{
                        field: "addtime",
                        title: "添加时间",
                        templet: function (d){
                            return u.toDateString(d.addtime * 1000);
                        }
                    }]
                ],
                page:!0,
                text: "对不起，加载出现异常！"
            }),
        e("useradmin", {})
    });
