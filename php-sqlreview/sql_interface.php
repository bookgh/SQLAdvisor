<?php
    session_start();

    //����Ƿ��¼����û��¼��ת���¼����  
    if(!isset($_SESSION['userid'])){
        header("Location:index.html");
        exit("�㻹û��¼�ء�");
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>SQL�Զ����-��������ƽ̨</title>
<style type="text/css">
<!--
.STYLE2 {font-size: 50px}
.STYLE3 {font-size: 24px}
-->
</style>
<link rel="stylesheet" type="text/css" href="css/page.css">
</head>

<!-- ÿ�δ���ҳ ���ҳ�滺��-->
<HEAD>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="0">
</HEAD>

<body>
<div class="jumbotron" style="background-color:#336699">
  <div class="container">
<span style="float:left;"><img src="image/logo.jpg" height="80" width="113" \></span><font size="11">SQL�Զ����-��������ƽ̨</font>
  </div>
</div>
<div class="jumbotron" style="background-color:#FFF; color:#333; padding:10px;">
  <div class="container">
  <p>����SQL���</p>
  </div>
</div>
<form action="sql_review.php" method="post" name="sql_statement" id="form1">
  <label></label>
  <div align="center">
    <label>
    <tr>
        <td>ѡ��������ݿ⣺</td>
        <td><select name="dbname">
	<?php
	$con=mysql_connect("localhost","root","111111"); 
	mysql_select_db("sql_db", $con);
	$result = mysql_query("SELECT dbname FROM dbinfo");
	while($row = mysql_fetch_array($result)){
	echo "<option value=\"".$row[0]."\">".$row[0]."</option>"."<br>";
        }?>
        </select><td>
    </tr>
<textarea name="sql_statement" type="text" rows="100" cols="100" value="������SQL���...;" size="1000000" style="width:745px;height:200px;color:blue;font-size:24px;border: 5px dashed #FF9933" onfocus="if (value =='������SQL���...'){value =''}" onblur="if (value ==''){value='������SQL���...'}" />
������SQL���...</textarea>
    <br />
    <br />
<input name="sql_order" type="text" style="width:300px;" maxlength="2000" value="�����빤������.."  
    onfocus="if (value =='�����빤������..'){value =''}"  
    onblur="if (value ==''){value='�����빤������..'}" />  
    <br />
    <br />
    </label>

    <label>
    <input name="Submit" type="submit" class="STYLE3" value="�ύ���" />
    </label>
  </div>
</form>
<table width="724" border="0" align="center">
  <tr>
    <td width="648"><div align="left">
      <p>ʹ��˵����<br />
        1�����select/insert/update/create/alter���˹���delete��Ҫ������ <br />
        2�����֮��Ҫ�пո���where id = 100��û�пո��Ӱ���жϵ�׼ȷ�ԡ� <br />
        3��SQL������Ҫ�ӷֺ�; MySQL�������涨�ֺŲſ���ִ��SQL��<br />
        <big><font color="#FF0000">4��������`���ܻ��������ʧ�ܣ���Ҫ���ı��༭���滻����</font></big><br />
        <big><font color="#FF0000">5��֧�ֶ���SQL��������һ���ֺ�;�ָ���磺<br/>
                                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;insert into t1 values(1,'a');<br>
                                   <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;###### <br>-->
                                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;insert into t1 values(2,'b'); </font></big><br />
	<big><font color="#FF0000">6��JSON��ʽ���˫����Ҫ�÷�б�ܽ���ת�壬���磺{\"dis_text\":\"nba\"}��</font></big></p>
      </div></td>
  </tr>
</table>
<p align="center">&nbsp;</p>
</body>
</html>

