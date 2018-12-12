<?php
/**
 * 控制器基类
 */
namespace Spoon;
abstract class Controller{
    protected $_view=null;
    protected $_model=null;

    public function view(){
        return $this->view;
    }

    public function model(){
        return $this->model;
    }

    public abstract function process();
}
?>