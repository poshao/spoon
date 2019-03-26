set names utf8;

drop database if exists `avery_logistics`;
create database `avery_logistics` default charset utf8 collate utf8_general_ci;

use `avery_logistics`;

#用户认证数据库

#创建用户表
drop table if exists `auth_users`;
create table `auth_users`(
    `id` int(11) primary key auto_increment not null,
    `workid` int(11) not null comment '工号',
    `username` varchar(30) comment '用户名',
    `password` varchar(64) not null comment '密码',
    `depart` varchar(30) comment '部门',
    `email` varchar(100) comment '邮箱',
    `phone` varchar(20) comment '电话',
    `status` varchar(10) not null default 'unaudited' comment '用户状态',
    `avator` varchar(200) comment '用户头像',
    `create_time` datetime not null default CURRENT_TIMESTAMP,
    `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `auth_users` ADD UNIQUE INDEX `workid_unique` (`workid` ASC);

#创建角色表
drop table if exists `auth_roles`;
create table `auth_roles`(
    `id` int(11) primary key auto_increment not null,
    `rolename` varchar(50) not null comment '角色名称',
    `description` varchar(100) comment '描述',
    `create_time` datetime not null default CURRENT_TIMESTAMP,
    `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `auth_roles` ADD UNIQUE INDEX `rolename_unique`(`rolename` ASC);

#创建分组表
drop table if exists `auth_groups`;
create table `auth_groups`(
    `id` int(11) primary key auto_increment not null,
    `groupname` varchar(60) not null comment '分组名称',
    `description` varchar(100) comment '描述',
    `roleid` int(11) not null default 0 comment '默认角色',
    `create_time` datetime not null default CURRENT_TIMESTAMP,
    `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `auth_groups` ADD UNIQUE INDEX `groupname_unique`(`groupname` ASC);

#创建权限表
drop table if exists `auth_permissions`;
create table `auth_permissions`(
    `id` int(11) primary key auto_increment not null,
    `permissionname` varchar(50) not null comment '权限名称',
    `description` varchar(100) comment '描述',
    `create_time` datetime not null default CURRENT_TIMESTAMP,
    `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `auth_permissions` ADD UNIQUE INDEX `permissionname_unique`(`permissionname` ASC);

#创建登录状态表
drop table if exists `auth_sessions`;
create table `auth_sessions`(
    `id` int(11) primary key auto_increment not null,
    `userid` int(11) not null comment '用户ID',
    `token` varchar(64) not null comment '用户密钥',
    `ip` varchar(50) comment 'ip地址',
    `valid_time` datetime not null comment '有效时间',
    `create_time` datetime not null default CURRENT_TIMESTAMP,
    `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `auth_sessions` ADD UNIQUE INDEX `session_unique`(`userid` ASC,`ip` ASC);

#创建用户角色关联表
drop table if exists `auth_ref_user_role`;
create table `auth_ref_user_role`(
    `id` int(11) primary key auto_increment not null,
    `userid` int(11) not null comment '用户ID',
    `roleid` int(11) not null comment '角色ID',
    `create_time` datetime not null default CURRENT_TIMESTAMP,
    `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `auth_ref_user_role` ADD UNIQUE INDEX `userrole_unique`(`userid` ASC,`roleid` ASC);

#创建角色权限关联表
drop table if exists `auth_ref_role_permission`;
create table `auth_ref_role_permission`(
    `id` int(11) primary key auto_increment not null,
    `roleid` int(11) not null comment '角色ID',
    `permissionid` int(11) not null comment '权限ID',
    `create_time` datetime not null default CURRENT_TIMESTAMP,
    `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `auth_ref_role_permission`ADD UNIQUE INDEX `rolepermission_unique`(`roleid` ASC,`permissionid` ASC);

#创建用户分组关联表
drop table if exists `auth_ref_user_group`;
create table `auth_ref_user_group`(
    `id` int(11) primary key auto_increment not null,
    `userid` int(11) not null comment '用户ID',
    `groupid` int(11) not null comment '分组ID',
    `create_time` datetime not null default CURRENT_TIMESTAMP,
    `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `auth_ref_user_group` ADD UNIQUE INDEX `usergroup_unique`(`userid` ASC,`groupid` ASC);

################################
# 函数 检查用户权限
################################
drop FUNCTION if exists `checkPermission`;
delimiter $$
CREATE FUNCTION `checkPermission`(p_userid int(11),p_permission varchar(50)) RETURNS int(10)
BEGIN
declare rlst int(10) default 0;
SELECT COUNT(*) into rlst FROM `auth_ref_role_permission` WHERE `roleid` IN (SELECT `roleid` FROM `auth_groups` WHERE `id` IN (SELECT `groupid` FROM `auth_ref_user_group` WHERE `userid` = p_userid)
      UNION ALL
      SELECT `roleid` FROM `auth_ref_user_role` WHERE `auth_ref_user_role`.`userid` = p_userid) AND `permissionid` IN (SELECT `id` FROM `auth_permissions` WHERE `permissionname` = p_permission);
RETURN rlst;
END$$
delimiter ;

################################
# 设定权限更新触发器
################################
delimiter $$
create trigger update_admin_permission after insert on auth_permissions for each row
BEGIN
    insert into auth_ref_role_permission(`roleid`,`permissionid`) select id,new.id from auth_roles where rolename='administrator';
END$$
delimiter ;

################################
#insert Role Data
################################
insert into auth_roles(`rolename`,`description`) values ('administrator','系统管理员');

################################
#insert Permission Data
################################
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_user_list','列举用户列表');
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_user_update','更新用户信息');
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_user_getinfo','获取用户信息');
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_user_register','用户注册');
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_user_login','用户登录');
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_user_logout','用户注销');
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_user_assign_group','绑定用户分组');
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_user_assign_role','绑定用户角色');
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_user_reset_password','重置用户密码');

insert into auth_permissions(`permissionname`,`description`) values ('app_auth_role_create','创建角色');
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_role_list','枚举角色');
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_role_update','更新角色');
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_assign_permission','关联角色权限');

insert into auth_permissions(`permissionname`,`description`) values ('app_auth_permission_create','创建权限');
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_permission_list','枚举权限');
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_permission_update','更新权限');

insert into auth_permissions(`permissionname`,`description`) values ('app_auth_group_create','创建分组');
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_group_list','枚举分组');
insert into auth_permissions(`permissionname`,`description`) values ('app_auth_group_update','更新分组');

################################
#insert User Data
################################
#默认密码 123456
insert into auth_users(`workid`,`username`,`depart`,`password`) values ('8123456','Byron Gong','Logistics','MjJjMmViZDhhMWNiNDc5MzM2OWQ2MTQ5MmJjMjBmYzQ=');
insert into auth_ref_user_role(`userid`,`roleid`) values(1,1);



