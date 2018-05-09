    <?php   
      
     $conn = mysql_connect("192.168.148.9","admin","123456") or die("数据库链接错误".mysql_error());  
     mysql_select_db("sql_db",$conn) or die("数据库访问错误".mysql_error());  
     mysql_query("set names utf8");  
    ?>  
