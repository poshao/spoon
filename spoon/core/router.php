<?php
/**
 * 路由分配执行
 */
namespace Spoon;
class Router{

    public static function process(){

        if(!isset($_SERVER['PATH_INFO'])) return Response::sendError(404,'invalid request');

        //解析路径
        $url=\strtolower(\ltrim($_SERVER['PATH_INFO'],'/'));
        $url_list=\explode('/',$url);

        //应用名称
        $app=array_shift($url_list);
        //版本号
        $version=array_shift($url_list);
        //资源号(0)
        $res=array_shift($url_list);

        //检查版本号
        if($version!='v1') return Response::sendError(404,'invalid version('.$version.')');
        //检查应用
        // if(!\file_exists(AppPath.'/'.$app)) return Response::sendError(404,'invalid request app('.$app.')');
        
        $class='App\\'.$app.'\\Controller\\'.$res;
        if(!\class_exists($class)) return Response::sendError(404,'invalid request');
        $p=new $class();
        $p->doMain();
        // \var_dump($app,$version,$res);

        //映射调用类
        // $ctl=new Controller();
        // $ctl->view()->getParams();
        // $ctl->process();
        // return $ctl->view()->response();
    }
}
?>