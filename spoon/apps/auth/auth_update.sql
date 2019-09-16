set names utf8;
use `avery_logistics`;

#2019/08/24

#用户通过邮件重置密码
drop table if exists `auth_tasks`;
create table `auth_tasks`(
  `userid` int(11) not null,
  `code` varchar(10) not null comment '验证码',
  `create_time` datetime default CURRENT_TIMESTAMP
)engine=innodb DEFAULT CHARSET=utf8;