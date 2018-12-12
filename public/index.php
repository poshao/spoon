<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);

//处理跨域访问
// header('Access-Control-Allow-Origin:*');
// header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE');

//常量设置
define('SitePath',realpath(__DIR__.'/../spoon'));//网站目录
define('CorePath',realpath(SitePath.'/core'));//核心类目录

//设置autoload
require SitePath.'/autoload.php';
//核心类
\Spoon\Autoload::addNamespace('\\Spoon',CorePath);
//拓展类
\Spoon\Autoload::addNamespace('\\Spoon\\Extension',realpath(SitePath.'/extensions'));
//应用类
\Spoon\Autoload::addNamespace('\\App',realpath(SitePath.'/apps'));

//加载配置
\Spoon\Config::load();

//设置时区
\date_default_timezone_set(\Spoon\Config::get('timezone','Asia/Shanghai'));

//路由分配并响应结果
\Spoon\Router::assign()->send();

// // 发送响应
// \Spoon\Response::send();

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

    $method=$_SERVER['REQUEST_METHOD'];
    $url=$_SERVER['PATH_INFO'];
    $protocol=$_SERVER['SERVER_PROTOCOL'];
    $request_contenttype=$_SERVER['CONTENT_TYPE'];
    $accept_contenttype=$_SERVER['HTTP_ACCEPT'];// */*任意格式
    $payload=file_get_contents('php://input');//请求原始数据
    $query=$_SERVER['QUERY_STRING'];

    // var_dump($method,$url,$protocol,$request_contenttype,$accept_contenttype,$payload,$query,$_REQUEST);
    // header("HTTP/1.1 401 Unauthozied");
    // header('WWW-Authenticate:Basic');
?>