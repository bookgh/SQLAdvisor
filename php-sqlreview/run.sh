#!/bin/bash
set -e
chown -R mysql:mysql /var/lib/mysql

cat >/etc/ssh/sshd_config<<EOF
Port 22
AddressFamily inet
ListenAddress 0.0.0.0
Protocol 2
SyslogFacility AUTHPRIV
PermitRootLogin yes
MaxAuthTries 6
RSAAuthentication yes
PubkeyAuthentication yes
AuthorizedKeysFile	.ssh/authorized_keys
PasswordAuthentication yes
PermitEmptyPasswords no
UsePAM yes
UseDNS no
X11Forwarding yes
Subsystem       sftp    /usr/libexec/openssh/sftp-server
EOF

echo "ssh111111" | passwd --stdin root
service sshd restart
service mysql  start
service httpd  start
echo "" | mysqladmin -uroot -p password 111111
mysql -uroot -p"111111" -e "CREATE DATABASE IF NOT EXISTS sql_db CHARACTER SET utf8;"
mysql -uroot -D sql_db -p"111111" < "/php-sqlreview/dbinfo.sql.txt"
ping 127.0.0.1 >> /dev/null
