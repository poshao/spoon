# vba 加载项管理数据库

set names utf8;
use `avery_logistics`;

#插入操作权限
insert into auth_permissions(`permissionname`,`description`) values ('app_vba_addin_list','列举加载项列表');
insert into auth_permissions(`permissionname`,`description`) values ('app_vba_addin_upload','上传加载项');
insert into auth_permissions(`permissionname`,`description`) values ('app_vba_user_assign_addin','授权用户加载项');
insert into auth_permissions(`permissionname`,`description`) values ('app_vba_user_assign_fun','授权用户子功能项');
insert into auth_permissions(`permissionname`,`description`) values ('app_vba_user_list','枚举用户列表');
insert into auth_permissions(`permissionname`,`description`) values ('app_vba_fun_list','枚举子功能列表');

#用户表
drop table if exists `vba_users`;
create table `vba_users`(
  `id` int(11) primary key auto_increment not null,
  `loginname` varchar(50) not null comment 'windows登录名',
  `username` varchar(50) not null comment 'AD域用户名',
  `status` varchar(10) not null default 'normal' comment '用户状态',
  `create_time` datetime not null default CURRENT_TIMESTAMP,
  `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `vba_users` ADD UNIQUE INDEX `user_unique` (`loginname` ASC);

#加载项表
drop table if exists `vba_addins`;
create table `vba_addins`(
  `id` int(11) primary key auto_increment not null,
  `addin_name` varchar(30) not null comment '名称',
  `version` varchar(10) not null comment '版本号',
  `description` varchar(100) comment '备注',
  `hashname` varchar(32) not null comment '存储文件名',
  `create_time` datetime not null default CURRENT_TIMESTAMP,
  `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `vba_addins` ADD UNIQUE INDEX `addin_unique` (`addin_name` ASC,`version` ASC);

#功能项表
drop table if exists `vba_funs`;
create table `vba_funs`(
  `id` int(11) primary key auto_increment not null,
  `fun_name` varchar(50) not null comment '名称',
  `addin_name` varchar(30) not null comment '加载项名称',
  `description` varchar(100) comment '描述',
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

# 组件包列表
drop table if exists `vba_packages`;
create table `vba_packages`(
  `id` int(11) primary key auto_increment not null,
  `name` varchar(20) not null comment '包名称',
  `version` varchar(10) not null comment '版本号',
  `author` varchar(20) not null comment '作者',
  `description` varchar(100) comment '概述',
  `hashname` varchar(32) not null comment '存储文件名',
  `create_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `vba_packages` ADD UNIQUE INDEX `package_unique` (`name` ASC,`version` ASC);

#插入测试数据
#insert into vba_users(`loginname`,`username`) values('0115289','Byron Gong');

#同步原数据(注意version过长)
#insert into vba_users(`id`,`loginname`,`username`,`create_time`) select `id`,`loginname`,`username`,`create_time` from autoload_users;
#insert into vba_addins(`id`,`addin_name`,`version`,`description`,`hashname`,`create_time`) select `id`,`name`,`version`,`message`,`hashname`,`create_time` from autoload_addins;
#insert into vba_funs(`id`,`fun_name`,`addin_name`,`description`,`create_time`) select `id`,`name`,`addin_name`,'',`create_time` from autoload_funs;
#insert into vba_ref_user_addin(`id`,`addinid`,`userid`,`create_time`) select `id`,`addin_id`,`user_id`,`create_time` from autoload_ref_user_addin;
#insert into vba_ref_user_fun(`id`,`userid`,`funid`,`create_time`) select `id`,`user_id`,`fun_id`,`create_time` from autoload_ref_user_fun;