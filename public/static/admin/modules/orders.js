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

layui.define(["table", "form", "element"],

    function(e) {
        var t = layui.$,
            i = layui.table,
            u = layui.util;


        i.render({
            elem: "#app-balance-details-list",
            url: 'channelStatV2',
            //自定义响应字段
            response: {
                statusName: 'code' //数据状态的字段名称
                , statusCode: 1 //数据状态一切正常的状态码
                , msgName: 'msg' //状态信息的字段名称
                , dataName: 'data' //数据详情的字段名称
            },
            cols: [[
                {
                    field: "pay_chanel_name",
                    width: '20%',
                    title: "通道名",
                },
                {
                    field: "username",
                    width: '20%',
                    title: "30面额",
                    templet: function (d) {
                         return d.success_rate_30+'%(成功'+d.order_price_30_success_count+'单,失败'+(d.order_price_30_count-d.order_price_30_success_count)+'单）'
                    }
                },

                {
                    field: "username",
                    width: '20%',
                    title: "50面额",
                    templet: function (d) {
                        return d.success_rate_50+'%(成功'+d.order_price_50_success_count+'单,失败'+(d.order_price_50_count-d.order_price_50_success_count)+'单）'
                    }
                },

                {
                    field: "username",
                    width: '20%',
                    title: "100面额",
                    templet: function (d) {
                        return d.success_rate_100+'%(成功'+d.order_price_100_success_count+'单,失败'+(d.order_price_100_count-d.order_price_100_success_count)+'单）'
                    }
                },

                {
                    field: "username",
                    width: '20%',
                    title: "200面额",
                    templet: function (d) {
                        return d.success_rate_200+'%(成功'+d.order_price_200_success_count+'单,失败'+(d.order_price_200_count-d.order_price_200_success_count)+'单）'
                    }
                },

            ]],
            // page: {
            //     limit: 10,
            //     limits: [10, 15, 20, 25, 30]
            // },
            text: "对不起，加载出现异常！"
        }),


            i.render({
                elem: "#app-order-list",
                url: 'getList',
                //自定义响应字段
                response: {
                    statusName: 'code' //数据状态的字段名称
                    ,statusCode: 1 //数据状态一切正常的状态码
                    ,msgName: 'msg' //状态信息的字段名称
                    ,dataName: 'data' //数据详情的字段名称
                },
                cols: [[{
                    type: "checkbox",
                    fixed: "left"
                },
                    /*  {
                          field: "id",
                          width: 120,
                          title: "ID",
                          sort: !0
                      },*/
                    {
                        field: "uid",
                        width: 100,
                        title: "商户编号",
                        templet: function (d) {
                            if(d.request_elapsed_time >0 ){
                                return d.uid;
                            }
                            return "<span style='color: red'>"+d.uid+"</span>";
                        }
                    },
                    {
                        field: "pay_code_name",
                        width: 100,
                        title: "支付产品",
                        templet: function (d) {
                            if(d.request_elapsed_time >0 ){
                                return d.pay_code_name;
                            }
                            return "<span style='color: red'>"+d.pay_code_name+"</span>";
                        }
                    },
                    {
                        field: "channel_name",
                        width: 100,
                        title: "交易渠道",
                        templet: function (d) {
                            if(d.request_elapsed_time >0 ){
                                return d.channel_name;
                            }
                            return "<span style='color: red'>"+d.channel_name+"</span>";
                        }
                    },
                    {
                        field: "out_trade_no",
                        width: 200,
                        title: "平台订单号",
                        templet: function (d) {
                            if(d.request_elapsed_time >0 ){
                                return d.out_trade_no;
                            }
                            return "<span style='color: red'>"+d.out_trade_no+"</span>";
                        }
                    },
                    {
                        field: "trade_no",
                        width: 200,
                        title: "跑分平台订单号",
                        templet: function (d) {
                            if(d.request_elapsed_time >0 ){
                                return d.trade_no;
                            }
                            return "<span style='color: red'>"+d.trade_no+"</span>";
                        }
                    },
                    /*{
                                field: "orderNum",
                                width: 200,
                                title: "淘宝订单号"
                            },
                            /*  {
                                 field: "subject",
                                 width: 150,
                                 title: "交易项目"
                             },
                             {
                                 field: "body",
                                 width: 150,
                                 title: "交易内容"
                             },
                            {
                                 field: "currency",
                                 width: 80,
                                 title: "货币"
                             },*/
                    {
                        field: "amount",
                        width: 100,
                        title: "交易金额",
                        style:"color:red"
                    },
                    // {
                    //     field: "income",
                    //     width: 100,
                    //     title: "到账金额",
                    //     style: "color:green"
                    // },
                    {
                        field: "user_in",
                        width: 100,
                        title: "商户收入",
                        style: "color:grey"
                    },
                    /*{
                         field: "agent_in",
                         width: 100,
                         title: "代理收入",
                         style: "color:orange"
                     },*/
                    // {
                    //     field: "platform_in",
                    //     width: 100,
                    //     title: "平台收入",
                    //     style: "color:red"
                    // },
                    {
                        field: "create_time",
                        width: 180,
                        title: "创建时间",
                        templet: function (d) {
                            if(d.request_elapsed_time >0 ){
                                return d.create_time;
                            }
                            return "<span style='color: red'>"+d.create_time+"</span>";
                        }
                    },
                    {
                        field: "update_time",
                        width: 180,
                        title: "更新时间",
                        templet: function (d) {
                            if(d.request_elapsed_time >0 ){
                                return d.update_time;
                            }
                            return "<span style='color: red'>"+d.update_time+"</span>";
                        }
                    },
                    // {
                    //     field: "visite_time",
                    //     width: 180,
                    //     title: "访问时间",
                    // },
                    {
                        field: "status",
                        title: "订单状态",
                        templet: "#buttonTpl",
                        minWidth: 80,
                        align: "center"
                    },
                    {
                        field: "notify",
                        title: "回调状态",
                        templet: "#notifyTpl",
                        minWidth: 80,
                        align: "center"
                    },
                    {
                        title: "操作",
                        align: "center",
                        minWidth: 220,
                        // fixed: "right",
                        toolbar: "#table-system-order"
                    },
                    {
                        align: "left",
                        field: "bd_remarks",
                        width: 200,
                        title: "补单备注",
                        templet: function (d) {
                            if(d.request_elapsed_time >0 ){
                                return d.bd_remarks?d.bd_remarks:'';
                            }
                            return "<span style='color: red'>"+d.bd_remarks+"</span>";
                        }
                    },

                ]],
                page: !0,
                limit: 10,
                limits: [10, 15, 20, 25, 30,50],
                text: "对不起，加载出现异常！"
            }),
            i.on("tool(app-order-list)",
                function(e) {
                    e.data;
                    if ("details" === e.event) {
                        t(e.tr);
                        layer.open({
                            type: 2,
                            title: "交易详情",
                            content: "details.html?id=" + e.data.id,
                            maxmin: !0,                             area: ['80%','60%'],
                            btn: ["确定", "取消"],
                            yes: function(e, t) {},
                            success: function(e, t) {}
                        })
                    }
                    else if ("add_notify" === e.event) {
                        //补发通知
                       /* t(e.tr);
                        var index = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
                        t.ajax({
                            url: 'subnotify?order_id='+ e.data.id,
                            method:'POST',
                            success:function (res) {
                                layer.closeAll();
                                if (res.code == 1){
                                    layer.msg(res.msg, {icon: 1,time: 3000},function () {

                                        i.reload('app-order-list');
                                    });

                                }else{
                                    layer.msg(res.msg, {icon: 2,time: 3000});
                                }
                            }
                        });*/
                        layer.open({
                            title: '服务器'
                            ,btn: ['确定','取消']
                            ,content: '<div class="layui-form-item">' +
                                '<label class="layui-form-label">服务器：</label>' +
                                '<div class="layui-input-block">' +
                                '<select class="layui-form-select" name="server" id="server" lay-filter="server">' +
                                '<option value="0">本地服务器</option>' +
                                '<option value="1">中转服务器</option>' +
                                '</select> ' +
                                '</div>' +
                                '</div>'
                            // ,success: function (layero){
                            //     var btn = layero.find('.layui-layer-btn');
                            //     btn.find('.layui-layer-btn0').attr({
                            //         msg: '1'
                            //     });
                            // }
                            ,yes: function (index, layero){
                                t(e.tr);
                                var index = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
                                t.ajax({
                                    url: 'subnotify?order_id='+ e.data.id + '&server=' + t
                                    ('#server').val(),
                                    method:'POST',
                                    success:function (res) {
                                        layer.closeAll();
                                        if (res.code == 1){
                                            layer.msg(res.msg, {icon: 1,time: 3000},function () {

                                                i.reload('app-order-list');
                                            });

                                        }else{
                                            let callback_data = res.data.callback_data;
                                            let params = res.data.params;

                                            let html = '';
                                            let copyButton = '<button class="layui-btn layui-btn-info  layui-btn-xs" lay-event="copy" ' +
                                                'style="margin-left: 1rem"  onclick="copy(\'callback_data\')" >复制</button>';
                                            html += '<p><strong>回调信息</strong>：<span id="callback_data" data-clipboard-text="123456">'+callback_data+'</span>'+ copyButton +'</p>'

                                            html += '<hr>';

                                            let copyButton1 = '<a class="layui-btn layui-btn-info  layui-btn-xs" lay-event="copy" ' +
                                                'style="margin-left: 1rem" onclick="copy(\'params\')">复制</a>';
                                            html += '<p><strong>请求参数：</strong><span id="params" data-clipboard-text="' + params + '">'+params+'</span>'+ copyButton1 +'</p>'
                                            html += '<hr/>'
                                            layer.open({
                                                title: '回调失败'
                                                ,area:['60%', 'auto']
                                                , content: html
                                            })
                                        }
                                    }
                                });
                            }
                        })
                    }
                    else if("budan" === e.event){
                        if (getCookie('security_code_success')){
                            layer.open({
                                type: 2,
                                title: "补单详情",
                                content: "budanDetails.html?id=" + e.data.id,
                                maxmin: !0,                             area: ['80%','60%'],
                                btn: ["确定", "取消"],
                                yes: function(e1, layero) {
                                    var bd_remarks= t.trim(layero.find('iframe').contents().find('#bd_remarks').val());
                                    if(bd_remarks===''){
                                        layer.msg('补单人请填写补单备注',{icon:2,time:1500});
                                    }
                                    if(bd_remarks.length >255){
                                        layer.msg('补单备注最长255个字符',{icon:2,time:1500});
                                    }
                                    //正式补单操作
                                    t.ajax({
                                        url: 'update?id='+ e.data.id,
                                        method:'POST',
                                        data:{bd_remarks:bd_remarks},
                                        success:function (res) {
                                            if (res.code == 1){
                                                layer.closeAll();
                                                i.reload('app-order-list');
                                            }else{
                                                layer.msg(res.msg, {icon: 2,time: 1500});
                                            }
                                        }
                                    });
                                },
                                // success: function(e, t) {
                                //
                                //     //正式补单操作
                                //     //                 t.ajax({
                                //     //                     url: 'update?id='+ e.data.id,
                                //     //                     method:'POST',
                                //     //                     success:function (res) {
                                //     //                         if (res.code == 1){
                                //     //                             e.update()
                                //     //                         }
                                //     //                         layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                                //     //                         layer.close(d); //关闭弹层
                                //     //                     }
                                //     //                 });
                                //
                                //
                                //
                                //
                                //
                                // }
                            })
                       } else {
                           layer.prompt({
                               formType: 1,
                               title: "敏感操作，请验证安全码"
                           }, function(d, f) {
                               // console.log(i);return false;
                               //检测口令
                               t.ajax({
                                   url: '/admin/api/checkOpCommand?command='+ d,
                                   method:'POST',
                                   success:function (res) {
                                       if (res.code == 1){
                                           //口令正确
                                           layer.close(f); //关闭弹层
                                           t(e.tr);
                                           layer.open({
                                               type: 2,
                                               title: "补单详情",
                                               content: "budanDetails.html?id=" + e.data.id,
                                               maxmin: !0,                             area: ['80%','60%'],
                                               btn: ["确定", "取消"],
                                               yes: function(e1, layero) {
                                                   var bd_remarks= t.trim(layero.find('iframe').contents().find('#bd_remarks').val());
                                                   if(bd_remarks===''){
                                                       layer.msg('补单人请填写补单备注',{icon:2,time:1500});
                                                   }
                                                   if(bd_remarks.length >255){
                                                       layer.msg('补单备注最长255个字符',{icon:2,time:1500});
                                                   }
                                                   //正式补单操作
                                                   t.ajax({
                                                       url: 'update?id='+ e.data.id,
                                                       method:'POST',
                                                       data:{bd_remarks:bd_remarks},
                                                       success:function (res) {
                                                           if (res.code == 1){
                                                               layer.closeAll();
                                                               i.reload('app-order-list');
                                                           }else{
                                                               layer.msg(res.msg, {icon: 2,time: 1500});
                                                           }
                                                       }
                                                   });
                                               },
                                               // success: function(e, t) {
                                               //
                                               //     //正式补单操作
                                               //     //                 t.ajax({
                                               //     //                     url: 'update?id='+ e.data.id,
                                               //     //                     method:'POST',
                                               //     //                     success:function (res) {
                                               //     //                         if (res.code == 1){
                                               //     //                             e.update()
                                               //     //                         }
                                               //     //                         layer.msg(res.msg, {icon: res.code == 1 ? 1: 2,time: 1500});
                                               //     //                         layer.close(d); //关闭弹层
                                               //     //                     }
                                               //     //                 });
                                               //
                                               //
                                               //
                                               //
                                               //
                                               // }
                                           })
                                       }else{
                                           layer.msg(res.msg,{icon:2,time:1500});
                                           layer.close(d); //关闭弹层
                                       }
                                   }
                               });
                           });
                       }
                    }
                }),
            e("orders", {})
    });
