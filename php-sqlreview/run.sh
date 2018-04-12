#!/bin/bash
set -e
service mysql  start
service httpd  start
echo "" | mysqladmin -uroot -p password 111111
ping 127.0.0.1 >> /dev/null
