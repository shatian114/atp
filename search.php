<?php
header("Content-type: text/html; charset=utf-8");
error_reporting(0);
$mi_name=$_POST['mi_name'];
$p_n=$_POST['p_n'];
@ $db=new mysqli('127.0.0.1', 'peng', 'yuanld2min', 'atp');
if(mysqli_connect_errno()){
	echo 'db is error';
	exit;
}
if($mi_name=='全部'){
	echo '搜索结果:<br />'.'<button class="btn btn-primary" onclick=\'close_s()\'>关闭</button>';
	$result=$db->query('select * from atp_tb_info');
	$num_mi=$result->num_rows;
	for($i=0;$i<$num_mi;++$i){
		$row=$result->fetch_assoc();
		echo '<div class="col-md-3 form-inline"><h3>'.$row['mi_name'].':</h3>';
		$mi_result=$db->query('select * from '.$row['tb_name'].' where name LIKE \'%'.$p_n.'%\'');
		$num_pa=$mi_result->num_rows;
		for($j=0;$j<$num_pa;++$j){
			$pa_row=$mi_result->fetch_assoc();
			$link='<p><a href=\'./pa/'.$row['mi_name'].'/'.$pa_row['name'].'.html\'>'.$pa_row['name'].'</a></p>';
			echo $link;
		}
		echo '</div>';
	}
}else{
	echo '搜索结果:<br />'.'<button class="btn btn-primary" onclick=\'close_s()\'>关闭</button>';
	echo $mi_name;
	$result=$db->query('select * from atp_tb_info where mi_name=\''.$mi_name.'\'');
	$row=$result->fetch_assoc();
	$result=$db->query('select * from '.$row['tb_name'].' where name LIKE \'%'.$p_n.'%\'');
	$num_pa=$result->num_rows;
	for($i=0;$i<$num_pa;++$i){
		$pa_row=$result->fetch_assoc();
		echo '<p><a href=\'./pa/'.$mi_name.'/'.$pa_row['name'].'.html\'>'.$pa_row['name'].'</a></p>';
	}
}
?>