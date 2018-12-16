<?php
namespace App\Auth\Controller;
use \App\Auth\Model\Users as mUsers;
use \App\Auth\View\Users as vUsers;
use \Spoon\Response;

class Users extends \Spoon\Controller{

    /**
     * 任务分配
     *
     * @return void
     */
    public function doMain(){
        switch(\strtolower($_SERVER['REQUEST_METHOD'])){
            case 'post'://create user
                $this->createUser();
            break;
        }
        Response::sendJSON(array('user'=>__CLASS__));
    }

    private function createUser(){
        //检查参数
        $this->view()->checkParams(array('workid','username','password'));
        //检查数据库

        //写入数据库

        //响应客户端
    }
}

?>