<?php
    date_default_timezone_set('PRC');
    error_reporting(0);
	//define("BASEDIR",__DIR__);
	//引入配置文件
	require_once './Api/config.php';
	//引入类文件
	require_once './Api/WxPay.class.php';
	//引入函数文件
	require_once './Api/Function.php';
	
	//print_r($config);die();
	//获取openid
	$openid = get_openid();

	//订单描述
	$body = '无描述';
	
	//生成订单号
	$out_trade_no = date("YmdHis") . rand(100, 999);
	
	//---------------价格(1代表1分钱，1块钱就*100)---------------
	$total_fee = 1;
	
	//回调地址
	$notify_url = 'http://www.baidu.com';
	//dump($notify_url);exit;
	
	//使用统一支付接口,获取prepay_id
	$wxp = new WxPay($config['KEY']);

	$wxp->setParameter("openid", $openid);
	$wxp->setParameter("appid", $config['APPID']);
	$wxp->setParameter("mch_id", $config['MCHID']);
	$wxp->setParameter("notify_url", $notify_url);

	$wxp->setParameter("body", $body);
	$wxp->setParameter("out_trade_no", $out_trade_no);
	$wxp->setParameter("total_fee", $total_fee);
	$wxp->setParameter("trade_type", 'JSAPI');
	
	$jsApiParameters = $wxp->getParameters();
	
	//var_dump($jsApiParameters);die();
	$html = array();

	$html[] = '<script language="javascript">';
	$html[] = "	function jsApiCall(){";
	//$html[] = "		alert(JSON.stringify(".$jsApiParameters."));";
	$html[] = "		WeixinJSBridge.invoke(";
	$html[] = "			'getBrandWCPayRequest',";
	$html[] = $jsApiParameters . ",";
	$html[] = "			function(res){";
	//$html[] = "				alert(res.err_msg);";
	$html[] = "				WeixinJSBridge.log(res.err_msg);";
	//$html[] = "				alert(res.err_code+res.err_desc+res.err_msg);";
	$html[] = "				if(res.err_msg == 'get_brand_wcpay_request:ok'){";
	$html[] = "					var url = '" . $notify_url . "';";
	$html[] = "					window.location.href=url;";
	$html[] = "				}";
	$html[] = "			}";
	$html[] = "		);";
	$html[] = "	}";
	$html[] = "	function callpay(){";
	$html[] = '		if (typeof WeixinJSBridge == "undefined"){';
	$html[] = "			if( document.addEventListener ){";
	$html[] = "				document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);";
	$html[] = "			}else if (document.attachEvent){";
	$html[] = "				document.attachEvent('WeixinJSBridgeReady', jsApiCall); ";
	$html[] = "				document.attachEvent('onWeixinJSBridgeReady', jsApiCall);";
	$html[] = "			}";
	$html[] = "		}else{";
	$html[] = "			jsApiCall();";
	$html[] = "		}";
	$html[] = "	}";
	$html[] = 'callpay();';
	$html[] = '</script>';
	//$html[] = '<div style="text-align:center"><button type="button" style="background: none repeat scroll 0 0 #00d20d; border: 0 none; color: #fff; display: block; font-size: 0.9rem; height: 2.5rem; text-align: center; width: 100%;" onclick="callpay()">微信安全支付</button></div>';
	echo implode('',$html);