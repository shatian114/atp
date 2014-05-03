<?php
header("Content-type: text/html; charset=utf-8");
error_reporting(0);
//打开数据库
$db_info=file('./db_info.lock');
@ $db=new mysqli(trim($db_info[0]), trim($db_info[1]), trim($db_info[2]), trim($db_info[3]));
if(mysqli_connect_errno()){
	echo "database error";
	exit;
}
$db->query('create table if not exists atp_tb_info ( mi_name varchar(50), tb_name varchar(3))');
$result=$db->query('select * from atp_tb_info');
$num_rows=$result->num_rows;
//删除旧数据
for($i=0;$i<$num_rows;++$i){
	$row=$result->fetch_assoc();
	$db->query("drop table if exists '".$row['tb_name']."'");
}
$db->query('delete from atp_tb_info');
function delete_all($full_path){
	if(!is_dir($full_path)){
		return false;
	}
	$dir=opendir($full_path);
	while($read_file=readdir($dir)){
		if($read_file!="." && $read_file!=".."){
			$dir_path=$full_path."/".$read_file;
			is_dir($dir_path) ? delete_all($dir_path) : unlink($dir_path);
		}
	}
	closedir($dir);
	rmdir($full_path);
}
delete_all('../pa');
$oldumask=umask(0);
mkdir('../pa', 0777);
umask($oldumask);
//将仓库路径及名称存入数组
$mi_url_arr=file('./package');
$sum_pa=0;
$num_pa=0;
$sum_mi=count($mi_url_arr);
for($i=0;$i<$sum_mi;++$i){
	$pa_url=trim($mi_url_arr[$i]);
	$mi_url[$i]=substr($pa_url, 0, strlen($pa_url)-9);
	$mi_name[$i]=substr($mi_url[$i], strrpos($mi_url[$i], '/')+1);
	$se_mi=$se_mi.'<option>'.$mi_name[$i].'</option>';
}
//开始制作文件及数据库
for($i=0;$i<$sum_mi;++$i){
	$pa_url=trim($mi_url_arr[$i]);
	$package_file=fopen($pa_url, 'r');
	if(!$package_file){
		echo $pa_url.'打开错误';
		continue;
	}
	$db->query('insert into atp_tb_info values(\''.$mi_name[$i]."', 'a$i')");
	$db->query("create table a$i ( name varchar(50) primary key)");
	$oldumask=umask(0);
	mkdir('../pa/'.$mi_name[$i], 0777);
	umask($oldumask);
	while(!feof($package_file)){
		$str=trim(fgets($package_file));
		while(!feof($package_file) && $str!=''){
			while(!feof($package_file) && $str!=''){
				$info_name=substr($str, 0, strpos($str, ':'));
				$info_content=substr($str, strpos($str, ':')+1);
				switch (trim($info_name)) {
					case 'Package':
						$p_package=trim($info_content);
						break;
					case 'Version':
						$p_ver=trim($info_content);
						break;
					case 'Architecture':
						$p_archi=trim($info_content);
						break;
					case 'Maintainer':
						$p_main=htmlentities(trim($info_content));
						break;
					case 'Installed-Size':
						$p_in_size=trim($info_content);
						break;
					case 'Depends':
						$p_depend=trim($info_content);
						break;
					case 'Replaces':
						$p_rep=trim($info_content);
						break;
					case 'Filename':
						$p_dlurl=$mi_url[$i].'/'.trim($info_content);
						break;
					case 'Size':
						$p_size=trim($info_content);
						break;
					case 'MD5sum':
						$p_md5=trim($info_content);
						break;
					case 'SHA1':
						$p_sha1=trim($info_content);
						break;
					case 'SHA256':
						$p_sha256=trim($info_content);
						break;
					case 'Section':
						$p_sec=trim($info_content);
						break;
					case 'Description':
						$p_des=trim($info_content);
						break;
					default:
						# code...
						break;
				}
				$str=trim(fgets($package_file));
			}
			$db->query('insert into a'.$i.' values(\''.$p_package.'\')');
			++$num_pa;
			$str=trim(fgets($package_file));
			$mi_str=$mi_str."<a href='./".$mi_name[$i]."/$p_package.html'><h3>$p_package</h3></a>";
			$pa_file=file_get_contents('./pa_temp');
			$pa_file=str_replace('this is the se_mi', $se_mi, $pa_file);
			$pa_file=str_replace('this is the nav', "<h2>$p_package</h2><table class='table table-striped'><tr><td>版本号</td><td>$p_ver</td><tr><td>架构</td><td>$p_archi</td><tr><td>维护者</td><td>$p_main</td><tr><td>安装后大小</td><td>$p_in_size</td><tr><td>依赖</td><td>$p_depend</td><tr><td>点击下载</td><td><a href='$p_dlurl'>下载</a></p><p>大小:$p_size</td><tr><td>MD5值</td><td>$p_md5</td><tr><td>SHA1值</td><td>$p_sha1</td><tr><td>SHA256值</td><td>$p_sha256</td><tr><td>类别</td><td>$p_sec</td><tr><td>说明</td><td>$p_des</td><tr><td>替代</td><td>$p_rep</td></table>", $pa_file);
			file_put_contents('../pa/'.$mi_name[$i]."/$p_package.html", $pa_file);
		}
	}
	$mi_file=file_get_contents('./mi_temp');
	$mi_file=str_replace('this is the nav', $mi_str, $mi_file);
	$mi_file=str_replace('this is the se_mi', $se_mi, $mi_file);
	file_put_contents('../pa/'.$mi_name[$i].'.html', $mi_file);
	$mi_str='';
<<<<<<< HEAD
	$se_nav=$se_nav.'<li><a href="./pa/'.$mi_name[$i].'.html"><span class="badge pull-right">'.$num_pa.'</span>'.$mi_name[$i].'</a></li>';
=======
	$se_nav=$se_nav.'<a href="./pa/'.$mi_name[$i].'.html"><h3>'.$mi_name[$i].'(共收录'.$num_pa.'个包)</h3></a>';
>>>>>>> 72452eb312aa236e3c6911c2501c05dea39e4829
	$num_pa=0;
	fclose($package_file);
}
$index_file=file_get_contents('./index_temp');
$index_file=str_replace('this is the se_mi', $se_mi, $index_file);
$index_file=str_replace('this is the nav', '<ul class="nav nav-pills nav-stacked">'.$se_nav.'</ul>', $index_file);
file_put_contents('../index.html', $index_file);
echo "网站更新完毕";
?>