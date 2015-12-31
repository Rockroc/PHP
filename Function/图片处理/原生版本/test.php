<?php
/*
 *  图像处理类
 *
 *  类名Image   
 *  文件:image.class.php
 *
 *  功能： 图片的缩放和加图片水印
 *
 *  目的：让不会使用GD库的学员，通过 本类可以对任意类型的图片进行缩放和加水印
 *
 *  
 *
 */
   include "image.class.php";

	$image=new Image("./images/");
	//对图片进行缩放
/*	echo $image->thumb("map.gif", 500, 500, "th5_")."<br>";
	echo $image->thumb("map.gif", 400, 400, "th4_")."<br>";
	echo $image->thumb("map.gif", 300, 300, "th3_")."<br>";
	echo $image->thumb("map.gif", 200, 200, "th2_")."<br>";
	echo $image->thumb("map.gif", 100, 100, "th1_")."<br>";
 */
	//对图片进行加水印
	echo $image->waterMark("map.gif", "gaolf.gif", 0, "wa0_")."<br>";
	echo $image->waterMark("map.gif", "gaolf.gif", 1, "wa1_")."<br>";
	echo $image->waterMark("map.gif", "gaolf.gif", 2, "wa2_")."<br>";
	echo $image->waterMark("map.gif", "gaolf.gif", 3, "wa3_")."<br>";
	echo $image->waterMark("map.gif", "gaolf.gif", 4, "wa4_")."<br>";
	echo $image->waterMark("map.gif", "gaolf.gif", 5, "wa5_")."<br>";
	echo $image->waterMark("map.gif", "gaolf.gif", 6, "wa6_")."<br>";
	echo $image->waterMark("map.gif", "gaolf.gif", 7, "wa7_")."<br>";
	echo $image->waterMark("map.gif", "gaolf.gif", 8, "wa8_")."<br>";
	echo $image->waterMark("map.gif", "gaolf.gif", 9, "wa9_")."<br>";
