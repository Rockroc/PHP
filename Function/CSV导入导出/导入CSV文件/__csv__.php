<?php
function input_csv($handle) { 
    //简体(脚本改变地区信息)
    setlocale(LC_ALL, 'zh_CN');
    $out = array (); 
    $n = 0; 
    while ($data = fgetcsv($handle, 10000)) { 
        $num = count($data); 
        for ($i = 0; $i < $num; $i++) { 
            $out[$n][$i] = $data[$i]; 
        } 
        $n++; 
    } 
    return $out; 
} 
//导入CSV 
$filename = './aaa.csv'; 
if (empty ($filename)) { 
	echo '请选择要导入的CSV文件！'; 
	exit; 
} 
$handle = fopen($filename, 'r'); 
$result = input_csv($handle); //解析csv 




$len_result = count($result); 
if($len_result==0){ 
	echo '没有任何数据！'; 
	exit; 
} 
fclose($handle); //关闭指针 
//echo "<pre>";

print_r($result);
