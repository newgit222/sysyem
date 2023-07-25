<?php

return [
    'template' => [
        [
            'name'  =>  '宝藏代付模板',
            'request_url'   =>  'api/dfpay/create',
            'query_url' =>  '',
            'url'   =>  '',
            'param' => 'merchant_no,order_no,order_amount,ts,receive_method,bank_account,bank_owner,bank_name,attach,callback_url',
            'pay_controller'    =>  'BaoZangOgPay'
        ],
        [
            'name'  =>  'Xxxx代付模板',
            'request_url'   =>  'api/dfpay/pay',
            'query_url' =>  '',
            'url'   =>  '',
            'param' => 'amount,bank_number,bank_code,bank_owner,mchid,out_trade_no,notify_url,body,subject',
            'pay_controller'    =>  'XxxxPay'
        ],
        [
            'name'  =>  '牛逼代付模板',
            'request_url'   =>  'settlement/pay',
            'query_url' =>  '',
            'url'   =>  '',
            'param' => 'p1_merchantno,p2_amount,p3_orderno,p4_truename,p5_cardnumber,p6_branchbankname',
            'pay_controller'    =>  'NbDaifuPay'
        ],
        [
            'name'  =>  '时代代付模板',
            'request_url'   =>  'api/withdrawalv2/submit',
            'query_url' =>  '',
            'url'   =>  '',
            'param' => 'Timestamp,AccessKey,PayChannelId,Payee,PayeeNo,PayeeAddress',
            'pay_controller'    =>  'ShiDaiDfPay'
        ],
        [
            'name'  =>  'ruize代付模板',
            'request_url'   =>  'cashier-api/df-unifiedorder',
            'query_url' =>  '',
            'url'   =>  '',
            'param' => 'mch_id,out_trade_no,amount,receive_method,bank_account,bank_owner,bank_name,attach,callback_url,timestamp',
            'pay_controller'    =>  'RuizeDfPay'
        ],
        [
            'name'  =>  '牛牛代付模板',
            'request_url'   =>  'v2/transfer/add',
            'query_url' =>  '',
            'url'   =>  '',
            'param' => 'merchant_no,order_no,order_amount,ts,receive_method,bank_account,bank_owner,bank_name,attach,callback_url,sign',
            'pay_controller'    =>  'NiuNiuDfPay'
        ],
        [
            'name'  =>  '秒付代付模板',
            'request_url'   =>  'api/payment',
            'query_url' =>  '',
            'url'   =>  '',
            'param' => 'app_id,product_id,out_trade_no,notify_url,amount,time,desc,ext,sign',
            'pay_controller'    =>  'MiaoFuPay'
        ],
        [
            'name'  =>  'sw代付模板',
            'request_url'   =>  'api/pay/submit',
            'query_url' =>  '',
            'url'   =>  '',
            'param' => 'Timestamp,AccessKey,PayChannelId,Payee,PayeeAddress,PayeeNo,OrderNo,Amount,CallbackUrl',
            'pay_controller'    =>  'HaiOuPay'
        ],
        [
            'name'  =>  '无限代付模板',
            'request_url'   =>  'api/agentpay',
            'query_url' =>  '',
            'url'   =>  '',
            'param' => 'merchant_id,pay_type,out_order_no,amount,notify_url,account_name,account_no,bank_name',
            'pay_controller'    =>  'WuXianPay'
        ],
        [
            'name'  =>  '宏远代付模板',
            'request_url'   =>  'v1/dfapi/add',
            'query_url' =>  '',
            'url'   =>  '',
            'param' => 'mchid,out_trade_no,money,notifyurl,bankname,subbranch,accountname,cardnumber,cardtype',
            'pay_controller'    =>  'HongYuanPay'
        ],
        [
            'name'  =>  '蜡笔代付模板',
            'request_url'   =>  'Payment_index.html',
            'query_url' =>  '',
            'url'   =>  '',
            'param' => 'mchid,addtime,bankcode,list(out_trade_no,amount,accountname,bankname,cardnumber,subbranch,province,city,mobile,attach),callback_url,sign',
            'pay_controller'    =>  'LaBiPay'
        ],
        [
            'name'  =>  'jjpay代付模板',
            'request_url'   =>  'api/personnelfiles/order/pay',
            'query_url' =>  '',
            'url'   =>  '',
            'param' => 'userName,version,cardName,cardNum,cardBank,amount,outOrderId,returnUrl',
            'pay_controller'    =>  'JjDfPay'
        ],
        [
            'name'  =>  '新牛牛代付模板',
            'request_url'   =>  'payment/create',
            'query_url' =>  '',
            'url'   =>  '',
            'param' => 'merchantId,tradeNo,bankCard,realName,bankName,notifyUrl,amount,sign',
            'pay_controller'    =>  'NewNiuNiuPay'
        ],
        [

            'name' => '兴隆代付模板',
            'request_url' => 'InterfaceV5/CreateWithdrawOrder',
            'query_url' => '',
            'url' => '',
            'param' => 'Amount,BankCardBankName,BankCardNumber,BankCardRealName,MerchantId,MerchantUniqueOrderId,NotifyUrl,Timestamp,WithdrawType',
            'pay_controller' => 'XingLongPay'    
        ],
        [    
            'name'  =>  'SsffPay代付模板',
            'request_url'   =>  'trade/transfer',
            'query_url' =>  '',
            'url'   =>  '',
            'param' => 'merchant_no,merchant_trade_no,channel_code,bank_code,account,name,amount,notify_url',
            'pay_controller'    =>  'SsffPay'
        ],
        [
            'name'  =>  'Cash代付模板',
            'request_url'   =>  'cash/',
            'query_url' =>  '',
            'url'   =>  '',
            'param' => 'mno,content',
            'pay_controller'    =>  'CashDfPay'
        ],
        [
            'name' => '上合代付模板',
            'request_url' => 'Payment_Dfpay_add.html',
            'query_url' => '',
            'url' => '',
            'param' => 'mchid,out_trade_no,money,bankname,subbranch,accountname,cardnumber,notifyurl,pay_md5sign',
            'pay_controller' => 'ShangHePay'
        ],
        [
            'name' => 'TMZG代付模板',
            'request_url' => 'tesla/api/gateway/dopay',
            'query_url' => '',
            'url' => '',
            'param' => 'merchant_id,channel_code,order_amount,device,request_time,user_name,bank_name,bank_card,notify_url,return_url,sign',
            'pay_controller' => 'TmzgPay'
        ],
        [
            'name' => '传奇代付模板',
            'request_url' => 'pay/unifiedorder',
            'query_url' => '',
            'url' => '',
            'param' => 'merchant_code,order_id,amount,notify_url,paytype,bank_name,username,cardno,fullname,sign',
            'pay_controller' => 'ChuanQiPay'
        ],
        [
            'name' => '百达代付模板',
            'request_url' => 'pay',
            'query_url' => '',
            'param' => 'mch_id,out_trade_no,money,pay_type,notify_url,bank_name,acct_name,acct_no,open_name,sign',
            'pay_controller' => 'BaiDaPay'
        ],
        [
            'name' => 'E代付模板',
            'request_url' => 'api/gateway/walletWithdrawAPIV2',
            'query_url' => '',
            'param' => 'merchantUUID,amount,channelCode,productType,currency,approveType,merchantOrderNo,notifyUrl,remark,extInfo.bankName,extInfo.fullName,extInfo.bankcardNumber,sign',
            'pay_controller' => 'EDaifuPay'
        ],
        [
            'name' => 'Aoks代付模板',
            'request_url' => 'dfApi/order',
            'query_url' => '',
            'param' => 'amount,outOrderNum,mchNum,timestamp,account,accountName,bankName,notifyUrl,sign',
            'pay_controller' => 'AoksPay'
        ],
        [
            'name' => 'Baxibx代付模板',
            'request_url' => 'proxypay/order',
            'query_url' => '',
            'param' => 'order_no,mch_account,amount,account_type,account_no,account_name,bank_code,bank_province,bank_city,bank_name,call_back_url,pay_type,verify_url,sign',
            'pay_controller' => 'BaxibxPay'
        ],
        [
            'name' => 'Rfyw代付模板',
            'request_url' => 'api/v1/Payout/Submit',
            'query_url' => '',
            'param' => 'MchNum,ChannelType,MchOrderNum,Money,CardAccount,CardNum,NotifyUrl,Sign',
            'pay_controller' => 'RfywPay'
        ]
    ]
];