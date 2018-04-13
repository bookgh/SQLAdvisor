#!/bin/bash
set -e
chown -R mysql:mysql /var/lib/mysql
service mysql  start
service httpd  start
echo "" | mysqladmin -uroot -p password 111111
mysql -uroot -p"111111" -e "CREATE DATABASE IF NOT EXISTS sql_db CHARACTER SET utf8;"
mysql -uroot -D sql_db -p"111111" < "/php-sqlreview/dbinfo.sql.txt"
ping 127.0.0.1 >> /dev/null
