# 一个简单的RESTful的PHP框架

## 功能说明
    参数检查:
    require,option
    length,length-max,length-min

    1.数值 max,min
    2.布尔值 true|false
    3.列表项 item1| item2 | item3
    4.文本
    5.正则表达式



## 已完成
1. 配置类 Spoon\Core\Config.php
2. 日志类 Spoon\Core\Logger.php

# 参考文档
* [RBAC用户管理设计](./docs/RBAC.md)
* [HTTP报文结构](./docs/HTTP.md)
* [RESTful设计指南](./docs/RESTful_design.md)

# 其他设置
#### 响应头隐藏PHP信息
修改php.ini中参数 
``` ini
expose_php = Off
```
#### 响应头隐藏Apache信息
在Apache 的http.conf中添加：
``` ini
ServerSignature Off #错误页面页脚提示
ServerTokens Prod #Http头Server字段仅返回 Apache
```