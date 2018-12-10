# 一个简单的RESTful的PHP框架

## 已完成
1. 配置类 Spoon\Core\Config.php
2. 日志类 Spoon\Core\Logger.php

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