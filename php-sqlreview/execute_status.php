<?php
$id = $_GET['update_id'];
$q = isset($_GET['q'])? htmlspecialchars($_GET['q']) : '';
if($q) {
        if($q =='是') {
        $con=mysql_connect("localhost","root","111111");
	mysql_select_db("sql_db", $con);
        $result = mysql_query("SELECT a.ip,a.dbname,a.user,a.pwd,a.port,b.ops_content FROM dbinfo a join sql_order_wait b on a.dbname = b.ops_db where b.id='".$id ."'",$con);
	while($row = mysql_fetch_array($result))
	{
  		$ip=$row[0];
  		$db=$row[1];
  		$user=$row[2];
  		$pwd=$row[3];
  		$port=$row[4];
		$ops_content=$row[5];
		/*echo $ip."<br>";
		echo $db."<br>";
		echo $user."<br>";
		echo $pwd."<br>";
		echo $port."<br>";
		echo $ops_content."<br>";*/
	}
	mysql_close($con);
	//上线（客户端请使用mysql5.5及以下版本，或者MariaDB10版本。）
	$dbsql_exec="/usr/bin/mysql --default-character-set=utf8 --skip-column-names --safe-updates -h$ip -u$user -p"."'".$pwd."'"." -P$port $db --execute=\"SHOW MASTER STATUS;".$ops_content.";SHOW MASTER STATUS;"."\"" ;

        ##########上线执行###################
	$remote_user="hechunyang";
	$remote_password="123456";
	$script=$dbsql_exec;
	$connection = ssh2_connect('127.0.0.1',60000);
	ssh2_auth_password($connection,$remote_user,$remote_password);
	$stream = ssh2_exec($connection,$script,NULL,$env=array(),100000,100000);
	$correctStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
	$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
	stream_set_blocking($errorStream, true);
	$message=stream_get_contents($errorStream);
	$measage_stdio=stream_get_contents($correctStream);

	if ($message == ''){
		$con2=mysqli_connect("localhost","root","111111","sql_db");
		$sql = "update sql_order_wait set finish_status=2 WHERE id={$id}";
		if(mysqli_query($con2,$sql)){
			echo "<br>";
			echo "对，就这样，上线成功了！<br>";
			echo "<img src='image/666.jpg'  alt='666' />";
		}
		mysqli_close($con2);
		//header("refresh:3;location:wait_order.php");
    		echo "<br>";
		echo "<a href='my_order.php'>点击返回工单界面</a><br>";
	}
	else{
     		 echo $message."</br>";
      		 echo "上线失败！</br>";
      		 echo "<img src='image/fail.gif'  alt='fail' />";
	}
	fclose($stream);
	fclose($errorStream);
	#######################################
	}
else{
	echo "没有执行.</br>";
    }
}

?>
