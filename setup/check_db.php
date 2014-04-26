<?php
header("Content-type: text/html; charset=utf-8");
error_reporting(0);
if(!file_exists('./db_info.lock')){
	echo '数据库未设置，请输入相关参数进行设置...';
	exit;
}
$db_info=file('./db_info.lock');
$db=new mysqli(trim($db_info[0]), trim($db_info[1]), trim($db_info[2]), trim($db_info[3]));
if(mysqli_connect_errno()){
	echo '数据库连接错误...';
}else{
	echo '数据库连接正常...';
}
$db->close();
?>