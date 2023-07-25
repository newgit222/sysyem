<?Php

use think\Request;

return
[
    [
        'id'=>1,
        'channel_name'=>'418#招财猫',
        'mchid'=>'100469',
        'key'=>'5fad04d6f3b6c5e5e4e2c1a36fe14e24',
        'order_addr'=>'http://43.225.47.61/api/dfpay/pay',
        'query_addr'=>'http;//www.a.com',
        'notify_addr' =>Request::instance()->domain().'/api/Daifunotify/notify/channel/XxxxPay',
        'contrroler'=>'XxxxPay',
        'admin_id' => 64,
        'notify_ips' => '43.225.47.56,43.225.47.59,43.225.47.61,68.178.164.187,45.207.58.203'
    ],

    [
        'id'=>2,
        'channel_name'=>'风筝代付-备用金',
        'mchid'=>'99889289726263362',
        'key'=>'2C54YcfmcEGDSJ185fscU7YO40nl2mXq',
        'order_addr'=>'http://203.74.125.18:28877/api/pay',
        'query_addr'=>'http;//www.a.com',
        'notify_addr' =>Request::instance()->domain().'/api/Daifunotify/notify/channel/FengZhengDfPay',
        'contrroler'=>'FengZhengDfPay',
        'admin_id' =>64,
        'notify_ips' => '97.74.82.182'
    ],

    [
        'id'=>3,
        'channel_name'=>'永安代付',
        'mchid'=>'53559',
        'key'=>'c04d90ffef37207687e3ef594bec02e4',
        'order_addr'=>'http://w1.beeapi.xyz/api/df_order',
        'query_addr'=>'http;//www.a.com',
        'notify_addr' =>Request::instance()->domain().'/api/Daifunotify/notify/channel/YongAnDfPay',
        'contrroler'=>'YongAnDfPay',
        'admin_id' =>64,
        'notify_ips' => '207.246.107.240'
    ],

    [
        'id'=>4,
        'channel_name'=>'龙腾代付',
        'mchid'=>'100389',
        'key'=>'0c44473cd30797d7d957d6acdb8a4744',
        'order_addr'=>'http://154.204.34.79/api/dfpay/pay',
        'query_addr'=>'http;//www.a.com',
        'notify_addr' =>Request::instance()->domain().'/api/Daifunotify/notify/channel/XxxxPay',
        'contrroler'=>'XxxxPay',
        'admin_id' =>64,
        'notify_ips' => '154.204.34.79'
    ],

];
