<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>���ݿ����߹�����ѯ</title>
<link rel="stylesheet" type="text/css" href="css/table.css">
</head>

<?php
session_start();
$prvi = $_SESSION['prvi'];
$login_user=$_SESSION['username'];

    if($prvi!=1){  
        echo "�Ƿ����ʣ���û��Ȩ������������<br>";
        //echo '<meta http-equiv="Refresh" content="2;url=my_order.php"/>';
	exit;
    }  

    $mysql_server_name='localhost'; 
    $mysql_username='root'; 
    $mysql_password='111111'; 
    $mysql_database='sql_db';

$conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting");
mysql_query("set names 'utf8'"); 
mysql_select_db($mysql_database);

$perNumber=50; //ÿҳ��ʾ�ļ�¼��  
$page=$_GET['page']; //��õ�ǰ��ҳ��ֵ  
$count=mysql_query("select count(*) from sql_order_wait"); //��ü�¼����  
$rs=mysql_fetch_array($count);   
$totalNumber=$rs[0];  
$totalPage=ceil($totalNumber/$perNumber); //�������ҳ��  
/*if (!isset($page)) {  
 $page=1;  
} //���û��ֵ,��ֵ1  */

if (empty($page)) {  
 $page=1;  
} //���û��ֵ,��ֵ1

$startCount=($page-1)*$perNumber; //��ҳ��ʼ,���ݴ˷����������ʼ�ļ�¼ 

$sql1 = "select user from login_user where user = '${login_user}' and privilege = 1";
$result1 = mysql_query($sql1,$conn);
if (mysql_num_rows($result1) > 0) {
	echo "Hi������Ա���ȴ�������������<br>";
	$sql ="select a.* from sql_order_wait a";
}
else{
	$sql ="select a.* from sql_order_wait a join login_user b on a.ops_name = b.user where a.ops_name = '${login_user}'";
}
$result = mysql_query($sql,$conn);

echo "<h1 align='center' class='STYLE2'><a href='wait_order.php'>���ݿ����߹�����ѯ</a></h1>";
echo "<hr />";

echo "<table class='bordered' width='1000px' height='100px' border='1' align='center'>";
echo "<tr>	
	    <th>������</th>
            <th>������</th>
            <th>���ݿ���</th>
            <th>����ʱ��</th>
            <th>����SQL</th>
	    <th>�������</th>
	    <th>����</th>
          </tr>";
while($row = mysql_fetch_array($result)) 
{
$status = $row['status']?"<span style=''>������</span>":"<a href='update.php?id={$row['id']}&login_user=$login_user'>������</a>";
$exec_status = $row['status'];
$exec_finish_status = $row['finish_status'];
echo "<tr>";
echo "<td width='70'>{$row['id']}</td>";
echo "<td width='80'>{$row['ops_name']}</td>";
echo "<td width='120'>{$row['ops_db']}</td>";
echo "<td width='150' style='word-wrap:all'>{$row['ops_time']}</td>";
echo "<td style='word-wrap:break-word'><pre>{$row['ops_content']}</pre></td>";
if($prvi==1){
	if($exec_status==0){
		echo "<td width='60'>$status</br></td>";
	}
	if($exec_status==1){
		echo "<td width='60'>$status</br>
		�����ˣ�</br>{$row['approver']}</td>";
	}
	if($exec_status==2){
		echo "<td width='80'>������ͨ��</br>
		�����ˣ�</br>{$row['approver']} </td>";
	}
}
else{
	echo "<td width='60'>�ȴ�������</td>";
}
#######################################################
if($exec_finish_status==1){
	echo "<td width='80'><a href='execute.php?id={$row['id']}'>ִ�й���</a></td>";
}
else if($exec_finish_status==2){
	echo "<td width='80'>��ִ����</td>";
}
else{
	echo "<td width='80'>û��������ִ��</td>";
	//echo "<td width='80'><a href='cancel.php?id={$row['id']}'>���г�������</a></td>";
} 
echo "</tr>";
}
echo "</table>";

if ($page != 1) { //ҳ��������1  
?>  
<a href='wait_order.php?page=<?php echo ($page - 1);?>'>��һҳ</a><!--��ʾ��һҳ-->  
<?php  
}  
for ($i=1;$i<=$totalPage;$i++) {  //ѭ����ʾ��ҳ��  
?> 
<a href="wait_order.php?page=<?php echo ($i);?>"><?php echo $i ;?></a>
<?php  
}  
if ($page<$totalPage) { //���pageС����ҳ��,��ʾ��һҳ����  
?>  
<a href="wait_order.php?page=<?php echo ($page + 1);?>">��һҳ</a>  
<?php  
}   
?>


</html>
