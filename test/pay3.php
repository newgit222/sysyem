<?php ?>
<!DOCTYPE html>
<html>

<head>
    <title>付款</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- <link href="/static/css/bootstrap-4.3.1-min.css" rel="stylesheet"> -->
<script src="/static/js/jquery-3.4.1.min.js"></script>
<!-- <script src="/static/layer/layer.js"></script> -->
<script src="/static/js/clipboard.min.js"></script>
<link href="/static/layui/css/layui.css" rel="stylesheet">
<script src="/static/layui/layui.js"></script>
<script src="/static/js/qrcode2.min.js"></script>
<style>
  .text-red {
    color: red
  }

  .text-center {
    text-align: center
  }

  .mt-10 {
    margin-top: 10px
  }

  img {
    max-width: 100%;
  }

  .mask {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background-color: #000000;
    display: none;
  }

  .mask .mask-tips {
    color: #fff;
    text-align: center;
    margin-top: 30%;
  }
  #qrcode img{
      display: inline;
      margin:auto;
      margin-bottom: 10px;
  }
  .pay-money{
      font-size:1.6rem;
      margin:8px 0;
  }
</style>

    <style>
        .money {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            margin: 10px 0px
        }

        .tips {
            color: red;
            margin: 5px;
            font-size: 14px;
            text-align: left;
        }

        .ordernum {
            margin-top: 10px;
            text-align: center;
            font-size: 16px;
        }

        .pay-img {
            text-align: center;
            margin-top: 5px;
        }

        .pay-img img {
            max-width: 60%;
            margin: auto
        }

        .end-tips {
            color: red;
            text-align: center;
            margin-top: 10px;
            font-size: 14px
        }

        .end-tips2 {
            color: #656565;
            text-align: center;
            font-size: 13px;
            margin-top: 5px;
        }

        .cut {
            text-align: center;
        }
        .top{
            text-align: center;
            height:50px;
            line-height: 50px;
            font-size: 18px;
        }
        .midTip{
            margin:10px 0;
            font-size:15px;
            text-align: left;
        }
        .midTip2{
            margin: 10px 0;
            font-size:16px;
            font-weight: bold;
            text-align: center;
            color:red;
        }
    </style>
</head>

<body>
<div class="layui-container">
    <div class="top"><img src="/static/image/wx.png" width="50px">微信群红包支付</div>
    <div class="money"><span style="font-size:20px;color:red"> ¥<?php echo $_GET['order_pay_price'];?></span></div>
    <p style="color:red;font-size: 16px;">重要提示：</p>
    <p>1.进群之后千万不要直接发红包，等待群主说发之后再发红包，必须是群主说！！！</p>
    <p>2.进入群聊后发现有他人同时进群的，请不要发红包,避免有骗子潜入准备偷领你红包！</p>
    <p>3.如有人私聊你让私发红包上分或者拉你去做其他有目的的事情都是骗子，切勿上当受骗！</p>
    <p>4.请在订单有效时间内进群付款（有效时间五分钟），切勿未下单直接进群付款！</p>
    <p>5.一个单号对应一个群二维码，请勿重复付款</p>
    <p></p>
    <hr/>
    <div class="text-center">
        群名字:<?php echo $_GET['account_name'];?>
    </div>
	<div class="ordernum">订单号：<?php echo $_GET['trade_no'];?></div>
    <div class="text-center">
        <img src="<?php echo '/'.$_GET['qun_image'];?>" width="200px">
    </div>
    <hr/>
    
   
    </div>
    <div class="end-tips2">
        1-3分钟不到账，请立即联系客服
    </div>
    <div class="mask">
        <p class="mask-tips">订单已过期，请重新下单</p>
    </div>
</div>
</body>
</html>

