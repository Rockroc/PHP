<?php
header("content-type:text/html;charset=UTF-8");
$publickey = file_get_contents("./public.der");
$privatekey= file_get_contents("./private.key");

//加密内容
$plaintext = "我虽然帅，但是我要低调点，不能说出来~";

//用公有密钥加密
openssl_public_encrypt($plaintext, $encrypted, $publickey);

echo "1.公有加密，私有解密";
echo "<hr />";
echo $encrypted;  //加密后的字符串
echo "<hr />";
//用私有密钥解密
openssl_private_decrypt($encrypted, $plain, $privatekey);

echo $plain;	//解密后内容

echo "<hr />";
echo "2.私有加密，公有解密";
echo "<hr />";
openssl_private_encrypt($plaintext, $encrypted, $privatekey);

echo $encrypted;  //加密后的字符串
echo "<hr />";
//用公有密钥解密
openssl_public_decrypt($encrypted, $plain, $publickey);

echo $plain;	//解密后内容

?>
