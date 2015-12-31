<?php

    $region_id = !empty($_GET['cityId']) ? $_GET['cityId'] : $_GET['proviceId'];

    $addressLng = $_GET['lng'];

    $addressLat = $_GET['lat'];

    $opts = array (
        'http' => array (
            'method' => 'GET',
            'header'=> "Access-Token: sRPAcwNpR3ODRV7r3PvNjQ\r\n".
                "Access-Key: 3588939af15343d491b5b470be0964c5\r\n",
        )
    );

    $num = mt_rand(1,999);
    for($i=3;$i>strlen($num);$i--){
        $num = '0'.$num;
    }
    $cache = time().$num;

    if(isset($_GET['page']) && !empty($_GET['page'])){
        $start = ($_GET['page']-1)*20;
    }else{
        $start = 0;
    }

    if(!empty($_GET['keyword'])){
        $keyword = urlencode($_GET['keyword']);
        $url = "http://www.wuliangye.com.cn/wly_eap/cn/api/v1/publix/dealers?&name={$keyword}&region_id={$region_id}&start={$start}&max_size=20&_={$cache}";
    }else{
        $url = "http://www.wuliangye.com.cn/wly_eap/cn/api/v1/publix/dealers?&region_id={$region_id}&start={$start}&max_size=20&_={$cache}";
    }

    if(!empty($_GET['type'])){
        $url .= "&type={$_GET['type']}";
    }

    $context = stream_context_create($opts);
    $html = file_get_contents($url,false, $context);
    //echo json_encode($html);die();
    $arr = json_decode($html,true);
    $far = array();
    foreach($arr['dealers'] as $key => $value){
        $url = "http://api.map.baidu.com/geocoder/v2/?address={$value['address']}&output=json&ak=YIjdDENupKI3zHmuTioRP3t6";
        $address = file_get_contents($url);
        $addressArr = json_decode($address,true);
        if($addressArr['status']==0){
            $lng = $addressArr['result']['location']['lng'];
            $lat = $addressArr['result']['location']['lat'];
            $distance = getDistance($addressLng,$addressLat,$lng,$lat);
            $far[$key] = $distance;
            $arr['dealers'][$key]['distance'] = meter_calculate($distance);
            $arr['dealers'][$key]['lng'] = $lng;
            $arr['dealers'][$key]['lat'] = $lat;

            //手机号码处理
            $phone = trim($arr['dealers'][$key]['contacts'][0]['phone_number'],'？');
            $phoneArr = explode('  ',$phone);
            $phoneHtml = "";
            foreach($phoneArr as $val){
                $phoneHtml .= "<a href='tel:{$val}'>{$val}</a>&nbsp;&nbsp;";
            }
            $arr['dealers'][$key]['phone'] = $phoneHtml;

        }else{
            unset($arr['dealers'][$key]);
        }
    }
    array_multisort($far,SORT_ASC,$arr['dealers']);

    echo json_encode(json_encode($arr));
    unset($arr);
    unset($far);
    die();

/**
 * @desc 根据两点间的经纬度计算距离
 * @param float $lat 纬度值
 * @param float $lng 经度值
 */
function getDistance($lat1, $lng1, $lat2, $lng2)
{
    $earthRadius = 6367000; //approximate radius of earth in meters

    /*

    http://shanmao.me

    */

    $lat1 = ($lat1 * pi() ) / 180;
    $lng1 = ($lng1 * pi() ) / 180;

    $lat2 = ($lat2 * pi() ) / 180;
    $lng2 = ($lng2 * pi() ) / 180;


    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;

    return round($calculatedDistance);
}

//距离换算
function meter_calculate($m){
    if($m<=1000){
        return $m.'m';
    }else{
        $km = $m/1000;
        return $km.'km';
    }
}

