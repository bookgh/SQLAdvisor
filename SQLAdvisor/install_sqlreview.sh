#!/bin/bash
yum install -y http://www.percona.com/downloads/percona-release/redhat/0.1-4/percona-release-0.1-4.noarch.rpm
yum install -y gcc-c++ make gcc httpd php php-mysql php-devel php-pear libssh2 libssh2-devel unzip cmake libaio-devel libffi-devel glib2 glib2-devel bison libaio-devel ncurses-devel Percona-Server-shared-56  Percona-Server-client-56 Percona-Server-devel-56 Percona-Server-server-56 openssh-server openssh-clients

echo '' | pecl install -f ssh2
echo "extension=ssh2.so" >> /etc/php.ini
ln -s /usr/lib64/libperconaserverclient_r.so.18 /usr/lib64/libperconaserverclient_r.so
chown -R mysql:mysql /var/lib/mysql
/bin/cp /php-sqlreview/my.cnf /etc/my.cnf
/bin/cp -R /php-sqlreview/* /var/www/html/

cd /SQLAdvisor
if [ -f  CMakeCache.txt ];then
    rm -rf CMakeCache.txt
fi
cmake -DBUILD_CONFIG=mysql_release -DCMAKE_BUILD_TYPE=debug -DCMAKE_INSTALL_PREFIX=/usr/local/sqlparser ./
make && make install

cd /SQLAdvisor/sqladvisor
cmake -DCMAKE_BUILD_TYPE=debug ./
make
cp sqladvisor /usr/bin/sqladvisor
sqladvisor --help
