<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>数据库上线工单查询</title>
<link rel="stylesheet" type="text/css" href="css/table.css">
</head>

<?php
$mysql_server_name='localhost';
$mysql_username='root'; 
$mysql_password='111111';
$mysql_database='sql_db';

$conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting");
mysql_query("set names 'latin1'"); 
mysql_select_db($mysql_database); 
$sql ="select * from operation order by id desc limit 50";
$result = mysql_query($sql,$conn);

echo "<h1 align='center' class='STYLE2'><a href='./order.php'>数据库上线工单查询</a></h1>";
echo "<hr />";
echo "<form action='order_result1.php' method='get'>
  <p align='center'>输入用户名: 
    <input type='text' name='ops_name' value='hechunyang'>
    <input name='submit'type='submit' value='查询' />
  </p>
</form>";
echo "<form action='order_result2.php' method='get'>  
  <p align='center'>输入时间范围: 
    <input name='access_time_before' type='text' value='2017-12-01 00:00:00'>
    AND
    <input name='access_time_end' type='text' value='2017-12-31 23:59:59' />
    <input type='submit' value='查询'>
  </p>
</form>";

echo "<table class='bordered' width='1000px' height='100px' border='1' align='center'>";
echo "<tr>	
	    <th>上线数量</th>
            <th>操作人</th>
            <th>数据库名</th>
            <th>操作时间</th>
            <th>上线SQL工单</th>
          </tr>";
while($row = mysql_fetch_array($result)) 
{ 
echo "<tr>";
echo "<td width='70'>{$row['id']}</td>";
echo "<td width='80'>{$row['ops_name']}</td>";
echo "<td width='120'>{$row['ops_db']}</td>";
echo "<td width='150' style='word-wrap:all'>{$row['ops_time']}</td>";
echo "<td style='word-wrap:break-word'>{$row['ops_content']}</td>";
echo "</tr>";
}
echo "</table>";
?>

</html>
