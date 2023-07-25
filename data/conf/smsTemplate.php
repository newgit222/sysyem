<?php
return [
    [
        'phone' => '96268',
        'source' => '江西农商银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'账户',
                'money_start'=>'人民币',
                'money_end'=>'元',
                'time_start'=>'账户',
                'time_end'=>'分跨',
            ]
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date'
    ],

    [
        'phone' => '106980009655',
        'source' => '宁夏银行',
        'preg' => [
            [
                'number_start'=>'尾号',
                'number_end'=>'的',
                'money_start'=>'人民币',
                'money_end'=>'元',
                'time_start'=>'账户',
                'time_end'=>'分跨',
            ]
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date'
    ],
  [
        'phone' => '10659805296789',
        'source' => '石嘴山银行',
        'preg' => [
            [
                'number_start'=>'账号',
                'number_end'=>'于',
                'money_start'=>'转入',
                'money_end'=>'元',
                'time_start'=>'账户',
                'time_end'=>'分跨',
            ],
            [
                'number_start'=>'账号',
                'number_end'=>'于',
                'money_start'=>'收入',
                'money_end'=>'元',
                'time_start'=>'账户',
                'time_end'=>'，',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date'
    ],


    [
        'phone' => '95347',
        'source' => '泰隆银行',
        'preg' => [
            [
                'number_start'=>'账户*',
                'number_end'=>'于',
                'money_start'=>'人民币',
                'money_end'=>'元',
                'time_start'=>'联卡在',
                'time_end'=>'发生',
            ]
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' => '10690333136588',
        'source' => '龙江银行',
        'preg' => [
            [
                'number_start'=>'卡尾号',
                'number_end'=>'转',
                'money_start'=>'人民币',
                'money_end'=>',',
                'time_start'=>'联卡在',
                'time_end'=>'发生',
            ],
            [
                'number_start'=>'卡尾号',
                'number_end'=>'银联入账',
                'money_start'=>'人民币',
                'money_end'=>',',
                'time_start'=>'行】',
                'time_end'=>'贵卡',
                'pay_name_start' => '银联入账(',
                'pay_name_end' =>')人民币'
            ]
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' => '106575159638',
        'source' => '黑龙江农信',
        'preg' => [
            [
                'number_start'=>'尾号',
                'number_end'=>'于',
                'money_start'=>'存入',
                'money_end'=>'元',
                'time_start'=>'联卡在',
                'time_end'=>'发生',
            ],
            [
                'number_start'=>'尾号',
                'number_end'=>'的账号',
                'money_start'=>'转账存入',
                'money_end'=>'元',
                'time_start'=>'联卡在',
                'time_end'=>'发生',
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',
    ],

    [
        'phone' => '106980096668',
        'source' => '山东省农村信用社',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的银联卡',
                'money_start'=>'跨行实时汇入交易）人民币',
                'money_end'=>'元',
                'time_start'=>'联卡在',
                'time_end'=>'发生',
            ]
        ],
        'preg_balance' => [
            [
                'balance_start'=>'账户可用余额',
                'balance_end'=>'元 ，备注',
            ]
        ],
        'time_function'=> 'date',
    ],

    [
        'phone' => '95595',
        'source' => '光大银行',
        'preg' => [
            [
                'number_start'=>'尾号',
                'number_end'=>'账户',
                'money_start'=>'转入',
                'money_end'=>'元',
                'time_start'=>'联卡在',
                'time_end'=>'发生',
            ]
        ],
        'time_function'=> 'date',
        'preg_balance' => [
            [
                'balance_start'=>'余额为',
                'balance_end'=>'元',
            ]
        ],


    ],

    [
        'phone' => '106590571332199',
        'source' => '浙信村镇银行',
        'preg' => [
            [
                'number_start'=>'您',
                'number_end'=>'账户',
                'money_start'=>'收入',
                'money_end'=>'，可',
                'time_start'=>'联卡在',
                'time_end'=>'发生',
            ]
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'，',
            ]
        ],

        'time_function'=> 'date',
    ],
    [
        'phone' => '96262',
        'source' => '陕西省农村信用合作社',
        'preg' => [
            [
                'number_start'=>'您',
                'number_end'=>'账户',
                'money_start'=>'转入',
                'money_end'=>'元',
                'time_start'=>'联卡在',
                'time_end'=>'发生',
            ]
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额[',
                'balance_end'=>'].元',
            ]
        ],

        'time_function'=> 'date',
    ],
    [
        'phone' => '95595',
        'source' => '光大银行',
        'preg' => [
            [
                'number_start'=>'尾号',
                'number_end'=>'账户',
                'money_start'=>'转入',
                'money_end'=>'元',
                'time_start'=>'联卡在',
                'time_end'=>'发生',
            ]
        ],
        'time_function'=> 'date',
        'preg_balance' => [
            [
                'balance_start'=>'余额为',
                'balance_end'=>'元',
            ]
        ],


    ],

    [
        'phone' => '106590571332199',
        'source' => '浙信村镇银行',
        'preg' => [
            [
                'number_start'=>'您',
                'number_end'=>'账户',
                'money_start'=>'收入',
                'money_end'=>'，可',
                'time_start'=>'联卡在',
                'time_end'=>'发生',
            ]
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'，',
            ]
        ],

        'time_function'=> 'date',
    ],
    [
        'phone' => '96262',
        'source' => '陕西省农村信用合作社',
        'preg' => [
            [
                'number_start'=>'您',
                'number_end'=>'账户',
                'money_start'=>'转入',
                'money_end'=>'元',
                'time_start'=>'联卡在',
                'time_end'=>'发生',
            ]
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额[',
                'balance_end'=>'].元',
            ]
        ],

        'time_function'=> 'date',
    ],

    [
        'phone' => '95577',
        'source' => '华夏银行',
        'preg' => [
            [
                'number_start'=>'您的账户',
                'number_end'=>'于',
                'money_start'=>'收入人民币',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'收入',
                'pay_name_start' => '付款方',
                'pay_name_end' =>'。【华夏银行】'
            ]
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],

        'time_function'=> 'date',
    ],

    [
        'phone' => '96669',
        'source' => '安徽省农村信用社',
        'preg' => [
            [
                'number_start'=>'您账号',
                'number_end'=>'于',
                'money_start'=>'收入',
                'money_end'=>'元',
                'time_start'=>'联卡在',
                'time_end'=>'发生',
            ],
            [
                'number_start'=>'您账号',
                'number_end'=>'于',
                'money_start'=>'手机转入',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'手机转入',
                'pay_name_start' => '付款方：',
                'pay_name_end' =>'】。'
            ]
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余',
                'balance_end'=>'元',
            ]
        ],

        'time_function'=> 'date',
    ],
    [
        'phone' => '10698000962999',
        'source' => '上海农商银行',
        'preg' => [
            [
                'number_start'=>'您账户',
                'number_end'=>'于',
                'money_start'=>'入人民币',
                'money_end'=>'元',
                'time_start'=>'联卡在',
                'time_end'=>'发生',
            ]
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额为',
                'balance_end'=>'元',
            ]
        ],

        'time_function'=> 'date',
    ],


    [
        'phone' => '1069183996588',
        'source' => '潍坊银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的账户',
                'money_start'=>'存入人民币',
                'money_end'=>'。',
                'time_start'=>'日',
                'time_end'=>'支取',
            ]
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' => '95580',
        'source' => '邮政储蓄银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'账户',
                'money_start'=>'收入金额',
                'money_end'=>'元',
                'time_start'=>'】',
                'time_end'=>'您尾号',
            ]
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' => '106980196688',
        'source' => '贵州农村商业银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'账户',
                'money_start'=>'收入',
                'money_end'=>'元',
                'time_start'=>'账户',
                'time_end'=>'收到',
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'账户',
                'money_start'=>'转入',
                'money_end'=>'元',
                'time_start'=>'账户',
                'time_end'=>'收到',
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'卡',
                'money_start'=>'转入',
                'money_end'=>'元',
                'time_start'=>'卡',
                'time_end'=>'收到',
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ],
            [
                'balance_start'=>'余额',
                'balance_end'=>'。',
            ]
        ],
        'time_function'=> 'date',
    ],

    [
        'phone' => '95561',
        'source' => '兴业银行',
        'preg' => [
            [
                'number_start'=>'账户*',
                'number_end'=>'*汇款',
                'money_start'=>'汇入收入',
                'money_end'=>'元',
                'time_start'=>'】',
                'time_end'=>'您尾号',
                'no_number'=>1,
            ],
            [
                'number_start'=>'账户*',
                'number_end'=>'*跨行代',
                'money_start'=>'收入',
                'money_end'=>'元，余额',
                'time_start'=>'】',
                'time_end'=>'您尾号',
                'no_number'=>1,
            ],

        ],
    ],
    [
        'phone' => '962999',
        'source' => '上海农商银行',
        'preg' => [
            [
                'number_start'=>'您账户',
                'number_end'=>'于',
                'money_start'=>'入人民币',
                'money_end'=>'元',
                'time_start'=>'联卡在',
                'time_end'=>'发生',
                'pay_name_start' => '付款方：',
                'pay_name_end' =>'，'
            ]
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额为',
                'balance_end'=>'元',
            ]
        ],

        'time_function'=> 'date',
    ],



    [
        'phone' => '95599',
        'source' => '中国农业银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'账户',
                'money_start'=>'代付交易人民币',
                'money_end'=>'，',
                'time_start'=>'日',
                'time_end'=>'向您尾号',
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'账户',
                'money_start'=>'转存交易人民币',
                'money_end'=>'，',
                'time_start'=>'于',
                'time_end'=>'向您尾号',
                'pay_name_start' => '【中国农业银行】',
                'pay_name_end' =>'于'
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'账户',
                'money_start'=>'转账交易人民币',
                'money_end'=>'，',
                'time_start'=>'于',
                'time_end'=>'向您尾号',
                'pay_name_start' => '【中国农业银行】',
                'pay_name_end' =>'于'
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'账户',
                'money_start'=>'银联入账交易人民币',
                'money_end'=>'，',
                'time_start'=>'于',
                'time_end'=>'向您尾号',
                'pay_name_start' => '【中国农业银行】',
                'pay_name_end' =>'于'
            ],
            [
                'number_start'=>'【中国农业银行】您尾号',
                'number_end'=>'账户',
                'money_start'=>'完成银联入账交易人民币',
                'money_end'=>'，',
                'time_start'=>'账户',
                'time_end'=>'完成银联',
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'，余额',
                'balance_end'=>'。',
            ]
        ],

        'time_function'=> 'date',
    ],
    [
        'phone' => '农业银行',
        'source' => '中国农业银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'账户',
                'money_start'=>'交易人民币',
                'money_end'=>'，',
                'time_start'=>'日',
                'time_end'=>'向您尾号',
            ]
        ],
        'preg_balance' => [
            [
                'balance_start'=>'，余额',
                'balance_end'=>'。',
            ]
        ],

        'time_function'=> 'date',
    ],


    [
        'phone' =>'106980096033',
        'source' => '贵阳银行',
        'preg' => [
            [
                'number_start'=>'尾号',
                'number_end'=>'于',
                'money_start'=>'存入',
                'money_end'=>']',
                'time_start'=>'于',
                'time_end'=>'存入',
            ],
            [
                'number_start'=>'尾号',
                'number_end'=>'于',
                'money_start'=>'存入',
                'money_end'=>'[',
                'time_start'=>'于',
                'time_end'=>'存入',
                'pay_name_start' => '付款人：',
                'pay_name_end' =>'。'
            ],
        ],

        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'。',
            ],
            [
                'balance_start'=>'余额',
                'balance_end'=>'。付款人',
            ]
        ],

        'time_function'=> 'date',
    ],

    [
        'phone' =>'106980000866',
        'source' => '厦门国际银行',
        'preg' => [
            [
                'number_start'=>'您尾号为',
                'number_end'=>'的账户',
                'money_start'=>'金额CNY',
                'money_end'=>'，活期余额',
                'time_start'=>'于',
                'time_end'=>'存入',
            ],
            [
                'number_start'=>'您尾号为',
                'number_end'=>'的账户',
                'money_start'=>'CNY',
                'money_end'=>',银联入账',
                'time_start'=>'于',
                'time_end'=>'存入',
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'的账户',
                'money_start'=>'金额CNY',
                'money_end'=>'，活期',
                'time_start'=>'于',
                'time_end'=>'存入',

            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'活期余额为CNY',
                'balance_end'=>'。本机',
            ]
        ],


        'time_function'=> 'date',
    ],

    [
        'phone' =>'95566',
        'source' => '中国银行',
        'preg' => [
            [
                'number_start'=>'账户',
                'number_end'=>'，于',
                'money_start'=>'存入人民币',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'存入',
            ],
            [
                'number_start'=>'借记卡账户',
                'number_end'=>'，于',
                'money_start'=>'收入人民币',
                'money_end'=>'元,交',
                'time_start'=>'于',
                'time_end'=>'收入',
            ],
            [
                'number_start'=>'借记卡账户',
                'number_end'=>'，于',
                'money_start'=>'人民币',
                'money_end'=>'元,交',
                'time_start'=>'于',
                'time_end'=>'收入',
            ],
            [
                'number_start'=>'您的借记卡/账户',
                'number_end'=>'于',
                'money_start'=>'入账人民币',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'银联',
                'pay_name_start' => '（',
                'pay_name_end' =>'）'
            ],
            [
                'number_start'=>'账户',
                'number_end'=>'于',
                'money_start'=>'人民币',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'存入',
                'no_number'=>1,
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'交易后余额',
                'balance_end'=>'【中国银',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'10691175712',
        'source' => '渣打银行',
        'preg' => [
            [
                'number_start'=>'您尾号为 ',
                'number_end'=>' 的账户',
                'money_start'=>'收入 CNY ',
                'money_end'=>'。',
                'time_start'=>'[',
                'time_end'=>']',
                'no_number'=>1,
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'【',
                'balance_end'=>'】',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'1069800096511',
        'source' => '长沙银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的银联卡活期账户',
                'money_start'=>'转账转入',
                'money_end'=>'元，',
                'time_start'=>'活期账户',
                'time_end'=>'转账转入',
                'pay_name_start' => '付方：',
                'pay_name_end' =>' '
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元，',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'10691175013',
        'source' => '汇丰银行',
        'preg' => [
            [
                'number_start'=>'***-',
                'number_end'=>' CNY',
                'money_start'=>'CNY ',
                'money_end'=>'+',
                'time_start'=>'【',
                'time_end'=>'】',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'【',
                'balance_end'=>'】，',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'95533',
        'source' => '建设银行',
        'preg' => [
            [
                'number_start'=>'向您尾号',
                'number_end'=>'的储蓄卡',
                'money_start'=>'存入人民币',
                'money_end'=>'元,',
                'time_start'=>'[',
                'time_end'=>']',
                'pay_name_jhdz'=>1,
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'的储蓄卡',
                'money_start'=>'收入人民币',
                'money_end'=>'元,活期',
                'time_start'=>'的储蓄卡',
                'time_end'=>'支付宝提现',
            ],


        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'96262',
        'source' => '陕西信合',
        'preg' => [
            [
                'number_start'=>'信合】您',
                'number_end'=>'账户',
                'money_start'=>'转入',
                'money_end'=>'元,',
                'time_start'=>'账户',
                'time_end'=>',他行',
                'pay_name_start' => '对方户名:',
                'pay_name_end' =>',附言'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'元,余额',
                'balance_end'=>'].元对',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'106980096288',
        'source' => '河南农信',
        'preg' => [
            [
                'number_start'=>'农信】您尾号',
                'number_end'=>'于',
                'money_start'=>'通过支付宝转入',
                'money_end'=>'元，余额',
                'time_start'=>'于',
                'time_end'=>'通过支付宝',
            ],
            [
                'number_start'=>'农信】您尾号',
                'number_end'=>'于',
                'money_start'=>'转入',
                'money_end'=>'元，余额',
                'time_start'=>'于',
                'time_end'=>'通过本行',
                'pay_name_start' => '（',
                'pay_name_end' =>'）转入'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'，余额',
                'balance_end'=>'元。',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'956056',
        'source' => '天津银行',
        'preg' => [
            [
                'number_start'=>'（尾数',
                'number_end'=>'）银联',
                'money_start'=>'存入人民币',
                'money_end'=>'元，',
                'time_start'=>'客户，',
                'time_end'=>'您借记',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额为人民币',
                'balance_end'=>'元，特',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'95555',
        'source' => '招商银行',
        'preg' => [
            [
                'number_start'=>'您账户',
                'number_end'=>'于',
                'money_start'=>'实时转入人民币',
                'money_end'=>'，余额',
                'time_start'=>'于',
                'time_end'=>'他行',
                'pay_name_start' => '付方',
                'pay_name_end' =>' '
            ],

        ],

        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'，付方',
            ]
        ],
        'time_function'=> 'date',

    ],

    [
        'phone' =>'95526',
        'source' => '北京银行',
        'preg' => [
            [
                'number_start'=>'您尾号为',
                'number_end'=>'普卡于',
                'money_start'=>'银联入账收入',
                'money_end'=>'元。',
                'time_start'=>'普卡于',
                'time_end'=>'银联入账',
                'pay_name_start' => '对方户名:',
                'pay_name_end' =>'。'
            ],
            [
                'number_start'=>'您尾号为',
                'number_end'=>'普卡于',
                'money_start'=>'超级网银转账收入',
                'money_end'=>'元。',
                'time_start'=>'普卡于',
                'time_end'=>'银联入账',
                'pay_name_start' => '对方户名:',
                'pay_name_end' =>'。'
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'活期余额',
                'balance_end'=>'元。',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'95226',
        'source' => '北京银行',
        'preg' => [
            [
                'number_start'=>'您尾号为',
                'number_end'=>'普卡于',
                'money_start'=>'银联入账收入',
                'money_end'=>'元。',
                'time_start'=>'普卡于',
                'time_end'=>'银联入账',
                'pay_name_start' => '对方户名:',
                'pay_name_end' =>'。'
            ],
            [
                'number_start'=>'您尾号为',
                'number_end'=>'普卡于',
                'money_start'=>'超级网银转账收入',
                'money_end'=>'元。',
                'time_start'=>'普卡于',
                'time_end'=>'银联入账',
                'pay_name_start' => '对方户名:',
                'pay_name_end' =>'。'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'活期余额',
                'balance_end'=>'元。',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'96500',
        'source' => '云南农信',
        'preg' => [
            [
                'number_start'=>'您的账户*',
                'number_end'=>'*于',
                'money_start'=>'转账转入人民币',
                'money_end'=>'元,余额',
                'time_start'=>'*于',
                'time_end'=>'发生转账',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元.',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'1065908796500',
        'source' => '云南农信',
        'preg' => [
            [
                'number_start'=>'您的账户*',
                'number_end'=>'*于',
                'money_start'=>'银联转入人民币',
                'money_end'=>'元,余额',
                'time_start'=>'*于',
                'time_end'=>'发生银联',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元.',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'95528',
        'source' => '浦发银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'卡人民币',
                'money_start'=>'存入',
                'money_end'=>'元[互联汇入]',
                'time_start'=>'活期',
                'time_end'=>'存入',
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'卡人民币',
                'money_start'=>'存入',
                'money_end'=>'[支付宝',
                'time_start'=>'活期',
                'time_end'=>'存入',
                'pay_name_start' => '支付宝-',
                'pay_name_end' =>'支付宝转'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'可用余额',
                'balance_end'=>'元。',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'95595',
        'source' => '光大银行',
        'preg' => [
            [
                'number_start'=>'向尾号',
                'number_end'=>'账户',
                'money_start'=>'转入',
                'money_end'=>'元，',
                'time_start'=>'账户',
                'time_end'=>'转入',
                'pay_name_start' => '[光大银行]',
                'pay_name_end' =>'向尾号'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额为',
                'balance_end'=>'元，',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
//        'phone' =>'1069040096399',
        'phone' =>'1069084096399',
        'source' => '青海银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的借记卡',
                'money_start'=>'网银转入',
                'money_end'=>'元,',
                'time_start'=>'账户于',
                'time_end'=>'跨行网银',
                'pay_name_start' => '付款人',
                'pay_name_end' =>',活期'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'活期余额',
                'balance_end'=>'元.[',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'1069040096399',
//        'phone' =>'1069084096399',
        'source' => '青海银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的借记卡',
                'money_start'=>'网银转入',
                'money_end'=>'元,',
                'time_start'=>'账户于',
                'time_end'=>'跨行网银',
                'pay_name_start' => '付款人',
                'pay_name_end' =>',活期'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'活期余额',
                'balance_end'=>'元.[',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'106550577996596',
        'source' => '浙江农信',
        'preg' => [
            [
                'number_start'=>'行：您',
                'number_end'=>'账户',
                'money_start'=>'收入',
                'money_end'=>'，可用',
                'time_start'=>'账户',
                'time_end'=>'收入',
                'pay_name_start' => '对方：',
                'pay_name_end' =>'。'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'可用余额',
                'balance_end'=>'，对方',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'1069800096008',
        'source' => '江苏农信',
        'preg' => [
            [
                'number_start'=>'您账户',
                'number_end'=>'于',
                'money_start'=>'入账人民币',
                'money_end'=>'元(付方',
                'time_start'=>'账户',
                'time_end'=>'收入',
                'pay_name_start' => '付方:',
                'pay_name_end' =>'）'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元。',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'95319',
        'source' => '江苏银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'账户于',
                'money_start'=>'转入人民币',
                'money_end'=>'元，',
                'time_start'=>'账户于',
                'time_end'=>'转入人',
                'pay_name_start' => '对方户名为',
                'pay_name_end' =>'，摘要'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元，',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'96668',
        'source' => '山东农商银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的银联卡在',
                'money_start'=>'人民币',
                'money_end'=>'元，',
                'time_start'=>'银联卡在',
                'time_end'=>'发生（跨行实时汇入交易',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'账户可用余额',
                'balance_end'=>'元 ，备注',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'96688',
        'source' => '内蒙古农信',
        'preg' => [
            [
                'number_start'=>'为您尾号',
                'number_end'=>'的账号上',
                'money_start'=>'号上汇入',
                'money_end'=>'元，',
                'time_start'=>'于',
                'time_end'=>'为您',
                'pay_name_start' => '【内蒙古农信】',
                'pay_name_end' =>'于'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'95588',
        'source' => '工商银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'卡',
                'money_start'=>'收入(他行汇入)',
                'money_end'=>'元，',
                'time_start'=>'卡',
                'time_end'=>'工商银行',
                'pay_name_start' => '对方户名：',
                'pay_name_end' =>'，对方'
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'卡',
                'money_start'=>'收入(网转)',
                'money_end'=>'元，',
                'time_start'=>'卡',
                'time_end'=>'手机银行',
                'pay_name_start' => '对方户名：',
                'pay_name_end' =>'，对方'
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'卡',
                'money_start'=>'网上银行收入(银联入账)',
                'money_end'=>'元，',
                'time_start'=>'卡',
                'time_end'=>'网上银行',
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'卡',
                'money_start'=>'支付宝)',
                'money_end'=>'元，',
                'time_start'=>'卡',
                'time_end'=>'快捷支付收入',
                'pay_name_start' => '收入(',
                'pay_name_end' =>'支付宝转账'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'106575203101096',
        'source' => '长江银行',
        'preg' => [
            [
                'number_start'=>'您的尾号为',
                'number_end'=>'的长江卡在',
                'money_start'=>'金额为',
                'money_end'=>'元',
                'time_start'=>'长江卡在',
                'time_end'=>'发生一笔',
                'pay_name_start' => '汇款人名称为',
                'pay_name_end' =>'）业务'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额为',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'106980096369',
        'source' => '河北农信',
        'preg' => [
            [
                'number_start'=>'您的账户(尾号',
                'number_end'=>')于',
                'money_start'=>'银联转入',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'银联转入',
            ],
            [
                'number_start'=>'您的账户(尾号',
                'number_end'=>')于',
                'money_start'=>'其他渠道转入',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'银联转入',
            ],
            [
                'number_start'=>'您的账户(尾号',
                'number_end'=>')于',
                'money_start'=>'支付宝转账',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'日',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'106980096888',
        'source' => '青海农信',
        'preg' => [
            [
                'number_start'=>'您的尾号为',
                'number_end'=>'的账户',
                'money_start'=>'人民币',
                'money_end'=>'元',
                'time_start'=>'人民币',
                'time_end'=>'元',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'956033',
        'source' => '东莞银行',
        'preg' => [
            [
                'number_start'=>'您账户[',
                'number_end'=>']于',
                'money_start'=>'转帐收入人民币',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'转帐',
            ],
            [
                'number_start'=>'您账户[',
                'number_end'=>']于',
                'money_start'=>'支付宝付款收入人民币',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'转帐',
            ],
            [
                'number_start'=>'您账户[',
                'number_end'=>']于',
                'money_start'=>'财付通付款收入人民币',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'转帐',
            ],
            [
                'number_start'=>'您账户[',
                'number_end'=>']于',
                'money_start'=>'京东付款收入人民币',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'转帐',
            ],
            [
                'number_start'=>'您账户',
                'number_end'=>'于',
                'money_start'=>'收入人民币',
                'money_end'=>'，',
                'time_start'=>'于',
                'time_end'=>'转帐',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额人民币',
                'balance_end'=>'元',
            ],
            [
                'balance_start'=>'余额',
                'balance_end'=>'。',
            ]


        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'95508',
        'source' => '广发银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'卡',
                'money_start'=>'收入人民币',
                'money_end'=>'元(入金交易)',
                'time_start'=>'卡',
                'time_end'=>'收入',
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'卡',
                'money_start'=>'收入人民币',
                'money_end'=>'元(网银入账)',
                'time_start'=>'卡',
                'time_end'=>'收入',
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'卡',
                'money_start'=>'收入人民币',
                'money_end'=>'元（(银联入账)',
                'time_start'=>'卡',
                'time_end'=>'收入',
                'pay_name_start' => '（(银联入账)-',
                'pay_name_end' =>'）。账户'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'账户余额:',
                'balance_end'=>'元。',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'96568',
        'source' => '湖北农信',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'银行卡',
                'money_start'=>'转入',
                'money_end'=>'元',
                'time_start'=>'银行卡',
                'time_end'=>'从',
                'pay_name_start' => '从',
                'pay_name_end' =>'转入'
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'106980096568',
        'source' => '湖北农信',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'银行卡',
                'money_start'=>'转入',
                'money_end'=>'元',
                'time_start'=>'银行卡',
                'time_end'=>'从',
                'pay_name_start' => '从',
                'pay_name_end' =>'转入'
            ]

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',

    ],

    [
        'phone' =>'1065756796500',
        'source' => '云南农信',
        'preg' => [
            [
                    'number_start'=>'您的账户*',
                    'number_end'=>'*于',
                    'money_start'=>'转账转入人民币',
                    'money_end'=>'元,余额',
                    'time_start'=>'*于',
                    'time_end'=>'发生转账',

            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',

    ],

    [
        'phone' =>'1069800096368',
        'source' => '河北银行',
        'preg' => [
                [
                    'number_start'=>'尾号',
                    'number_end'=>'银联借记卡',
                    'money_start'=>'发生网银来账+',
                    'money_end'=>'元',
                    'time_start'=>'银联借记卡',
                    'time_end'=>'发生转账',
                    'pay_name_start' => '对方',
                    'pay_name_end' =>'。'
                ],
            [
                'number_start'=>'尾号',
                'number_end'=>'银联借记卡',
                'money_start'=>'银联入账（贷记）+',
                'money_end'=>'元',
                'time_start'=>'银联借记卡',
                'time_end'=>'发生转账',
                'pay_name_start' => '对方',
                'pay_name_end' =>'。'
            ],
            [
                'number_start'=>'尾号',
                'number_end'=>'银联借记卡',
                'money_start'=>'发生+',
                'money_end'=>'元',
                'time_start'=>'银联借记卡',
                'time_end'=>'发生+',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'活期余额：',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'106980096533',
        'source' => '富滇银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的账户',
                'money_start'=>'收入￥',
                'money_end'=>'，',
                'time_start'=>'账户',
                'time_end'=>'收入',
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'的账户',
                'money_start'=>'银联入账￥',
                'money_end'=>'，',
                'time_start'=>'账户',
                'time_end'=>'收入',
            ],


        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'。',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'10690879696368',
        'source' => '承德银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的账号',
                'money_start'=>'网银贷记来帐',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'网银',
                'pay_name_start' => '对方户名:',
                'pay_name_end' =>'.'
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'的账号于',
                'money_start'=>'转入',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'网银',
                'pay_name_start' => '对方户名:',
                'pay_name_end' =>' '
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',

    ],

    [
        'phone' =>'10690329296368',
        'source' => '张家口银行',
        'preg' => [
            [
                'number_start'=>'向您尾号',
                'number_end'=>'的银行卡于',
                'money_start'=>'通过网银互联转入',
                'money_end'=>'元',
                'time_start'=>'的银行卡于',
                'time_end'=>'通过',
//                'pay_name_start' => '银行】',
//                'pay_name_end' =>'向您尾号'
            ],
            [
                'number_start'=>'向您尾号',
                'number_end'=>'的银行卡于',
                'money_start'=>'通过城银清支付渠道转入',
                'money_end'=>'元',
                'time_start'=>'的银行卡于',
                'time_end'=>'通过',
//                'pay_name_start' => '银行】',
//                'pay_name_end' =>'向您尾号'
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'的银行卡',
                'money_start'=>'有限公司转入',
                'money_end'=>'元',
                'time_start'=>'的银行卡于',
                'time_end'=>'通过',
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'的借记卡',
                'money_start'=>'银联代付，金额',
                'money_end'=>'元',
                'time_start'=>'借记卡于',
                'time_end'=>'银联代付',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'10691659696888',
        'source' => '辽宁农信',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的账户',
                'money_start'=>'存入',
                'money_end'=>'元',
                'time_start'=>'的账户',
                'time_end'=>'网银',
                'pay_name_start' => '][',
                'pay_name_end' =>']，活期余额'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'活期余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',

    ],
    [
        'phone' =>'95559',
        'source' => '交通银行',
        'preg' => [
            [
                'number_start'=>'您尾号*',
                'number_end'=>'的卡于',
                'money_start'=>'网络支付转入',
                'money_end'=>'元，交',
                'time_start'=>'于',
                'time_end'=>'网络',
            ],
            [
                'number_start'=>'您尾号*',
                'number_end'=>'的卡于',
                'money_start'=>'在支付宝转入',
                'money_end'=>'元，交',
                'time_start'=>'于',
                'time_end'=>'网络',
            ],
            [
                'number_start'=>'贵账户*',
                'number_end'=>'于',
                'money_start'=>'转入资金',
                'money_end'=>'元',
                'time_start'=>'的银行卡于',
                'time_end'=>'网银',
                'pay_name_start' => '对方户名：',
                'pay_name_end' =>'，附言'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'交易后余额为',
                'balance_end'=>'元',
            ],
            [
                'balance_start'=>'现余额',
                'balance_end'=>'元',
            ]
        ],
        'time_function'=> 'date',

    ],

    [
        'phone' =>'95537',
        'source' => '哈尔滨银行',
        'preg' => [
            [
                'number_start'=>'您账户',
                'number_end'=>'于',
                'money_start'=>'转入人民币',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'转入',
            ],
            [
                'number_start'=>'您尾号为',
                'number_end'=>'的卡于',
                'money_start'=>')人民币',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'银联入账',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],

    [
        'phone' =>'10690329036588',
        'source' => '龙江银行',
        'preg' => [
            [
                'number_start'=>'贵卡尾号',
                'number_end'=>'转入人民币',
                'money_start'=>'转入人民币',
                'money_end'=>',余额',
                'time_start'=>' ',
                'time_end'=>'贵卡',
            ],
            [
                'number_start'=>'贵卡尾号',
                'number_end'=>'银联入账(',
                'money_start'=>')人民币',
                'money_end'=>',余额',
                'time_start'=>' ',
                'time_end'=>'贵卡',
                'pay_name_start' => '银联入账(',
                'pay_name_end' =>')人民币'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>',余额',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'10659031796368',
        'source' => '廊坊银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的账户',
                'money_start'=>'提现存入人民币 ',
                'money_end'=>'元 余额',
                'time_start'=>'账户',
                'time_end'=>' 支付',
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'的账户',
                'money_start'=>'汇兑存入人民币 ',
                'money_end'=>'元 余额',
                'time_start'=>'账户',
                'time_end'=>' 支付',
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'的账户',
                'money_start'=>')存入人民币 ',
                'money_end'=>'元 余额',
                'time_start'=>'账户',
                'time_end'=>' 支付',
                'pay_name_start' => '银联入账(',
                'pay_name_end' =>')存入人民'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额  ',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],

    [
        'phone' =>'10690333336588',
        'source' => '龙江银行',
        'preg' => [
            [
                'number_start'=>'贵卡尾号',
                'number_end'=>'转入人民币',
                'money_start'=>'转入人民币',
                'money_end'=>',余额',
                'time_start'=>' ',
                'time_end'=>'贵卡',
            ],
            [
                'number_start'=>'贵卡尾号',
                'number_end'=>'银联入账(',
                'money_start'=>')人民币',
                'money_end'=>',余额',
                'time_start'=>' ',
                'time_end'=>'贵卡',
                'pay_name_start' => '银联入账(',
                'pay_name_end' =>')人民币'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>',余额',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'106550577196',
        'source' => '稠州银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的银联卡',
                'money_start'=>'收入RMB',
                'money_end'=>',余额',
                'time_start'=>'银联卡于',
                'time_end'=>'超网',
                'pay_name_start' => '汇款人:',
                'pay_name_end' =>'.【稠州银行】'
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额RMB',
                'balance_end'=>',汇款行',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'1068020195511',
        'source' => '平安银行',
        'preg' => [
            [
                'number_start'=>'您存款账户',
                'number_end'=>'于',
                'money_start'=>'跨行转账转入人民币',
                'money_end'=>'元付款人',
                'time_start'=>'于',
                'time_end'=>'跨行',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'活期余额人民币',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'106910096888',
        'source' => '吉林农信',
        'preg' => [
            [
                'number_start'=>'您尾号为',
                'number_end'=>'的账户',
                'money_start'=>'存入',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'存入',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'10698010096666',
        'source' => '吉林银行',
        'preg' => [
            [
                'number_start'=>'尾号',
                'number_end'=>'的卡',
                'money_start'=>'入人民币',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'存入',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额人民币',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'10655878840196518',
        'source' => '山西农信',
        'preg' => [
            [
                'number_start'=>'您尾号为',
                'number_end'=>'的账户',
                'money_start'=>'转入:',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'存入',
            ],
            [
                'number_start'=>'您尾号为',
                'number_end'=>'的账户',
                'money_start'=>'支付宝转账:',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'存入',
            ],


        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额:',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'95568',
        'source' => '民生银行',
        'preg' => [
            [
                'number_start'=>'账户*',
                'number_end'=>'于',
                'money_start'=>'存入￥',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'存入',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'可用余额',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'10691839496588',
        'source' => '德州银行',
        'preg' => [
            [
                'number_start'=>'尾号为',
                'number_end'=>'的账户',
                'money_start'=>'进账(手机银行转账)',
                'money_end'=>'人民币',
                'time_start'=>'于',
                'time_end'=>'进账',
            ],
            [
                'number_start'=>'尾号为',
                'number_end'=>'的账户',
                'money_start'=>'进账(网银互联网银互联汇兑)',
                'money_end'=>'人民币',
                'time_start'=>'于',
                'time_end'=>'进账',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'现余额',
                'balance_end'=>'人民币',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'10657304696588',
        'source' => '德州银行',
        'preg' => [
            [
                'number_start'=>'尾号为',
                'number_end'=>'的账户',
                'money_start'=>'进账(手机银行转账)',
                'money_end'=>'人民币',
                'time_start'=>'于',
                'time_end'=>'进账',
            ],
            [
                'number_start'=>'尾号为',
                'number_end'=>'的账户',
                'money_start'=>'进账(网银互联网银互联汇兑)',
                'money_end'=>'人民币',
                'time_start'=>'于',
                'time_end'=>'进账',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'现余额',
                'balance_end'=>'人民币',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'9555801',
        'source' => '中信银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的中信卡',
                'money_start'=>'存入人民币',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'，跨',
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额为人民币',
                'balance_end'=>'元。',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'106575510096688',
        'source' => '中原银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的卡',
                'money_start'=>'转入',
                'money_end'=>'元',
                'time_start'=>'卡',
                'time_end'=>'支付',
                'pay_name_start' => '对方户名：',
                'pay_name_end' =>'。'
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'可用余额',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'95541',
        'source' => '渤海银行',
        'preg' => [
            [
                'number_start'=>'贵卡',
                'number_end'=>'于',
                'money_start'=>'入账人民币',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'在网银',
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'可用余额',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'106575096306',
        'source' => '邢台银行',
        'preg' => [
            [
                'number_start'=>'您个人卡账号',
                'number_end'=>'于',
                'money_start'=>'转入人民币',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'网银',
                'pay_name_start' => '对方',
                'pay_name_end' =>',详询'
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'10691839696588',
        'source' => '枣庄银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的卡',
                'money_start'=>'存入(网银互联汇款)',
                'money_end'=>'元',
                'time_start'=>'的卡',
                'time_end'=>'通过',
                'pay_name_start' => '汇款人:',
                'pay_name_end' =>'，账户'
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'1065502896633',
        'source' => '四川农信',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的账户',
                'money_start'=>'到账人民币',
                'money_end'=>'元(网银互联汇兑来账)',
                'time_start'=>'于',
                'time_end'=>'到账',
                'pay_name_start' => '户名为',
                'pay_name_end' =>'。'
            ],
            [
                'number_start'=>'您尾号',
                'number_end'=>'的账户',
                'money_start'=>'到账人民币',
                'money_end'=>'元(支付宝转账',
                'time_start'=>'于',
                'time_end'=>'到账',
                'pay_name_start' => '支付宝转账-',
                'pay_name_end' =>'支付宝转账)'
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'10693882282136',
        'source' => '江西裕民银行',
        'preg' => [
            [
                'number_start'=>'您尾号*',
                'number_end'=>'的银联卡',
                'money_start'=>'转入',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'跨行',
            ],

        ],
        'preg_balance' => [
            [
                'balance_start'=>'当前余额为',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'10659057196596',
        'source' => '浙江农信',
        'preg' => [
            [
                'number_start'=>'您',
                'number_end'=>'账户',
                'money_start'=>'收入',
                'money_end'=>'，可',
                'time_start'=>'账户',
                'time_end'=>'收入',
                'pay_name_start' => '对方：',
                'pay_name_end' =>'。'
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'可用余额',
                'balance_end'=>'，对',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'10690550067395415',
        'source' => '定兴丰源村银',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'账户',
                'money_start'=>'存入',
                'money_end'=>'元',
                'time_start'=>'账户',
                'time_end'=>'存入',
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'1069033396262',
        'source' => '陕西信合',
        'preg' => [
            [
                'number_start'=>'【陕西信合】您',
                'number_end'=>'账户',
                'money_start'=>'跨行转入',
                'money_end'=>'元',
                'time_start'=>'账户',
                'time_end'=>',他',
                'pay_name_start' => '对方户名:',
                'pay_name_end' =>',附言'
            ],
            [
                'number_start'=>'【陕西信合】您',
                'number_end'=>'账户',
                'money_start'=>'支付宝转入',
                'money_end'=>'元',
                'time_start'=>'账户',
                'time_end'=>',支付宝',
            ],
            [
                'number_start'=>'【陕西信合】您',
                'number_end'=>'的卡',
                'money_start'=>'银联入账(转账)人民币',
                'money_end'=>'元',
                'time_start'=>'卡',
                'time_end'=>',银联',
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额[',
                'balance_end'=>'].元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'10690333196262',
        'source' => '陕西信合',
        'preg' => [
            [
                'number_start'=>'【陕西信合】您',
                'number_end'=>'账户',
                'money_start'=>'跨行转入',
                'money_end'=>'元',
                'time_start'=>'账户',
                'time_end'=>',他',
                'pay_name_start' => '对方户名:',
                'pay_name_end' =>',附言'
            ],
            [
                'number_start'=>'【陕西信合】您',
                'number_end'=>'账户',
                'money_start'=>'支付宝转入',
                'money_end'=>'元',
                'time_start'=>'账户',
                'time_end'=>',支付宝',
            ],
            [
                'number_start'=>'【陕西信合】您',
                'number_end'=>'的卡',
                'money_start'=>'银联入账(转账)人民币',
                'money_end'=>'元',
                'time_start'=>'的卡',
                'time_end'=>',银联入账',
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额[',
                'balance_end'=>'].元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'95367',
        'source' => '武汉农商行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的借记卡',
                'money_start'=>'转入',
                'money_end'=>'元',
                'time_start'=>'于',
                'time_end'=>'经',
                'pay_name_start' => '付款方',
                'pay_name_end' =>'。存'
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
    [
        'phone' =>'10690329295539',
        'source' => '新安融兴村镇银行',
        'preg' => [
            [
                'number_start'=>'您尾号',
                'number_end'=>'的卡',
                'money_start'=>'转入',
                'money_end'=>'元',
                'time_start'=>'交易时间',
                'time_end'=>'.',
            ],
        ],
        'preg_balance' => [
            [
                'balance_start'=>'余额',
                'balance_end'=>'元',
            ],
        ],
        'time_function'=> 'date',
    ],
];
