set names utf8;
use `avery_logistics`;

#创建文件类别表
drop table if exists `sys_file_categories`;
create table `sys_file_categories`(
  `id` int(11) primary key auto_increment not null,
  `name` varchar(20) not null comment '名称',
  `description` varchar(100) comment '描述',
  `create_time` datetime not null default CURRENT_TIMESTAMP,
  `last_user` int(11) comment '最后修改人工号',
  `last_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;

insert into `sys_file_categories`(`name`,`description`) values('unknown','未知分类');

#创建文件目录
drop table if exists `sys_files`;
create table `sys_files`(
  `id` int(11) primary key auto_increment not null,
  `categoryid` int(11) not null default 1 comment '分类',
  `hashname` varchar(64) not null comment '唯一名称',
  `status` varchar(10) not null default 'normal' comment '文件状态',
  `folder` varchar(100) not null comment '相对目录',
  `origin_name` varchar(100) not null comment '原始文件名',
  `owner` int(11) not null comment '所有者',
  `create_time` datetime not null default CURRENT_TIMESTAMP,
  `last_user` int(11) comment '最后修改人',
  `last_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `sys_files` ADD UNIQUE INDEX `hashname_unique` (`hashname` ASC);
