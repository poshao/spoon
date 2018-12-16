# HTTP报文结构

## 1. 请求报文

    请求方法 空格 URL 空格 协议版本 回车换行
    头部字段名 : 值 回车换行
        ...
    头部字段名 : 值 回车换行
    回车换行
    数据报文

示例
``` http
POST http://localhost HTTP/1.1
Content-Type:text/html;charset=utf-8
Accept: image/gif, image/x-xbitmap
Cookie: PREF=ID=80a06da87be9ae3c:NW=1:S=ybYcq2wpfefs4V9g;

hl=zh-CN&source=hp&q=domety
```
## 2. 响应报文
    协议版本 空格 状态码 空格 状态描述 回车换行
    头部字段名 : 值 回车换行
        ...
    头部字段名 : 值 回车换行
    回车换行
    数据报文
    
示例
``` http
HTTP/1.1 200 OK
Date: Sat, 31 Dec 2005 23:59:59 GMT
Content-Type: text/html;charset=ISO-8859-1
Content-Length: 122

<html>
<head>
<title>Wrox Homepage</title>
</head>
<body>
<!-- body goes here -->
</body>
</html>
```