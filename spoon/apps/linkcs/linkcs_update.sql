#2019/05/12
#增加列标识是否有附件
update cs_orders set has_attachment='y' where json_depth(json_extract(`detail`,'$.files'))>1;