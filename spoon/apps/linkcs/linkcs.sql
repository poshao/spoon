#CS放单平台数据库
set names utf8;

use `avery_logistics`;

#添加权限
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_newrequest','CS资料');
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_list_request','查看订单列表');
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_orders_update_status_presend','更新订单状态(草稿)');
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_orders_update_status_sended','更新订单状态(已发送)');
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_orders_update_status_pass','更新订单状态(通过)');
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_orders_update_status_reject','更新订单状态(异常)');
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_orders_update_status_finish','更新订单状态(完成)');
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_orders_update_status_cancel','更新订单状态(取消)');
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_orders_update_status_resend','更新订单状态(已重发)');

insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_file_add','添加附件');
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_file_list','枚举已上传的文件列表');
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_file_delete','删除文件');
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_file_get','获取文件');
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_report_create_struct','新增表格定义');
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_report_update_struct','更新表格定义');
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_report_delete_struct','删除表格定义');
insert into auth_permissions(`permissionname`,`description`) values ('app_linkcs_report_get_struct','获取表结构');

#CS放单资料表
drop table if exists `cs_detail`;
create table `cs_detail`(
  `id` int(11) primary key auto_increment not null,
  `dnei` varchar(60) GENERATED ALWAYS AS (json_unquote(json_extract(`json_detail`,'$.dnei'))) VIRTUAL,
  `level` varchar(15) GENERATED ALWAYS AS (json_unquote(json_extract(`json_detail`,'$.level'))) VIRTUAL,
  `json_detail` JSON comment '详细数据',
  
  `creator` int(11) not null comment '创建人工号',
  `assign` int(11) comment '受理人工号',
  `status` varchar(10) not null default 'unknow' comment '订单状态',
  `reject_reason` varchar(100) comment 'reject 原因',
  `create_time` datetime not null default CURRENT_TIMESTAMP,
  `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;

#CS放单资料表(new)
drop table if exists `cs_orders`;
create table `cs_orders`(
  `id` int(11) primary key auto_increment not null,
  `dnei` varchar(60) not null comment '单号最多填写5个',
  `level` varchar(15) not null comment '紧急程度',
  `system` varchar(15) not null comment '系统',

  `detail` JSON comment '详细数据',

  `creator` int(11) not null comment '创建人(工号)',
  `create_time` datetime not null default CURRENT_TIMESTAMP,

  `assign` int(11) comment '受理人(工号)',
  `assign_time` datetime comment '受理时间',

  `status` ENUM('pre_send','sended','pass','reject','cancel','resend','finish') not null comment '状态',
  `reject_reason` varchar(100) comment 'reject 原因',
  `parentid` int(11) comment '订单组id',

  `last_user` int(11) not null comment '最后的操作账号(工号)',
  `last_time` datetime not null default CURRENT_TIMESTAMP comment '最后操作时间'
)engine=innodb;

#表结构定义
drop table if exists `cs_detail_report`;
create table `cs_detail_report`(
  `id` int(11) primary key auto_increment not null,
  `name` varchar(20) not null comment '报表名称',
  `struct` JSON not null comment '表结构描述',
  `creator` int(11) not null comment '创建人',
  `create_time` datetime not null default CURRENT_TIMESTAMP,
  `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
ALTER TABLE `cs_detail_report` ADD UNIQUE INDEX `name_unique` (`name` ASC);