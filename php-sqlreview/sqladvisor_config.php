<?php
	
  $remote_user="hechunyang";   //Linux��������ssh�û���
  $remote_password="123456";   //Linux��������ssh����
  $connection = ssh2_connect('192.168.148.10',60000);  //Linux��������IP��ַ��ssh�˿ں�
  $script='/usr/bin/sqladvisor -h '.$ip.' -u '.$user.' -p '.$pwd.' -P '.$port.' -d '.$db.' -q "'.$multi_sql[$x].'"'; //��Щ���ø�
  ssh2_auth_password($connection,$remote_user,$remote_password);
  $stream = ssh2_exec($connection,$script);
  $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
  stream_set_blocking($errorStream, true);
  $message=stream_get_contents($errorStream);

?>
