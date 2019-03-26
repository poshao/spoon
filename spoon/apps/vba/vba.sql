# vba 加载项管理数据库

set names utf8;
use `avery_logistics`;

#加载项表
drop table if exists `vba_addins`;
create table `vba_addins`(
  `id` int(11) primary key auto_increment not null,
  `name` varchar(30) not null comment '名称',
  `version` varchar(10) not null comment '版本号',
  `hashname` varchar(32) not null comment '存储文件名',
  `create_time` datetime not null default CURRENT_TIMESTAMP,
  `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `vba_addins` ADD UNIQUE INDEX `addin_unique` (`name` ASC,`version` ASC);

#功能项表
drop table if exists `vba_funs`;
create table `vba_funs`(
  `id` int(11) primary key auto_increment not null,
  `name` varchar(50) not null comment '名称',
  `addin_name` varchar(30) not null comment '加载项名称',
  `create_time` datetime not null default CURRENT_TIMESTAMP,
  `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;

#授权表
drop table if exists `vba_ref_user_addin`;
create table `vba_ref_user_addin`(
  `id` int(11) primary key auto_increment not null,
  `addinid` int(11) not null comment '加载项ID',
  `userid` int(11) not null comment '用户ID',
  `create_time` datetime not null default CURRENT_TIMESTAMP,
  `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `vba_ref_user_addin` ADD UNIQUE INDEX `assign_addin_unique` (`addinid` ASC,`userid` ASC);

#功能授权表
drop table if exists `vba_ref_user_fun`;
create table `vba_ref_user_fun`(
  `id` int(11) primary key auto_increment not null,
  `userid` int(11) not null comment '用户ID',
  `funid` int(11) not null comment '功能ID',
  `create_time` datetime not null default CURRENT_TIMESTAMP,
  `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `vba_ref_user_fun` ADD UNIQUE INDEX `assign_fun_unique` (`userid` ASC,`funid` ASC);
