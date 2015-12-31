<?php 
//connect.php 用来检测登录状态的页面，内嵌在页面的iframe中 
header('Content-Type:text/html; charset=utf-8'); 
if(isset($_COOKIE['sign'])){ 
    $callback = urldecode($_GET['callback']);unset($_GET['callback']); 
    $query = http_build_query($_COOKIE); 
    $callback = $callback."?{$query}"; 
}else{ 
    exit; 
} 
?>
<html><script type="text/javascript">top.location.href="<?php echo $callback; ?>";</script></html>