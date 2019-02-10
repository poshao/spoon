# MySQL说明

## JSON原生支持
### 查询
``` sql
select json_detail->'$.id' from cs_detail;
```
### 虚拟列
``` sql
ALTER TABLE `cs_detail` ADD no VARCHAR(15) GENERATED ALWAYS AS (json_detail->'$.no') VIRTUAL;
```