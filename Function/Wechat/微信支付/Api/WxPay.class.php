<?php
/*	必需的请求参数:
		
		$key;                      商户支付密钥
		
		$openid;
		$appid;                    公众账号ID
		$mch_id;                   商户号
		$notify_url;               通知地址
		
		$body;                     商品描述(传递)
		$out_trade_no;             商户订单号(传递)
		$total_fee;                总金额(传递)

		
		$trade_type;               交易类型（系统自动生成）
		$spbill_create_ip;         终端IP（系统自动生成）
		$nonce_str;                随机字符串（系统自动生成）
		$sign;                     签名（系统自动生成）
		
*/

class Wxpay
{
	const CURL_TIMEOUT = 30;
	const UNIFIEDORDER_URL = "https://api.mch.weixin.qq.com/pay/unifiedorder";
	
	private $parameters;
	private $key;
	
	
	public function __construct($k)
	{
		$this->key = $k;
		$this->setParameter("trade_type","JSAPI");
		$this->parameters["spbill_create_ip"] = $_SERVER['REMOTE_ADDR'];
		$this->parameters["nonce_str"] = $this->createNoncestr();
		
	}
	
	public function formatBizQueryParaMap($paraMap, $urlencode)
	{
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v)
		{
		    if($urlencode)
		    {
			   $v = urlencode($v);
			}
			//$buff .= strtolower($k) . "=" . $v . "&";
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar;
		if (strlen($buff) > 0) 
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		}
		return $reqPar;
	}
	
	public function getSign($Obj)
	{
		foreach ($Obj as $k => $v)
		{
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//echo '【string1】'.$String.'</br>';
		//签名步骤二：在string后加入KEY
		$String = $String."&key=".$this->key;
		//echo "【string2】".$String."</br>";
		//签名步骤三：MD5加密
		$String = md5($String);
		//echo "【string3】 ".$String."</br>";
		//签名步骤四：所有字符转为大写
		$result_ = strtoupper($String);
		//echo "【result】 ".$result_."</br>";
		return $result_;
	}
	
	public function createNoncestr( $length = 32 ) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		}  
		return $str;
	}

	public function getPrepayId()
	{
		//dump($this->parameters);
		$this->parameters["sign"] = $this->getSign($this->parameters);
		$xml = $this->arrayToXml($this->parameters);
		$response = $this->postXmlCurl($xml,self::UNIFIEDORDER_URL,self::CURL_TIMEOUT);
		$result = $this->xmlToArray($response);
		$prepay_id = $result["prepay_id"];
		return $prepay_id;
	}
	
	public function getParameters()
	{
		$params["appId"] = $this->parameters["appid"];
		$timeStamp = time();
	    $params["timeStamp"] = "$timeStamp";
	    $params["nonceStr"] = $this->createNoncestr();
		$params["package"] = "prepay_id=".$this->getPrepayId();
	    $params["signType"] = "MD5";
	    $params["paySign"] = $this->getSign($params);
	    return json_encode($params);	
	}
	
	public function postXmlCurl($xml,$url,$second=30)
	{		
        //初始化curl        
       	$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOP_TIMEOUT, $second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
        $data = curl_exec($ch);
		curl_close($ch);
		//返回结果
		
		//dump($data);exit;
		
		if($data)
		{
			curl_close($ch);
			return $data;
		}
		else 
		{ 
			$error = curl_errno($ch);
			echo "curl出错，错误码:$error"."<br>"; 
			echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
			curl_close($ch);
			return false;
		}
	}
	
	public function setParameter($parameter, $parameterValue) 
	{
		$this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
	}
	
	public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
        	 if (is_numeric($val))
        	 {
        	 	$xml.="<".$key.">".$val."</".$key.">"; 

        	 }
        	 else
        	 	$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
        }
        $xml.="</xml>";
        return $xml; 
    }
	
	public function xmlToArray($xml)
	{		
        //将XML转为array        
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $array_data;
	}
	
	public function trimString($value)
	{
		$ret = null;
		if (null != $value) 
		{
			$ret = $value;
			if (strlen($ret) == 0) 
			{
				$ret = null;
			}
		}
		return $ret;
	}

}
?>
