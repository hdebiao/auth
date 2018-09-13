# auth
auth权限认证

## 说明: 
auth权限验证类使用到三张数据表:auth_user,auth_group,auth_rule,它们的作用分别为:
auth_user:用户数据表
auth_group:用户组别表,用于保存用户属于哪一个组的数据
auth_rule:用户规则表,用于保存组别和规则的数据

~~~SQL
create table auth_user
(
  id     int(11) unsigned auto_increment
    primary key,
  name   varchar(80) default ''  not null
  comment '用户名',
  groups varchar(255) default '' not null
  comment '所属组别',
  constraint name
  unique (name)
)
  charset = utf8;
~~~

~~~SQL
create table auth_group
(
  id     mediumint unsigned auto_increment
    primary key,
  name   char(100) default ''   not null
  comment '组名',
  status tinyint(1) default '1' not null
  comment '是否生效',
  rules  char(255) default ''   not null
  comment '拥有的规则'
)
  charset = utf8;
~~~

~~~SQL
create table auth_rule
(
  id          int(11) unsigned auto_increment
    primary key,
  name        varchar(80) default ''       not null
  comment '规则名称',
  title       varchar(100) default ''      not null
  comment '规则说明',
  creatUserId int(11) unsigned default '0' not null
  comment '创建人id',
  status      tinyint(1) default '1'       not null
  comment '是否生效',
  constraint name
  unique (name)
)
  charset = utf8;
~~~


## 使用方法
1. 引入自动加载文件
2. 配置数据库信息
3. 实例化类,传入规则名称和用户uid即可验证规则
~~~
require_once './vendor/autoload.php';

use Hdb\Auth\Auth;

$dataConfig = array(
    'host' => 'localhost',
    'user' => 'root',
    'password' => '1122',
    'port' => 3306,
    'database' => 'auth'
);

$auth = new Auth($dataConfig);

$checkRule= $auth->checkRule('checkProject', 2);

~~~


