<?php
/**
 * 控制器基类
 */
namespace Spoon;
abstract class Controller{
    protected $_view=null;
    protected $_model=null;

    public function __construct(){
        //初始化同时初始化视图类和模型类
        $controlClass=\get_called_class();
        $viewClass=\str_replace('Controller','View',$controlClass);
        $this->view=new $viewClass;
        $modelClass=\str_replace('Controller','Model',$controlClass);
        $this->model=new $modelClass;
    }

    public function view(){
        return $this->view;
    }

    public function model(){
        return $this->model;
    }

    /**
     * 获取参数值(同View->get)
     *
     * @param string $key 参数名
     * @return mixed
     */
    public function get($key){
        return $this->view()->get($key);
    }

    /**
     * 分配函数
     *
     * @return void
     */
    public abstract function doMain();
}
?>