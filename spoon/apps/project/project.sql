set names utf8;
use `avery_logistics`;

#插入权限
insert into auth_permissions(`permissionname`,`description`) values ('app_project_projects_create','创建项目');

insert into auth_permissions(`permissionname`,`description`) values ('app_project_projects_update_status_request2pass','状态更新');
insert into auth_permissions(`permissionname`,`description`) values ('app_project_projects_update_status_request2cancel','状态更新');
insert into auth_permissions(`permissionname`,`description`) values ('app_project_projects_update_status_pass2check','状态更新');
insert into auth_permissions(`permissionname`,`description`) values ('app_project_projects_update_status_check2process','状态更新');
insert into auth_permissions(`permissionname`,`description`) values ('app_project_projects_update_status_check2pending','状态更新');
insert into auth_permissions(`permissionname`,`description`) values ('app_project_projects_update_status_process2pending','状态更新');
insert into auth_permissions(`permissionname`,`description`) values ('app_project_projects_update_status_pending2check','状态更新');
insert into auth_permissions(`permissionname`,`description`) values ('app_project_projects_update_status_pending2cancel','状态更新');
insert into auth_permissions(`permissionname`,`description`) values ('app_project_projects_update_status_process2finish','状态更新');


#创建项目表
drop table if exists `project_projects`;
create table `project_projects`(
    `id` int(11) primary key auto_increment not null,
    `subject` varchar(50) not null comment '项目名称',
    `description` varchar(100) comment '描述',
    `files` json comment '附件列表',

    `request` int(11) not null comment '提出人/跟进人',
    `request_time` datetime comment '提出时间',
    `audit` int(11) comment '审核人',
    `audit_time` datetime comment '审核时间',
    `develop` int(11) comment '开发人',
    `develop_time` datetime comment '开发受理时间',
    `finish_time` datetime comment '完成状态',
    
    `status` enum('request','pass','cancel','check','pending','process','finish') default 'request' not null comment '项目状态',
    `project_id` int(11) comment '项目ID(用户分组)',
    `create_time` datetime not null default CURRENT_TIMESTAMP,

    `last_user` int(11) not null comment '最后操作人工号',
    `last_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;

#项目日志
drop table if exists `project_logs`;
create table `project_logs`(
  `id` int(11) primary key auto_increment not null,
  `project_id` int(11) not null comment '项目ID',
  `subject` varchar(50) comment '项目名称',
  `description` varchar(100) comment '描述',
  `status` varchar(20) comment '状态',
  `files` json comment '附件',
  `operator` int(11) not null comment '操作人工号',
  `create_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;