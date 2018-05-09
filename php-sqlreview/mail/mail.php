<?php

class mail{
	private $dev_user;
	private $sql_order;

	function __construct($dev_user,$sql_order){
		$this->dev_user = $dev_user;
		$this->sql_order = $sql_order;
	}
	
	function execCommand(){
		system("./mail/sendEmail -f youradmin@126.com -t youradmin@126.com -cc {$this->dev_user}@126.com -s smtp.126.com:25 -u '数据运维工单处理提醒' -o message-charset=utf8 -m '工单名称：{$this->sql_order}' -xu youradmin@126.com -xp 'password' -o tls=no");
	}
}

?>
