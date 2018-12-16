# RESTful风格说明
## 1. 版本
应该将API的版本号放入URL。

    https://api.example.com/v1/
另一种做法是，将版本号放在HTTP头信息中，但不如放入URL方便和直观。Github采用这种做法

## 2. 路径
### 路径又称"终点"(endpoint)，表示API的具体网址。

在RESTful架构中，每个网址代表一种资源(resource)，所以网址中不能有动词，只能有名词，而且所用的名词往往与数据库的表格名对应。一般来说，数据库中的表都是同种记录的"集合"(collection)，所以API中的名词也应该使用复数。

    举例来说，有一个API提供动物园(zoo)的信息，还包括各种动物和雇员的信息，则它的路径应该设计成下面这样。

    https://api.example.com/v1/zoos
    https://api.example.com/v1/animals
    https://api.example.com/v1/employees

## 3. HTTP动词

### 对于资源的具体操作类型，由HTTP动词表示。
### 常用的HTTP动词有下面五个(括号里是对应的SQL命令)。

    GET(SELECT)：从服务器取出资源(一项或多项)。
    POST(CREATE)：在服务器新建一个资源。
    PUT(UPDATE)：在服务器更新资源(客户端提供改变后的完整资源)。 
    PATCH(UPDATE)：在服务器更新资源(客户端提供改变的属性)。
    DELETE(DELETE)：从服务器删除资源。

### 还有两个不常用的HTTP动词。

    HEAD：获取资源的元数据。如资源的哈希编码,创建时间
    OPTIONS：获取信息，关于资源的哪些属性是客户端可以改变的。

## 4. 状态码
### 服务器向用户返回的状态码和提示信息，常见的有以下一些(方括号中是该状态码对应的HTTP动词)。

    200 OK - [GET]：服务器成功返回用户请求的数据，该操作是幂等的(Idempotent)。
    201 CREATED - [POST/PUT/PATCH]：用户新建或修改数据成功。
    202 Accepted - [*]：表示一个请求已经进入后台排队(异步任务)
    204 NO CONTENT - [DELETE]：用户删除数据成功。
    400 INVALID REQUEST - [POST/PUT/PATCH]：用户发出的请求有错误，服务器没有进行新建或修改数据的操作，该操作是幂等的。
    401 Unauthorized - [*]：表示用户没有权限(令牌、用户名、密码错误)。
    403 Forbidden - [*] 表示用户得到授权(与401错误相对)，但是访问是被禁止的。
    404 NOT FOUND - [*]：用户发出的请求针对的是不存在的记录，服务器没有进行操作，该操作是幂等的。
    406 Not Acceptable - [GET]：用户请求的格式不可得(比如用户请求JSON格式，但是只有XML格式)。
    410 Gone -[GET]：用户请求的资源被永久删除，且不会再得到的。
    422 Unprocesable entity - [POST/PUT/PATCH] 当创建一个对象时，发生一个验证错误。
    500 INTERNAL SERVER ERROR - [*]：服务器发生错误，用户将无法判断发出的请求是否成功。
## 5. 参数过滤
如果记录数量很多，服务器不可能都将它们返回给用户。API应该提供参数，过滤返回结果。

下面是一些常见的参数。

    ?limit=10：指定返回记录的数量
    ?offset=10：指定返回记录的开始位置。
    ?page=2&per_page=100：指定第几页，以及每页的记录数。
    ?sortby=name&order=asc：指定返回结果按照哪个属性排序，以及排序顺序。
    ?animal_type_id=1：指定筛选条件
参数的设计允许存在冗余，即允许API路径和URL参数偶尔有重复。比如，GET /zoo/ID/animals 与 GET /animals?zoo_id=ID 的含义是相同的。

## 6. 数据格式

|文件拓展|Content-Type|描述|
|-|-|-|
|*.json|application/json|JSON数据
|*.xml|application/xml|可扩展标记语言
|*.pdf|application/pdf|PDF文档
|*.html|text/html|超文本文档
|*.htm|text/html|超文本文档
|*.css|text/css|层叠样式表
|*.csv|text/csv|逗号分开值
|*.jpg|image/jpeg|jpg图片
|*.png|image/png|png图片
|*.gif|image/gif|gif图片
|*.ico|image/x-icon|图标
|*.js|application/x-javascript|javascript脚本
|*.xls|application/vnd.ms-excel|MS-Excel文件
|form|application/x-www-form-urlencoded|表单数据
|form|multipart/form-data|表单数据(二进制)

## 7. 错误处理
### 如果状态码是4xx，就应该向用户返回出错信息。一般来说，返回的信息中将error作为键名，出错信息作为键值即可。
``` json
{
    error: "Invalid API key"
}
```

## 8. 返回结果
 针对不同操作，服务器向用户返回的结果应该符合以下规范。

    GET /collection：返回资源对象的列表(数组)
    GET /collection/resource：返回单个资源对象
    POST /collection：返回新生成的资源对象
    PUT /collection/resource：返回完整的资源对象
    PATCH /collection/resource：返回完整的资源对象
    DELETE /collection/resource：返回一个空文档

## 9. 请求流程
                    开始
                     ↓
    +-----+    +------------+
    |  E  |    | 1.Request  |
    |  R  |    +------------+
    |  R  |          ↓
    |  O  |    +------------+
    |  R  |    | 2.Router   |
    |     |    +------------+
    |  H  |          ↓
    |  A  |    +------------+      +--------------+      +---------+
    |  N  |    | 3.View     |  ←→  | 4.Controller |  ←→  | 5.Model |
    |  D  |    +------------+      +--------------+      +---------+
    |  L  |          ↓
    |  E  |    +------------+
    |  R  |    | 6.Response |
    +-----+    +------------+
                     ↓
                    结束
    流程顺序：
        Request -> Router -> View -> Controller -> Model -> Controller -> View -> Response

> Error Handler: 错误处理

> Request: 用户请求 包含 URL,Method,Filter,Data

> Router: 路由分发 根据URL进行初级任务分发

> View: 视图层 初步验证参数、调用Controller执行任务,返回结果

> Controller: 控制器 调用Model层并将结果反馈给View

> Model: 模型 资源操作，如数据库，打印机，Socket IO等

> Response: 响应数据 封装view提供的数据(根据请求的 Accept数据格式 对结果转码输出)
