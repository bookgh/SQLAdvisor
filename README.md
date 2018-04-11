# SQLAdvisor
# php-sqlreview

Dockerfile.sqladvisor  是SQLAdvisor的Dockerfile文件

Dockerfile.sqlreview   是php-sqlreview的Dockerfile文件

Docker镜像文件请访问https://hub.docker.com/r/ppabc/sqladvisor/

# SQL自动审核-自助上线平台

     为了让DBA从日常繁琐的工作中解放出来，通过SQL自助平台，可以让开发自上线，开发提交SQL后就会自动返回优化建议，无需DBA的
 再次审核，从而提升上线效率，有利于建立数据库开发规范。借鉴了去哪网Inception的思路和Percona在线sql审核思路，并且把美团网SQLAdvisor（索引优化建议）集成在一起，并结合了之前写的《DBA的40条军规》纳入了审核规则里，用PHP实现。

SQL自动审核主要完成两方面目的：
1、避免性能太差的SQL进入生产系统，导致整体性能降低。
2、检查开发设计的索引是否合理，是否需要添加索引。

思路其实很简单:
1、获取开发提交的SQL
2、对要执行的SQL做分析，触碰事先定义好的规则来判断这个SQL是否可以自动审核通过，未通过审核的需要人工处理。

使用说明：
1、针对select/insert/update/create/alter加了规则，delete需要审批。
2、语句之间要有空格，例where id = 100，没有空格会影响判断的准确性。
3、SQL语句后面要加分号; MySQL解析器规定分号才可以执行SQL。
4、反引号`会造成上线失败，需要用文本编辑器替换掉。
5、支持多条SQL解析，用一个分号;分割。例如：
     insert into t1 values(1,'a');
     insert into t1 values(2,'b');
6、JSON格式里的双引号要用反斜杠进行转义，例如：{\"dis_text\":\"nba\"}。


注：审核规则是根据我公司的情况制定而成，非Inception审核规则（只借鉴思路），使用时请注意！
    其内部的原理，主要用正则表达式匹配规则实现。

--------------------------------------------------------------------------------------------------
SELECT审核
1、开发人员可以直接将SQL语句提交到平台进行风险评估
2、平台对SQL语句进行分析，自动给出其不符合开发规范的改进意见
3、适用场景：应用开发阶段

检查项：
1、select * 是否有必要查询所有的字段？
2、警告！没有where条件，注意where后面的字段要加上索引
3、没有limit会查询更多的数据
4、警告！子查询性能低下，请转为join表关联
5、提示：in里面的数值不要超过1000个
6、提示：采用join关联，注意关联字段要都加上索引，如on a.id=b.id
7、提示：MySQL对多表join关联性能低下，建议不要超过3个表以上的关联
8、警告！like '%%'双百分号无法用到索引，like 'mysql%'这样是可以利用到索引的
9、提示：默认情况下，MySQL对所有GROUP BY col1，col2...的字段进行排序。如果查询包括GROUP BY，
想要避免排序结果的消耗，则可以指定ORDER BY NULL禁止排序。
10、警告！MySQL里用到order by rand()在数据量比较多的时候是很慢的，因为会导致MySQL全表扫描，故也不会用到索引
11、提示：是否要加一个having过滤下？
12、警告！禁止不必要的order by排序,因为前面已经count统计了
13、警告！MySQL里不支持函数索引，例DATE_FORMAT('create_time','%Y-%m-%d')='2016-01-01'是无法用到索引的，需要改写为
create_time>='2016-01-01 00:00:00' and create_time<='2016-01-01 23:59:59'
14、之后会调用美团网SQLAdvisor进行索引检查


INSERT审核
检查项：
1、警告: insert 表1 select 表2，会造成锁表。


UPDATE审核规则
1、警告！没有where条件，update会全表更新，禁止执行！！！
2、更新的行数小于1000行，可以由开发自助执行。否则请联系DBA执行！！！
3、防止where 1=1 绕过审核规则
4、检查更新字段有无索引


CREATE审核规则
检查项：
1、警告！表没有主键
2、警告！表主键应该是自增的，缺少AUTO_INCREMENT
3、提示：id自增字段默认值为1，auto_increment=1
4、警告！表没有索引
5、警告！表中的索引数已经超过5个，索引是一把双刃剑，它可以提高查询效率但也会降低插入和更新的速度并占用磁盘空间
6、警告！表字段没有中文注释，COMMENT应该有默认值，如COMMENT '姓名'
7、警告！表没有中文注释
8、警告！表缺少utf8字符集，否则会出现乱码
9、警告！表存储引擎应设置为InnoDB
10、警告！表应该为timestamp类型加默认系统当前时间


ALTER审核规则
检查项：
1、警告！不支持create index语法，请更改为alter table add index语法。
2、警告！更改表结构要减少与数据库的交互次数，应改为，例alter table t1 add index IX_uid(uid),add index IX_name(name)
3、表记录小于100万行，可以由开发自助执行。否则表太大请联系DBA执行!
4、支持删除索引，但不支持删除字段

具体演示，请移步 http://blog.51cto.com/hcymysql/2053798
---------------------------------------------------------------------------------------------

一、环境安装
1、php环境安装
# yum install httpd php mysql php-mysql php-devel php-pear libssh2 libssh2-devel unzip -y
yum install -y http://www.percona.com/downloads/percona-release/redhat/0.1-4/percona-release-0.1-4.noarch.rpm
yum install gcc-c++ make gcc -y
2、安装php ssh2扩展
pecl install -f ssh2

3、修改/etc/php.ini
在最后一行添加
extension=ssh2.so

4、关闭selinux
# vim /etc/selinux/config
SELINUX=disabled

5、美团网SQLAdvisor安装
请移步 https://github.com/Meituan-Dianping/SQLAdvisor/blob/master/doc/QUICK_START.md
yum  install cmake libaio-devel libffi-devel glib2 glib2-devel Percona-Server-shared-56  bison libaio-devel  ncurses-devel
yum  install --enablerepo=Percona56 Percona-Server-shared-56

cd /usr/lib64/ 2. ln -s libperconaserverclient_r.so.18 libperconaserverclient_r.so
二、部署
将php-sqlreview.zip解压缩到/var/www/html/目录下

1、导入dbinfo.sql（DB配置信息表）和operation.sql（SQL工单记录表）

2、修改db_config.php（DB配置信息的IP、端口、用户名、密码、库名）

3、修改sqladvisor_config.php（访问SQLAdvisor服务器的IP、SSH端口、SSH用户名、SSH密码）

4、修改sql_submit.php（记录工单表的IP、端口、用户名、密码）和（调用mysql客户端的IP、SSH端口、SSH用户名、SSH密码）

三、脚本解释
1、main.php（SQL传参入口）
2、sql_review.php（过审核规则）
3、sql_submit.php（通过后，SQL上线提交）
4、order.php（工单查询-只记录成功入库的SQL）
5、order_result1.php（按照用户名分页搜索）
6、order_result2.php（按照时间范围分页搜索）

注：
1、修复了一些子查询的bug。
2、首页不用手工写库了，直接从dbinfo表里获取。
3、增加一个导航栏，超链接到工单查询。

