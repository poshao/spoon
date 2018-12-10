<?php
/**
 * 控制器类
 */
namespace Spoon;
class Controller{
    protected $_view=null;
    protected $_model=null;

    public function view(){
        return $this->view;
    }

    public function model(){
        return $this->model;
    }

    public function process(){
        
    }
}
?>