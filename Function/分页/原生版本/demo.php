<?php
	header("Content-Type:text/html;charset=utf-8");
	include "page.class.php";
	
	$link=mysql_connect("localhost", "root", "123456");

	mysql_select_db("xsphpdb");


	$result=mysql_query("select * from shops");


	$total=mysql_num_rows($result);

	$num=5;
	
	$page=new Page($total, $num, "&cid=99");

	$sql="select * from shops {$page->limit}";

	$result=mysql_query($sql);

	echo '<table align="center" border="1" width="960">';
	echo '<caption><h1>SHOPS</h1></caption>';

	while($row=mysql_fetch_assoc($result)){
		echo '<tr>';
		echo '<td>'.$row["id"].'</td>';
		echo '<td>'.$row["name"].'</td>';
		echo '<td>'.$row["price"].'</td>';
		echo '<td>'.$row["num"].'</td>';
		echo '<td>'.$row["desn"].'</td>';
		echo '</tr>';	
	}

	echo '<tr><td colspan="5" align="right">'.$page->fpage(array(0,1,2,3,4,5,6,7,8)).'</td></tr>';
	echo '</table>';
