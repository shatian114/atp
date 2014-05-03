<?php
header("Content-type: text/html; charset=utf-8");
error_reporting(0);
$mi_name=$_POST['mi_name'];
$p_n=$_POST['p_n'];
$db_info=file('./setup/db_info.lock');
@ $db=new mysqli(trim($db_info[0]), trim($db_info[1]), trim($db_info[2]), trim($db_info[3]));
if(mysqli_connect_errno()){
	echo "database error";
	exit;
}
if($mi_name=='全部'){
	$result=$db->query('select * from atp_tb_info');
	$num_mi=$result->num_rows;
	for($i=0;$i<$num_mi;++$i){
		$row=$result->fetch_assoc();
		$mi_result=$db->query('select * from '.$row['tb_name'].' where name LIKE \'%'.$p_n.'%\'');
		$num_pa=$mi_result->num_rows;
		for($j=0;$j<$num_pa;++$j){
			$pa_row=$mi_result->fetch_assoc();
			$pa_arr[$j]=array('pa'=>$pa_row['name']);
		}
		$mi_arr[$i]=$pa_arr;
		$pa_arr=array();
		$mi_result->free();
	}
	$mi_json=array('pa'=>$mi_arr);
	echo json_encode($mi_json);
}else{
	$result=$db->query('select * from atp_tb_info where mi_name=\''.$mi_name.'\'');
	$row=$result->fetch_assoc();
	$result=$db->query('select * from '.$row['tb_name'].' where name LIKE \'%'.$p_n.'%\'');
	$num_pa=$result->num_rows;
	for($i=0;$i<$num_pa;++$i){
		$pa_row=$result->fetch_assoc();
		$pa_arr[$i]=array('pa'=>$pa_row['name']);
	}
	$pa_json=array('pa'=>$pa_arr);
	echo json_encode($pa_json);
}
$result->free();
$db->close();
?>