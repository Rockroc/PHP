<?php
// 使用说明：只需修改下面的appid和appsecret  为微信公众平台（已认证）的就可以
$appId = 'xxx';
$appsecret = 'xxx';

$timestamp = time();
$jsapi_ticket = make_ticket($appId, $appsecret);
$nonceStr = make_nonceStr();
$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$signature = make_signature($nonceStr, $timestamp, $jsapi_ticket, $url);

function make_nonceStr()
{
    $codeSet = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    for ($i = 0; $i < 16; $i++) {
        $codes[$i] = $codeSet[mt_rand(0, strlen($codeSet) - 1)];
    }
    $nonceStr = implode($codes);
    return $nonceStr;
}

function make_signature($nonceStr, $timestamp, $jsapi_ticket, $url)
{
    $tmpArr = array(
        'noncestr' => $nonceStr,
        'timestamp' => $timestamp,
        'jsapi_ticket' => $jsapi_ticket,
        'url' => $url
    );
    ksort($tmpArr, SORT_STRING);
    $string1 = http_build_query($tmpArr);
    $string1 = urldecode($string1);
    $signature = sha1($string1);
    return $signature;
}

function make_ticket($appId, $appsecret)
{
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode(file_get_contents("access_token.json"));
    if ($data->expire_time < time()) {
        $TOKEN_URL = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appId . "&secret=" . $appsecret;
        $json = file_get_contents($TOKEN_URL);
        $result = json_decode($json, true);
        $access_token = $result['access_token'];
        if ($access_token) {
            $data->expire_time = time() + 7000;
            $data->access_token = $access_token;
            $fp = fopen("access_token.json", "w");
            fwrite($fp, json_encode($data));
            fclose($fp);
        }
    } else {
        $access_token = $data->access_token;
    }

    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode(file_get_contents("jsapi_ticket.json"));
    if ($data->expire_time < time()) {
        $ticket_URL = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $access_token . "&type=jsapi";
        $json = file_get_contents($ticket_URL);
        $result = json_decode($json, true);
        $ticket = $result['ticket'];
        if ($ticket) {
            $data->expire_time = time() + 7000;
            $data->jsapi_ticket = $ticket;
            $fp = fopen("jsapi_ticket.json", "w");
            fwrite($fp, json_encode($data));
            fclose($fp);
        }
    } else {
        $ticket = $data->jsapi_ticket;
    }

    return $ticket;
}

?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="target-densitydpi=320,width=640,user-scalable=no">
    <title>同学米店</title>
    <link rel="stylesheet" type="text/css" href="./css/default.css">
    <link rel="stylesheet" type="text/css" href="./css/animate.css">
    <link rel="stylesheet" type="text/css" href="./css/index.css">
    <style>

    </style>
</head>
<body>
<div class="main">
    <section class="loading page">
        <img class="logo" src="./images/logo.png" alt=""/>

        <div class="middle">
            <img src="./images/clock.png" class="clock animated infinite tada" alt=""/>
            <img class="cook" src="./images/cook.png" alt=""/>
        </div>
        <div class="bottom"></div>
    </section>
    <section class="intro page">
        <div class="logo"><img src="./images/logo.png" alt=""/></div>
        <div class="container">
            <img class="top" src="./images/yiqibao.png" alt=""/>

            <div class="slider-main">
                <div class="slider-container">
                    <div class="slider"><img src="./images/slide1.png" alt=""/></div>
                    <div class="slider"><img src="./images/slide2.png" alt=""/></div>
                    <div class="slider"><img src="./images/slide3.png" alt=""/></div>
                </div>
                <div class="pagination-container"></div>
                <div class="comment-container">
                    <span class="p1">根据粽子下方箭头指示顺序点击九宫格内方块</span>
                    <span class="p2"><img src="./images/skip.png"/></span>
                </div>
            </div>
        </div>
        <div class="bottom"></div>
        <div class="game-window">
            <div class="dragon"><img src="./images/longzi.png" alt=""/></div>
            <div class="prepare hvcenter">
                <span>预备</span>

                <span class="span1">0</span>
            </div>
            <div class="zong">

            </div>
            <div class="error hvcenter"><img src="./images/error.png" alt=""/></div>
            <div class="direction">
            </div>
        </div>
        <div class="game-panel">
            <div class="panel">
                <div class="btn btn1" rsi="1"></div>
                <div class="btn btn2" rsi="2"></div>
                <div class="btn btn3" rsi="3"></div>
                <div class="btn btn4" rsi="4"></div>
                <div class="btn btn5" rsi="5"></div>
                <div class="btn btn6" rsi="6"></div>
                <div class="btn btn7" rsi="7"></div>
                <div class="btn btn8" rsi="8"></div>
            </div>
            <div class="info">
                <p class="score"><em>0</em>件</p>

                <p class="sec"><em>30</em>'秒</p>
            </div>
        </div>
        <div class="over-window">
            <div class="logo"><img src="./images/logo.png" alt=""/></div>
            <div class="wrap">
                <div class="timeout"><img src="./images/timeout.png" alt=""/></div>
                <div class="gameoverlogo"><img src="./images/gameoverlogo.png" alt=""/></div>
                <p class="comment">我包了<span class="x">0</span>个粽子，获得<span class="y">0</span>级包粽子证书<br/>相当于<span
                        class="z">0</span>！</p>
            </div>
        </div>
        <div class="over-panel">
            <img class="shareBtn animated infinite pulse" src="./images/shareBtn.png" alt=""/>
            <img class="restartBtn animated infinite pulse" src="./images/restartBtn.png" alt=""/>
            <img class="tenBtn animated infinite pulse" src="./images/tenBtn.png" alt=""/>

            <div class="bottom"></div>
        </div>
        <div class="share-window">
            <div class="mask"></div>
            <img class="hand animated infinite bounce" src="./images/hand.png" alt=""/>
            <img class="comment" src="./images/sharecomment.png" alt=""/>

            <div class="sharelogo"><img src="./images/sharelogo.png" alt=""/></div>

        </div>
    </section>
</div>
<script src="./js/jquery-2.1.4.min.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="./js/global.js"></script>
<script src="./js/app.js"></script>
<script>
    $(function () {
        wx.config({
            debug: false,
            appId: '<?php echo $appId ?>', // 必填，公众号的唯一标识
            timestamp: <?php echo $timestamp ?>, // 必填，生成签名的时间戳
            nonceStr: '<?php echo $nonceStr ?>',// 必填，生成签名的随机串
            signature: '<?php echo $signature ?>',// 必填，签名
            jsApiList: [ // 必填，需要使用的JS接口列表
                'checkJsApi',
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareWeibo',
                'hideMenuItems',
                'showMenuItems',
                'hideAllNonBaseMenuItem',
                'showAllNonBaseMenuItem',
                'translateVoice',
                'startRecord',
                'stopRecord',
                'onRecordEnd',
                'playVoice',
                'pauseVoice',
                'stopVoice',
                'uploadVoice',
                'downloadVoice',
                'chooseImage',
                'previewImage',
                'uploadImage',
                'downloadImage',
                'getNetworkType',
                'openLocation',
                'getLocation',
                'hideOptionMenu',
                'showOptionMenu',
                'closeWindow',
                'scanQRCode',
                'chooseWXPay',
                'openProductSpecificView',
                'addCard',
                'chooseCard',
                'openCard'
            ]
        });
    });
</script>
</body>
</html>