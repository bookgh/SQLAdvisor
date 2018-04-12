#!/bin/bash
set -e
service mysql  start
service httpd  start
ping 127.0.0.1 >> /dev/null
