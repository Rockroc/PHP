<?php 
//callback.php 回调页面用来设置跨域COOKIE 
header('Content-Type:text/html; charset=utf-8'); 
if(empty($_GET)){ 
    exit('您还未登录!'); 
}else{ 
    foreach($_GET as $key=>$val){ 
        setcookie($key,$val,0,''); 
    } 
    header("location:index.php"); 
}
?>