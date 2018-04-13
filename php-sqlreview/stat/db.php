<?php
    $mysql_server_name='192.168.148.9'; 
    $mysql_username='admin'; 
    $mysql_password='123456'; 
    $mysql_database='sql_db';
  $conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting") ;
  mysql_query("set names 'utf8'");
  mysql_select_db($mysql_database);
  $result = mysql_query("select ops_db,count(*) as count from operation group by ops_db");
  $data="";
  $array= array();
  class User{
    public $ops_db;
    public $count;
  }
  while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
    $user=new User();
    $user->ops_db = $row['ops_db'];
    $user->count = $row['count'];
    $array[]=$user;
  }
  $data=json_encode($array);
  echo $data;
?>
