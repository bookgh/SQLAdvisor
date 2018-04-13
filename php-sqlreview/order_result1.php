<?php

$mysql_server_name='localhost';
$mysql_username='root'; 
$mysql_password='111111';
$mysql_database='sql_db';

$conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting");
mysql_query("set names 'utf8'"); 
mysql_select_db($mysql_database); 

$perNumber=30; //每页显示的记录数  
$page=$_GET['page']; //获得当前的页面值  
$count=mysql_query("select count(*) from operation where ops_name = '".$_GET['ops_name']."'"); //获得记录总数  
$rs=mysql_fetch_array($count);   
$totalNumber=$rs[0];  
$totalPage=ceil($totalNumber/$perNumber); //计算出总页数  
/*if (!isset($page)) {  
 $page=1;  
} //如果没有值,则赋值1  */

if (empty($page)) {  
 $page=1;  
} //如果没有值,则赋值1

$startCount=($page-1)*$perNumber; //分页开始,根据此方法计算出开始的记录 

$sql ="select * from operation where ops_name = '".$_GET['ops_name']."' order by id desc limit $startCount,$perNumber";
$result = mysql_query($sql,$conn);

echo "<h1 align='center' class='STYLE2'><a href='./order.php'>数据库上线工单查询</a></h1>";
echo "<hr />";
echo "<style type='text/css'>table,th,td{border:1px solid blue;}</style>";
echo "<table width='100' height='20' border='1' align='center'>";
#echo "<style type='text/css'>table,th,td{border:1px solid blue;},td{width:100px;}</style>";
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
echo "<td>{$row['id']}</td>";
echo "<td>{$row['ops_name']}</td>";
echo "<td>{$row['ops_db']}</td>";
echo "<td>{$row['ops_time']}</td>";
echo "<td>{$row['ops_content']}</td>";
echo "</tr>";
}
echo "</table>";

if ($page != 1) { //页数不等于1  
?>  
<a href='order_result1.php?page=<?php echo ($page - 1).'&ops_name='.$_GET['ops_name'];?>'>上一页</a> <!--显示上一页-->  
<?php  
}  
for ($i=1;$i<=$totalPage;$i++) {  //循环显示出页面  
?>  
<a href="order_result1.php?page=<?php echo ($i).'&ops_name='.$_GET['ops_name'];?>"><?php echo $i ;?></a>  
<?php  
}  
if ($page<$totalPage) { //如果page小于总页数,显示下一页链接  
?>  
<a href="order_result1.php?page=<?php echo ($page + 1).'&ops_name='.$_GET['ops_name'];?>">下一页</a>  
<?php  
}   
?>
