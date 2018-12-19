# 一个简单的RESTful的PHP框架

## 1. 请求流程
                                   开始
                                    ↓
    +-----+                  +--------------+
    |  E  |                  | 1.Request    |
    |  R  |                  +--------------+
    |  R  |                         ↓
    |  O  |                  +--------------+
    |  R  |                  | 2.Router     |
    |     |                  +--------------+
    |  H  |                         ↓
    |  A  |  +--------+      +--------------+      +---------+
    |  N  |  | 4.View |  ←→  | 3.Controller |  ←→  | 5.Model |
    |  D  |  +--------+      +--------------+      +---------+
    |  L  |                         ↓
    |  E  |                  +--------------+
    |  R  |                  | 6.Response   |
    +-----+                  +--------------+
                                    ↓
                                   结束
    流程顺序：
        Request -> Router -> Controller[View/Model] -> Response

> Error Handler: 错误处理

> Request: 用户请求 包含 URL,Method,Filter,Data

> Router: 路由分发 根据URL进行初级任务分发

> View: 视图层 初步验证参数,返回结果

> Controller: 控制器 调用Model层并将结果反馈给View

> Model: 模型 资源操作，如数据库，打印机，Socket IO等

> Response: 响应数据 封装view提供的数据(根据请求的 Accept数据格式 对结果转码输出)

## 2.功能说明
    参数检查:
    require,option
    length,length-max,length-min

    1.数值 max,min
    2.布尔值 true|false
    3.列表项 item1| item2 | item3
    4.文本
    5.正则表达式
    6.数组
    
## 已完成
1. 配置类 Spoon\Core\Config.php
2. 日志类 Spoon\Core\Logger.php

## 常用加密算法
* DES
* AES
* RSA
* Base64
* MD5
* SHA1

# 参考文档
* [RBAC用户管理设计](./docs/RBAC.md)
* [HTTP报文结构](./docs/HTTP.md)
* [RESTful设计指南](./docs/RESTful_design.md)
* [NotORM使用说明](./docs/notorm.md)
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