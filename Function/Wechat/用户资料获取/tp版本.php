<?php
/**
 * 微信封装类
 */

//获取公众号的配置信息
function get_token_appinfo($token = '')
{
    empty ($token) && $token = get_token();
    $map ['wechat_falg'] = $token;
    $info = M('Wxch_config')->field('id,wechat_falg',true)->where($map)->find();
    return $info;
}

// 获取当前用户的Token(医院)
function get_token($token = NULL)
{
    if ($token !== NULL) {
        session('token', $token);
    } elseif (!empty ($_REQUEST ['token'])) {
        session('token', $_REQUEST ['token']);
    }
    $token = session('token');
    if (empty ($token)) {
        return -1;
    }
    return $token;
}

//判断是否在微信浏览器里打开
function isWeixinBrowser()
{
    $agent = $_SERVER ['HTTP_USER_AGENT'];
    if (!strpos($agent, "icroMessenger")) {
        return false;
    }
    return true;
}


// 获取当前用户的OpenId
function get_openid($openid = NULL)
{
    if ($openid !== NULL) {
        session('openid', $openid);
    } elseif (!empty ($_REQUEST ['openid'])) {
        session('openid', $_REQUEST ['openid']);
    }
    $openid = session('openid');

    $isWeixinBrowser = isWeixinBrowser();
    if ((empty ($openid) || $openid=='-1') && $isWeixinBrowser) {
        $callback = GetCurUrl();
        //$callback = str_replace('114.215.175.151', 'test.balincms.com', $callback);
        OAuthWeixin($callback);
    }

    if (empty ($openid)) {
        return -1;
    }

    return $openid;
}


//通过授权回调获取用户openid
function OAuthWeixin($callback)
{
    $isWeixinBrowser = isWeixinBrowser();
    $info = get_token_appinfo();
    if (!$isWeixinBrowser || empty ($info ['appid'])) {
        redirect($callback . '&openid=-1');
    }
    $param ['appid'] = $info ['appid'];
    if (!isset ($_GET ['getOpenId'])) {
        $param ['redirect_uri'] = $callback . '&getOpenId=1';
        $param ['response_type'] = 'code';
        $param ['scope'] = 'snsapi_base';
        //$param ['scope'] = 'snsapi_userinfo';
        $param ['state'] = 123;
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($param) . '#wechat_redirect';
        redirect($url);
    } elseif ($_GET ['state']) {
        $param ['secret'] = $info ['appsecret'];
        $param ['code'] = I('code');
        $param ['grant_type'] = 'authorization_code';
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query($param);
        //$content = file_get_contents($url);
        $content = getCurl($url);
        $content = json_decode($content, true);
        redirect($callback . '&openid=' . $content ['openid']);
    }
}
// php获取当前访问的完整url地址
function GetCurUrl()
{
    $url = 'http://';
    if (isset ($_SERVER ['HTTPS']) && $_SERVER ['HTTPS'] == 'on') {
        $url = 'https://';
    }
    if ($_SERVER ['SERVER_PORT'] != '80') {
        $url .= $_SERVER ['HTTP_HOST'] . ':' . $_SERVER ['SERVER_PORT'] . $_SERVER ['REQUEST_URI'];
    } else {
        $url .= $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
    }
    // 兼容后面的参数组装
    if (stripos($url, '?') === false) {
        $url .= '?t=' . time();
    }
    return $url;
}


// 获取access_token，自动带缓存功能
function get_access_token($token = '')
{
    empty ($token) && $token = get_token();
    $key = 'access_token_' . $token;
    $res = S($key);
    if ($res !== false) {
        return $res;
    }
    $info = get_token_appinfo($token);
    if (empty ($info ['appid']) || empty ($info ['appsecret'])) {
        S($key, 0, 7200);

        return 123;
    }

    $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $info ['appid'] . '&secret=' . $info ['appsecret'];

    $tempArr = json_decode(file_get_contents($url), true);

    if (@array_key_exists('access_token', $tempArr)) {
        S($key, $tempArr ['access_token'], 7200);
        return $tempArr ['access_token'];
    } else {
        return 0;
    }
}


// 通过openid获取微信用户基本信息,此功能只有认证的服务号才能用
function getWeixinUserInfo($openid, $token='')
{
    $access_token = get_access_token($token);
    if (empty ($access_token)) {
        return false;
    }

    $param2 ['access_token'] = $access_token;
    $param2 ['openid'] = $openid;
    $param2 ['lang'] = 'zh_CN';

    $url = 'https://api.weixin.qq.com/cgi-bin/user/info?' . http_build_query($param2);

    $content = file_get_contents($url);
    $content = json_decode($content, true);
    if($content['subscribe']==0){
        $content = getWeixinUserInfo2($openid, $token);
    }
    return $content;
}

//如果用户未关注的情况下用授权登录获取用户详细信息
function getWeixinUserInfo2($openid, $token){
    $callback = GetCurUrl();
    $info = get_token_appinfo();
    $param ['appid'] = $info['appid'];
    if (empty($_GET ['code'])) {
        $param ['redirect_uri'] = $callback;
        $param ['response_type'] = 'code';
        $param ['scope'] = 'snsapi_userinfo';
        $param ['state'] = 123;
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($param) . '#wechat_redirect';
        redirect($url);
    }
    $param ['secret'] = $info['appsecret'];
    $param ['code'] = I('code');
    $param ['grant_type'] = 'authorization_code';
    $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query($param);
    $content = file_get_contents($url);
    $content = json_decode($content, true);
    $userinfo = file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$content['access_token'].'&openid='.$openid.'&lang=zh_CN');
    $userinfo = json_decode($userinfo, true);
    trace($userinfo);
    return $userinfo;
}



function getCurl($url)
{
    //初始化curl
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOP_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    //设置header
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //运行curl
    $data = curl_exec($ch);
    curl_close($ch);
    //返回结果
    return $data;

}