<?php
/**
 * 路由分配执行
 */
namespace Spoon;
class Router{

    public static function assign(){
        $url=\strtolower(\ltrim($_SERVER['PATH_INFO'],'/'));
        $url_list=explode($url,'/');

        //版本号
        $version=array_shift($url_list);

        //映射调用类

        $ctl=new Controller();
        $ctl->view()->getParams();
        $ctl->process();
        return $ctl->view()->response();
    }
}
?>