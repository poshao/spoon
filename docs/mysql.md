# MySQL说明
## 创建用户
``` sql
create user 'sa'@'%' identified with mysql_native_password by '123456';
```
## 修改密码
``` sql
-- 修改认证方式及密码
alter user 'root'@'localhost' identified with mysql_native_password by '123465';
-- 刷新权限
flush privileges;
```
## JSON原生支持
### 查询
``` sql
select json_detail->'$.id' from cs_detail;
```
### 虚拟列
``` sql
ALTER TABLE `cs_detail` ADD no VARCHAR(15) GENERATED ALWAYS AS (json_detail->'$.no') VIRTUAL;
```