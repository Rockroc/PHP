<?php
    //-----------------ajax保存信息-----------------------
    $filename  = './data.json';
    if ($_POST['msg']!='') {
        file_put_contents($filename,$_POST['msg']);
        echo $_POST['msg'];

    }else{
        echo "发送内容不能为空";
    }