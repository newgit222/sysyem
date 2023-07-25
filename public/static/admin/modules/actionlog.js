/*
 * +----------------------------------------------------------------------
 *   | 狂神系统系统 [ WE CAN DO IT JUST THINK ]
 * +----------------------------------------------------------------------
 *   | Copyright (c) 2018 http://www.iredcap.cn All rights reserved.
 * +----------------------------------------------------------------------
 *   | Licensed ( https://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 *   | Author: Brian Waring <BrianWaring98@gmail.com>
 * +----------------------------------------------------------------------
 */

layui.define(["table", "form", "element"],
    function(e) {
        var q = layui.$
            ,u = layui.util
            ,util = layui.util
            ,i = layui.table;
        // 表格初始化
        i.render({
            elem: "#app-admin-log-list",
            url: 'getList',
            where:{describe:q('input[name="describe"]').val()},
            //自定义响应字段
            response: {
                statusCode: 1 //数据状态一切正常的状态码
            },
            cols: [[{
                type: "checkbox",
                fixed: "left"
            },
                {
                    field: "id",
                    width: 100,
                    title: "ID",
                    sort: !0
                },
                {
                    field: "module",
                    width: 100,
                    title: "模块12"
                },
                {
                    field: "uid",
                    width: 100,
                    title: "操作者ID"
                },
                {
                    field: "action",
                    width: 100,
                    title: "操作行为"
                },
                {
                    field: "describe",
                    title: "行为描述"
                },
                {
                    field: "url",
                    title: "URL"
                },
                {
                    field: "ip",
                    width: 100,
                    title: "IP"
                },
                {
                    field: "create_time",
                    width: 200,
                    title: "创建时间",
                },
                {
                    title: "操作",
                    align: "center",
                    width: 100,
                    fixed: "right",
                    toolbar: "#table-system-order"
                }]],
            page: !0,
            limit: 10,
            limits: [10, 15, 20, 25, 30],
            text: "对不起，加载出现异常！"
        }),
        i.on("tool(app-admin-log-list)",
            function(e) {
            if ("del" === e.event){
                if (getCookie('admin_check_command_ok')){
                    layer.confirm("真的删除么", function(t) {
                        q.ajax({
                            url: 'logDel?id='+ e.data.id,
                            method:'POST',
                            success:function (res) {
                                if (res.code == 1){
                                    e.del()
                                }
                                layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                                layer.close(index); //关闭弹层
                            }
                        });
                    })
                }else{
                    layer.prompt({
                        formType: 1,
                        title: "敏感操作，请验证安全码"
                    }, function(value, index) {
                        layer.close(index)
                        q.ajax({
                            url: '/admin/api/checkOpCommand?command='+ value,
                            method:'POST',
                            success:function (res) {
                                if (res.code == 1){
                                    //口令正确
                                    layer.close(index); //关闭弹层
                                    layer.confirm("真的删除么",
                                        function(t) {
                                            q.ajax({
                                                url: 'logDel?id='+ e.data.id,
                                                method:'POST',
                                                success:function (res) {
                                                    if (res.code == 1){
                                                        e.del()
                                                    }
                                                    layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                                                    layer.close(index); //关闭弹层
                                                }
                                            });
                                        })
                                }else{
                                    layer.msg(res.msg,{icon:2,time:1500});
                                    layer.close(index); //关闭弹层
                                }
                            }
                        });
                    });
                }
            }
            }),
        i.render({
                elem: "#app-sms-log-list",
                url: 'getSmsLogLists',
                //自定义响应字段
                response: {
                    statusCode: 1 //数据状态一切正常的状态码
                },
                cols: [[{
                    type: "checkbox",
                    fixed: "left"
                },
                    {
                        field: "id",
                        width: 60,
                        title: "ID",
                        sort: !0
                    },
                    {
                        field: "username",
                        width:100,
                        title: "码商"
                    },
                    {
                        field: "context",
                        title: "短信",
                        minWidth: 200,
                        templet:function (v){
                            let txt = '';
                            txt += '来源号码:' + v.phone + '<br/>';
                            txt += '短信内容:' + v.context;
                            return txt;
                        }
                    },
                    {
                        field: 'bank_name',
                        title: '信息',
                        minWidth: 200,
                        templet: function (v){
                            let txt = '';
                            txt += '<strong>姓名：'+ v.bank_name + '</strong><br>';
                            txt += '<strong>卡号：'+ v.account_number + '</strong><br>';
                            txt += '<strong>银行：'+ v.account_name + '</strong>';
                            return txt;
                        }

                    },
                    {
                        field: 'order_pay_price',
                        minWidth: 80,
                        title: '账变金额',
                    },
                    {
                        field: 'balance',
                        minWidth: 80,
                        title: '余额',
                    },
                    {
                        field: 'order_no',
                        title: '解析状态',
                        templet: function (v){
                            if (v.order_no){
                                return '<button class="layui-btn layui-btn-success layui-btn-xs">已匹配</button>'
                            }
                            return '<button class="layui-btn layui-btn-danger layui-btn-xs">未匹配</button>'
                        }
                    },
                    {
                        field: "order_no",

                        title: "订单号"
                    },
                    {
                        field: "ip",
                        width: 100,
                        title: "IP"
                    },

                    {
                        field: "create_time",
                        width: 200,
                        title: "创建时间",
                    }
                   /* {
                        title: "操作",
                        align: "center",
                        width: 100,
                        fixed: "right",
                        toolbar: "#table-sms-log-order"
                    }*/
                    ]],
                page: !0,
                limit: 10,
                limits: [10, 15, 20, 25, 30],
                text: "对不起，加载出现异常！"
            });

        e("actionlog", {})
    });