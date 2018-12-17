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
        // Response::sendJSON(array('user'=>__CLASS__));
    }

    /**
     * 创建用户
     * @apiName Register
     * @api {POST} /auth/v1/users Register
     * @apiDescription 创建用户
     * @apiGroup User
     * @apiVersion 0.1.0
     * 
     * @apiParam {string} workid 工号
     * @apiParam {string} password 密码
     * @apiParamExample  {json} 请求示例:
     * {
     *      "workid":"8020507",
     *      "password":"123456"
     * }
     * 
     * @apiSuccess {int} userid 用户编码
     * @apiSuccess {string} workid 工号
     * @apiSuccess {string} password 密码
     * @apiSuccessExample {json} 成功响应:
     * {
     *      "userid":1,
     *      "workid":"8020507",
     *      "password":"123456"
     * }
     * @apiSampleRequest /auth/v1/users
     * @apiPermission user_register
     */
    private function createUser(){
        //检查参数
        
        $this->view()->checkParams(array('workid','username','password'));
        //检查数据库
        
        $user=$this->model()->getUser();
        foreach($user as $k=>$v){
            echo $k.' : '.$v.'<br/>';
        }
        //写入数据库

        //响应客户端
        // Response::sendJSON(array(
        //     'workid'=>$this->view()->get('workid'),
        //     'password'=>$this->view()->get('password'),
        //     'username'=>$this->view()->get('username'),
        // ));
    }
}

?>