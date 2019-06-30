<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);

//处理跨域访问
header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept , X-Token,authorization");
// header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE, OPTION');
// if(strtoupper($_SERVER['REQUEST_METHOD'])== 'OPTIONS'){ 
//     exit;
// }

//常量设置
define('DS', DIRECTORY_SEPARATOR);//目录分隔符号
define('SitePath', realpath(__DIR__.'/../spoon'));//网站目录
define('CorePath', realpath(SitePath.'/core'));//核心类目录
define('AppPath', realpath(SitePath.'/apps'));//应用目录

//设置autoload
require SitePath.'/autoload.php';
//核心类
\Spoon\Autoload::addNamespace('\\Spoon', CorePath);
//拓展类
\Spoon\Autoload::addNamespace('\\Spoon\\Extensions', realpath(SitePath.'/extensions'));
//应用类
\Spoon\Autoload::addNamespace('\\App', realpath(SitePath.'/apps'));

//加载配置
\Spoon\Config::load();

//设置时区
\date_default_timezone_set(\Spoon\Config::get('timezone', 'Asia/Shanghai'));

// $_POST;
// $_GET;

//验证代码解析(Authorization)
//spoon + base64(id=123456&token=124==)
if (function_exists('apache_request_headers')) {
    //apache Server
    $headers=apache_request_headers();
    $auth=isset($headers['Authorization'])?$headers['Authorization']:null;
} else {
    //针对IIS的处理办法
    $auth=isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:null;
}
if ($auth!=null && \strpos($auth, 'spoon ')===0) {
    $params=\explode('&', base64_decode(substr($auth, 6)));
    $p=array();
    foreach ($params as $k=>$v) {
        $kv=\explode('=', $v, 2);
        $p[$kv[0]]=$kv[1];
    }
    $_POST['auth_workid']=$p['id'];
    $_POST['auth_token']=$p['token'];
}

//注入验证模块
\Spoon\DI::setDI('verify', new \App\Auth\Controller\Verify());

if (!\getenv('spoon_test_unit', true)) {
    //路由分配并响应结果
    try {
        \Spoon\Router::process();
    } catch (\Spoon\Exception $e) {
        $e->render();
    }
} else {
    //加载单元测试
    include __DIR__.'/../test/process.php';
}

// return;

// //路由分配
// $url=isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'';
// $url=strtolower(trim($url,'/'));

// $method=$_SERVER['REQUEST_METHOD'];
// $payload=file_get_contents('php://input');//请求原始数据

// \Spoon\Response::send();
// var_dump($_SERVER['PATH_INFO']);
// header('Access-Control-Allow-Origin:*');
// var_dump(headers_list());

//返回响应

    // $method=$_SERVER['REQUEST_METHOD'];
    // $url=$_SERVER['PATH_INFO'];
    // $protocol=$_SERVER['SERVER_PROTOCOL'];
    // $request_contenttype=$_SERVER['CONTENT_TYPE'];
    // $accept_contenttype=$_SERVER['HTTP_ACCEPT'];// */*任意格式
    // $payload=file_get_contents('php://input');//请求原始数据
    // $query=$_SERVER['QUERY_STRING'];

    // var_dump($method,$url,$protocol,$request_contenttype,$accept_contenttype,$payload,$query,$_REQUEST);
    // header("HTTP/1.1 401 Unauthozied");
    // header('WWW-Authenticate:Basic');
