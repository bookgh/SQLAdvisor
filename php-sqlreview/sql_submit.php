<?php

ini_set('memory_limit','3072M');    // 临时设置最大内存占用为3G
set_time_limit(0);                  // 设置脚本最大执行时间 为0 永不过期

$dbuser=$_POST["dbuser"];
$dbpwd=$_POST["dbpwd"];
$sql=$_POST["sql"];
$sql_replace=preg_replace('/(#*|-*|`)/', '', $sql);
$dbip=$_POST["dbip"];
$dbname=$_POST["dbname"];
$dbport=$_POST["dbport"];

/*
echo "用户名：".$dbuser."<br>";
echo "密码：".$dbpwd."<br>";
echo "审核的SQL：".$sql_replace."<br>";
echo "数据库IP：".$dbip."<br>";
echo "数据库名：".$dbname."<br>";
echo "端口：".$dbport."<br>";
*/

$dbsql_exec="/usr/bin/mysql --net_buffer_length=1048576 --max-allowed-packet=33554432 --default-character-set=utf8 --skip-column-names --safe-updates -h$dbip -u$dbuser -p"."'".$dbpwd."'"." -P$dbport $dbname --execute=\"SHOW MASTER STATUS;".$sql_replace.";SHOW MASTER STATUS;"."\"" ;
echo "</br>";

echo "用户名：".$dbuser."<br>";
echo "数据库名：".$dbname."<br>";
echo "上线的SQL：".$sql_replace."<br>";


##########上线执行###################
$remote_user="root";
$remote_password="ssh111111";
$script=$dbsql_exec;
$connection = ssh2_connect('127.0.0.1',22);
ssh2_auth_password($connection,$remote_user,$remote_password);
$stream = ssh2_exec($connection,$script,NULL,$env=array(),10000,10000);
$correctStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
stream_set_blocking($errorStream, true);
$message=stream_get_contents($errorStream);
$measage_stdio=stream_get_contents($correctStream);
if ($message == ''){

######记录上线操作######
$conn=mysqli_connect("localhost","root","111111","sql_db");
$ops_time="NOW()";
$str_replace_sql=str_replace("'","\'",$sql_replace);
$ops_sql = "INSERT INTO operation (ops_name, ops_db, ops_time, ops_content, binlog_information) VALUES ('$dbuser','$dbname',$ops_time,'$str_replace_sql','$measage_stdio')";
mysqli_query("SET NAMES utf8");
mysqli_query($conn,$ops_sql);
mysqli_close($conn);
############################################################

echo "<br>";
echo "对，就这样，上线成功了！<br>";
echo "<img src='image/666.jpg'  alt='666' />";
//echo $measage_stdio;
}
else{
      echo $message."</br>";
      echo "上线失败！</br>";
      echo "<img src='image/fail.gif'  alt='fail' />";
}
fclose($stream);
fclose($errorStream);
#######################################

?>

