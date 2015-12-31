<?php
$conn=mysql_connect("localhost","root","123456");//链接数据库
mysql_select_db("city");//选择数据库
mysql_query("set names 'utf8'");//设定字符集

//获取市
if(isset($_POST['province'])){
    $provinceId = $_POST['province'];
    $sql="select `code`,`name` from city where provincecode={$provinceId}";
    $result=mysql_query($sql);
    $data = array();
    while($row=mysql_fetch_row($result)){
        $data[] = $row;
    }
    echo json_encode($data);
}


//获取区
if(isset($_POST['city'])){
    $cityId = $_POST['city'];
    $sql="select `code`,`name` from area where citycode={$cityId}";
    $result=mysql_query($sql);
    while($row=mysql_fetch_row($result)){
        $data[] = $row;
    }
    echo json_encode($data);
}