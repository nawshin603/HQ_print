<?php
    
	error_reporting(0);
	//if (!$conn=mysqli_connect("localhost","root","","excelfile")){
    if (!$conn=mysqli_connect("localhost","root","","cms")){
		$connError='Y';
		}
	error_reporting(E_ALL & ~(E_WARNING|E_NOTICE));	
?>
