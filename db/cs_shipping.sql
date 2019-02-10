#CS放单平台数据库
set names utf8;

use `Spoon`;

#CS放单资料表
drop table if exists `cs_detail`;
create table `cs_detail`(
  `id` int(11) primary key auto_increment not null,
  `dnei` varchar(15) GENERATED ALWAYS AS (json_unquote(json_extract(`json_detail`,'$.dnei'))) VIRTUAL,
  `json_detail` JSON comment '详细数据',
  `creator` int(11) not null comment '工号',
  `create_time` datetime not null default CURRENT_TIMESTAMP,
  `update_time` datetime not null default CURRENT_TIMESTAMP
)engine=innodb;
