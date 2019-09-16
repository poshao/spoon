#2019/05/12
#增加列标识是否有附件
update cs_orders set has_attachment='y' where json_depth(json_extract(`detail`,'$.files'))>1;

# 2019/08/22
# 修改状态选项
ALTER TABLE `cs_orders` CHANGE COLUMN `status` `status` enum('pre_send','sended','pass','reject','cancel','resend','finish','lock','received') NOT NULL DEFAULT 'pre_send' COMMENT '状态';
update `cs_orders` set `status`='received' where `status`='lock';
update `cs_orders` set `status`='finish' where `status`='pass';
ALTER TABLE `cs_orders` CHANGE COLUMN `status` `status` enum('pre_send','sended','reject','cancel','resend','finish','received') NOT NULL DEFAULT 'pre_send' COMMENT '状态';