#!/bin/bash
set -e
chown -R mysql:mysql /var/lib/mysql
service mysql  start
service httpd  start
echo "" | mysqladmin -uroot -p password 111111
ping 127.0.0.1 >> /dev/null
