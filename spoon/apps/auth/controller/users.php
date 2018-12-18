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
            case 'get'://查询部分或完整用户信息

                break;
            case 'post'://create user
                $this->createUser();
                break;
            case 'put'://更新用户信息

                break;
            case 'patch'://更新部分用户信息

                break;
            case 'delete'://删除用户

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
     * @apiSuccessExample {json} 成功响应:
     * {
     *      "userid":1
     * }
     * @apiSampleRequest /auth/v1/users
     * @apiPermission user_register
     */
    private function createUser(){
        //检查参数
        $this->view()->checkParams(array('workid','password'));

        //检查数据库
        $userid=$this->model()->create($this->get('workid'),$this->get('password'));
        if($userid===false){
            throw new \Spoon\Exception('user already register',400);
        }

        //响应客户端
        $this->view()->sendJSON(array('userid'=>$id));
    }
}

?>