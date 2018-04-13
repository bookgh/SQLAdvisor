<?php

$s=$c=$is=$up=$at=0;
$sql_count=1;
$parm_post=$_POST['sql_statement'];
$parm=preg_replace('/\`/','',strtolower($parm_post));
$dbname=$_POST["dbname"];
$alter_array=array();
$alter_parm=array();

require 'db_config.php';

$multi_sql=preg_split("/;+/",ltrim($parm));
$arrlength=count($multi_sql);
echo "</br>";
echo "<big><font color=\"#0000FF\">------你刚才选择的数据库名字是：" . $dbname . "------</font></big></br>";
for($x=0;$x<$arrlength;$x++){
echo "</br>";
echo "第" . $sql_count . "条、你刚才输入的SQL语句是：" . $multi_sql[$x] . "</br>";

echo "<hr style=FILTER: progid:DXImageTransform.Microsoft.Glow(color=#987cb9,strength=10) width=100% color=#987cb9 SIZE=1>";
if($multi_sql[$x]){
    	$parmArr_enter = str_replace("\r\n","  ",$multi_sql[$x]);
	$parmArr = preg_split("/[\s]+/",ltrim($parmArr_enter));
	switch ($parmArr[0]) {
		case 'select':
			if(in_array('*',$parmArr)){
				echo '提示：select *是否有必要查询所有的字段？</br>';
				$s++;
			}
			if(!in_array('where',$parmArr)){
                echo '<big><font color="#FF0000">警告！没有where条件，注意where后面的字段要加上索引</font></big></br>';
				$s++;
            }
			if(!in_array('limit',$parmArr)){
                echo '提示：没有limit会查询更多的数据</br>';
				$s++;
            }
			if(in_array("exists",$parmArr)){
                echo '<big><font color="#FF0000">警告！子查询性能低下，请转为join表关联</font></big></br>';
                $s++;
            }
            	if(in_array("in",$parmArr)){
			$countIn = array_count_values($parmArr);
			if(preg_match_all('/\(.*\)/',$parm,$out)){
                	echo "提示：in里面的数值不要超过1000个</br>";
                        $s++;
                	}
                	if(in_array("(select",$parmArr)){
				echo '<big><font color="#FF0000">警告！子查询性能低下，请转为join表关联</font></big></br>';
				        $s++;
			}
			if($countIn['select']>1){
				echo '<big><font color="#FF0000">警告!！子查询性能低下，请转为join表关联</font></big></br>';
                                        $s++;
                        }	
		}
			if(in_array("in(select",$parmArr)){
                echo '<big><font color="#FF0000">警告！子查询性能低下，请转为join表关联</font></big></br>';
                $s++;
			}
			
			if(in_array("join",$parmArr)){
				echo '提示：采用join关联，注意关联字段要都加上索引，如on a.id=b.id</br>';
				$countArr = array_count_values($parmArr);
				if($countArr['join']>1){
					echo '提示：MySQL对多表join关联性能低下，建议不要超过3个表以上的关联</br>';
					$s++;
				}
			}
			if(in_array("like",$parmArr)){
				if(preg_match_all("/'%(.)*%'/",$parm,$out)){
                     echo "<big><font color=\"#FF0000\">警告！like '%%'双百分号无法用到索引，like 'mysql%'这样是可以利用到索引的</font></big></br>";
                     $s++;
                }
			}
			if(in_array('group',$parmArr)){
				if(in_array('by',$parmArr)){
                     echo '提示：默认情况下，MySQL对所有GROUP BY col1，col2...的字段进行排序。如果查询包括GROUP BY，想要避免排序结果的消耗，则可以指定ORDER BY NULL禁止排序。</br>';
                     $s++;
				if(!in_array('having',$parmArr)){
		     echo '提示：是否要加一个having过滤下？</br>';
		     $s++;
				}
            }}
			if(in_array('order',$parmArr)){
                 if(preg_match_all("/by.*rand().*/",$parm,$out)){
                      echo '<big><font color="#FF0000">警告！MySQL里用到order by rand()在数据量比较多的时候是很慢的，因为会导致MySQL全表扫描，故也不会用到索引</font></big></br>';
                      $s++;
                 }
			     /*if(in_array('group',$parmArr)){
				      if(in_array('by',$parmArr)){
                            echo '提示：是否要加一个having过滤下？</br>';
                            $s++;
			          }
			     }*/
			     if(!in_array('group',$parmArr)){
			          if(preg_match_all("/count(.*)/",$parm,$out)){
                            echo '<big><font color="#FF0000">警告！禁止不必要的order by排序,因为前面已经count统计了</font></big></br>';
                            $s++;
                      }
                 }
    		}
			if(in_array('where',$parmArr)){
                 if(preg_match_all("/\(.*\)\s{0,}(>|<|=)/",$parm,$out)){
                            echo "<big><font color=\"#FF0000\">警告！MySQL里不支持函数索引，例DATE_FORMAT('create_time','%Y-%m-%d')='2016-01-01'是无法用到索引的，需要改写为create_time>='2016-01-01 00:00:00' and create_time<='2016-01-01 23:59:59'</font></big></br>";
                             $s++;
                 }
				 if(preg_match_all("/\(.*\)\s{0,}(>|<|=|between)/",$parm,$out)){
                             echo "<big><font color=\"#FF0000\">警告！MySQL里不支持函数索引，例DATE_FORMAT('create_time','%Y-%m-%d')='2016-01-01'是无法用到索引的，需要改写为create_time>='2016-01-01 00:00:00' and create_time<='2016-01-01 23:59:59'</font></big></br>";
                             $s++;
                 }
			}
			if($s==0){
                 echo 'SQL语句未发现问题</br>';
            }
			echo '</br>';
            //echo '<big><font color=\"#0000FF\">开始调用美团网SQLAdvisor进行第二次索引检查</font></big></br>';
			require 'sqladvisor_config.php';
			if ($message === ''){
                    echo "查询字段索引已经创建了,无需创建.</br>";
			}else{	
				echo "查询的字段没有创建索引，建议添加如下索引：</br>";
				echo $message."</br>";
				echo "<big><font color=\"#FF0000\">大表创建索引风险很高，如果一定要创建，请联系DBA进行执行。</font></big></br>";
			}	
			echo "</br>";
			echo "如果你觉得审核意见比较满意，请心中默念666，并推广给其他小伙伴使用。</br>";
            fclose($stream);
            fclose($errorStream);
			break;
		case 'insert':
			if(preg_match('/insert.*select/i',$multi_sql[$x])){
				echo "<big><font color=\"#FF0000\">警告: insert 表1 select 表2，会造成锁表。</font></big></br>";
                $is++;
            }
			if($is==0){
                echo 'insert语句未发现问题</br>';
                $c_insert=1;
            }
			break;
		case 'update':
			if(!in_array('where',$parmArr)){
                echo '<big><font color="#FF0000">警告！没有where条件，update会全表更新，禁止执行！！！</font></big></br>';
                exit;
            }
			$con1=mysql_connect($ip.":".$port,$user,$pwd);
            mysql_select_db($db, $con1);
            $result = mysql_query("explain  ".$multi_sql[$x], $con1);
            while($row = mysql_fetch_array($result)){
                  $record_rows=$row[8];
                  if($record_rows<=50000){
                         echo "</br>";
                         echo $parmArr[1]."表 where条件字段，扫描影响的行数小于50000行，可以由开发自助执行。</br>";
                  }
                  else{
                         echo '<big><font color="#FF0000">'.$parmArr[1].'表 where条件字段，扫描影响的行数是：'.$record_rows.' 行，超过50000行请联系DBA执行!!!</font></big></br>';
                         $up++;
                  }
            }
            mysql_close($con1);
			if($up==0){
                  echo 'update语句未发现问题</br>';
                  $c_update=1;
            }
			echo '</br>';
            //echo '<big><font color=\"#0000FF\">开始调用美团网SQLAdvisor进行第二次索引检查</font></big></br>';
            require 'sqladvisor_config.php';
            
            if ($message === ''){
                     echo "更新的where条件字段索引已经创建了,无需创建.</br>";
            }else{
                     echo "<big><font color=\"#FF0000\">更新的where条件字段没有创建索引，建议添加如下索引：</font></big></br>";
                     echo $message."</br>";
                     echo "<big><font color=\"#FF0000\">InnoDB存储引擎是通过给索引上的索引项加锁来实现的，这就意味着：只有通过索引条件检索数据，InnoDB才会使用行级锁，否则，InnoDB将使用表锁。</font></big></br>";
            }
            echo "</br>";
            echo "如果你觉得审核意见比较满意，请心中默念666，并推广给其他小伙伴使用。</br>";
            fclose($stream);
            fclose($errorStream);
            break;
		case 'delete':
			echo 'delete删除数据属于高危语句，需要人工审核</br>';
			break;
		case 'create':
		    if(preg_match('/create\s*index/',$multi_sql[$x])){
                    echo "<big><font color=\"#FF0000\">警告！不支持create index语法，请更改为alter table add index语法。</font></big></br>";
                    break;
            }
			if(!in_array('primary',$parmArr)){
                    echo "<big><font color=\"#FF0000\">警告！$parmArr[2]表没有主键</font></big></br>";
                    $c++;
            }
			if(in_array('primary',$parmArr)){
				    if(!preg_match('/AUTO_INCREMENT[ |,]/i',$multi_sql[$x])){
                           echo "<big><font color=\"#FF0000\">警告！$parmArr[2]表主键应该是自增的，缺少AUTO_INCREMENT</font></big></br>";
                           $c++;
                    }
            }
			if(!preg_match('/auto_increment=1 /i',$multi_sql[$x])){
				    echo "提示：id自增字段默认值为1，auto_increment=1 </br>";
			}
			if(preg_match_all('/\bkey\b/i',$multi_sql[$x],$match)){
				    if(!in_array('index',$parmArr)){
 					$countkey = array_count_values($parmArr);
					    if($countkey['key'] == 1){
                             echo "<big><font color=\"#FF0000\">警告！$parmArr[2]表没有索引</font></big></br>";
				             $c++;
                        }
                    }
			}
			if(in_array('key',$parmArr)){
				  $countkey = array_count_values($parmArr);
                  if($countkey['key']>=15){
                          echo "<big><font color=\"#FF0000\">警告！表中的索引数已经超过5个，索引是一把双刃剑，它可以提高查询效率但也会降低插入和更新的速度并占用磁盘空间，请让dba使用pt-duplicate-key-checker --user=root --password=xxxx --host=localhost --socket=/tmp/mysql.sock来检查重复的索引</font></big></br>";
                          $c++;
                  }
				  $countkey = array_count_values($parmArr);
                  if($countkey['index']>=15){
                           echo "<big><font color=\"#FF0000\">警告！表中的索引数已经超过5个，索引是一把双刃剑，它可以提高查询效率但也会降低插入和更新的速度
并占用磁盘空间。</font></big></br>";
                           $c++;
                  }
			}
			if(!in_array('comment',$parmArr)){
                  echo "<big><font color=\"#FF0000\">警告！$parmArr[2]表字段没有中文注释，COMMENT应该有默认值，如COMMENT '姓名'</font></big></br>";
                  $c++;
            }
            if(!preg_match_all("/comment=.*/",$parm,$out)){
                  echo "<big><font color=\"#FF0000\">警告！$parmArr[2]表没有中文注释，例：COMMENT='新版授信项表'</font></big></br>";
                  $c++;
			}
			if(!preg_match_all("/.*utf8.*/",$parm,$out)){
                  echo "<big><font color=\"#FF0000\">警告！$parmArr[2]表缺少utf8字符集，否则会出现乱码</font></big></br>";
                  $c++;
            }
			if(!in_array('engine=innodb',$parmArr)){
                  echo "<big><font color=\"#FF0000\">警告！$parmArr[2]表存储引擎应设置为InnoDB</font></big></br>";
                  $c++;
            }
			if(in_array('timestamp',$parmArr)){
				  if(!in_array('current_timestamp',$parmArr)){
                          echo "<big><font color=\"#FF0000\">警告！$parmArr[2]表应该为timestamp类型加默认系统当前时间。例如：update_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间' </font></big></br>";
                          $c++;
				  }
            }
            if(preg_grep('/.*utf8_bin/',$parmArr)){
            	echo "<big><font color=\"#FF0000\">警告！$parmArr[2]表 utf8_bin应使用默认的字符>集核对utf8_general_ci。</font></big><br>";
                $c++;
            }
            if($c==0){
                  echo '建表语句未发现问题</br>';
                  $c_create=1;
            }
			break;
		case 'alter':
            array_push($alter_array,$parmArr[2]);
			array_push($alter_parm,$parmArr[0]);	
            $con2=mysql_connect($ip.":".$port,$user,$pwd); 
			mysql_select_db($db, $con2);
			$result = mysql_query("explain select * from ".$parmArr[2], $con2);
			while($row = mysql_fetch_array($result)){
				   $record_rows=$row[8];
				   if($record_rows<=1500000){
					    echo "</br>";
					    echo $parmArr[2]."表记录小于150万行，可以由开发自助执行。</br>";
				   }
			       else{
					    echo '<big><font color="#FF0000">'.$parmArr[2].'表记录是：'.$record_rows.' 行，表太大请联系DBA执行!!!</font></big></br>';
					    exit;
				   }  
			}
			mysql_close($con2);
            if(in_array('drop',$parmArr)){
                   if(!preg_match('/drop.*index/i',$multi_sql[$x])){
                        echo "<big><font color=\"#FF0000\">警告！你要对$parmArr[2]表删除字段，数据会存在丢失的风险，请走审批！！！</font></big></br>";
                        exit;
                   }
            }
		    if($at==0){
                   echo 'alter语句未发现问题</br>';
                   $c_alter=1;
            }else{exit;}
            break;
		default:
			echo '请输入正确的sql语句</br>';
			break;
	}
}
$sql_count=$sql_count+1;
    if (($alter_parm[0] == $alter_parm[1] && $alter_parm[1] == 'alter') && $alter_array[0] == $alter_array[1]){
        echo "<p>";
        echo "<big><font color='#FF0000'>警告！更改表结构要减少与数据库的交互次数，应改为，例alter table t1 add index IX_uid(uid),add index IX_name(name);</font></big><p>";
        exit;
    }
}
if($c_create==1 || $c_insert==1 || $c_alter==1 || $c_update==1){
      echo '</br>';
      echo '<form action="sql_submit.php" method="post">';
      echo ' 输入你的DB用户名: <input type="text" name="dbuser">';
      echo ' 输入你的DB密码: <input type="password" name="dbpwd">';
      echo '<input type="hidden" name="sql" value="'.htmlspecialchars($parm_post).'">';
      echo '<input type="hidden" name="dbip" value="'.$ip.'">';
      echo '<input type="hidden" name="dbname" value="'.$db.'">';
      echo '<input type="hidden" name="dbport" value="'.$port.'">';
      echo ('&nbsp;&nbsp;&nbsp').'<input type="submit" onclick="javascript:return confirm(\'你确认提交吗？\')" value="我要上线!!!">';
      echo '</form>';
      echo '<img src=image/go.png />';
}
?>
