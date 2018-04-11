<?php

//获取开发选择的数据库的配置信息，表结构为dbinfo.sql
$con=mysql_connect("192.168.148.9","admin","123456");
mysql_select_db("sql_db", $con);
$result = mysql_query("SELECT ip,dbname,user,pwd,port FROM dbinfo where dbname='".$dbname ."'");
while($row = mysql_fetch_array($result))
{
  $ip=$row[0];
  $db=$row[1];
  $user=$row[2];
  $pwd=$row[3];
  $port=$row[4];
}
mysql_close($con);

?>
