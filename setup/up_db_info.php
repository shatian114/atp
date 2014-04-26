<?php
$db_url=$_POST['db_url'];
$db_user=$_POST['db_user'];
$db_passwd=$_POST['db_passwd'];
$db_name=$_POST['db_name'];
$file=fopen('./db_info.lock', 'w');
fputs($file, $db_url."\n");
fputs($file, $db_user."\n");
fputs($file, $db_passwd."\n");
fputs($file, $db_name);
fclose($file);
echo $db_name;
?>