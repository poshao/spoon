<?php
namespace App\Auth\Controller;
use \App\Auth\Model\Users as mUsers;
use \App\Auth\View\Users as vUsers;
use \Spoon\Response;
use \Spoon\Exception;

class Users extends \Spoon\Controller{

    /**
     * 任务分配
     *
     * @return void
     */
    public function doMain(){
        switch(\strtolower($_SERVER['REQUEST_METHOD'])){
            case 'get'://查询部分或完整用户信息
                if($this->view()->paramsCount()==0){
                    $this->listUsers();
                }else{
                    $this->getInfo();
                }
                break;
            case 'post'://create user
                $this->createUser();
                break;
            case 'put'://更新用户信息

                break;
            case 'patch'://更新部分用户信息
                $this->updateUser();
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
        $password=\Spoon\Encrypt::hashPassword($this->get('password'),\Spoon\Config::getByApps('auth')['salt']);
        $userid=$this->model()->create($this->get('workid'),$password);
        if($userid===false){
            throw new Exception('user already register',400);
        }

        //响应客户端
        $this->view()->sendJSON(array('userid'=>$userid));
    }

    /**
     * 更新用户
     * @apiName Update
     * @api {POST} /auth/v1/users Update
     * @apiDescription 更新用户信息
     * @apiGroup User
     * @apiVersion 0.1.0
     * 
     * @apiParam {string} workid 工号
     * @apiParam {object} info 用户信息
     * @apiParam {string} [info.username] 用户名字
     * @apiParam {string} [info.depart] 部门
     * @apiParamExample  {json} 请求示例:
     * {
     *      "workid":"8020507",
     *      "info":{
     *          "username":"Byron Gong",
     *          "depart":"LOG",
     *      }
     * }
     * 
     * @apiSuccess {int} userid 用户编码
     * @apiSuccessExample {json} 成功响应:
     * {
     *      "userid":1
     * }
     * @apiSampleRequest /auth/v1/users
     * @apiPermission user_update
     */
    private function updateUser(){
        $this->view()->checkParams(array('workid','info'));
        $userid=$this->model()->update($this->get('workid'),$this->get('info'));
        if(empty($userid)){
            throw new Exception('update user info failed',422);
        }
        $this->view()->sendJSON(array('userid'=>$userid));
    }

    /**
     * 获取用户信息
     * @apiName GetInfo
     * @api {GET} /auth/v1/users GetInfo
     * @apiDescription 获取用户信息
     * @apiGroup User
     * @apiVersion 0.1.0
     * 
     * @apiParam {string} workid 工号
     * @apiParam {string} [field] 用户信息(username,depart) 未设置时获取所有信息
     * @apiParamExample  {json} 请求示例:
     * {
     *      "workid":"8020507",
     *      "fields":'username'
     * }
     * 
     * @apiSuccess {string} userinfo 用户信息
     * @apiSuccess {string} [userinfo.username] 用户名
     * @apiSuccess {string} [userinfo.depart] 部门
     * 
     * @apiSuccessExample {json} 成功响应:
     * {
     *      "userinfo":{
     *          "username":"Byron Gong"
     *      }
     * }
     * @apiSampleRequest /auth/v1/users
     * @apiPermission user_getinfo
     */
    private function getInfo(){
        $this->view()->checkParams(array('workid'),array('infofields'));
        $user=$this->model()->getUser($this->get('workid'),$this->get('infofields'));
        $this->view()->sendJSON(array('userinfo'=>$user));
    }

    /**
     * 获取用户清单
     * @apiName List
     * @api {GET} /auth/v1/users List
     * @apiDescription 获取用户清单
     * @apiGroup User
     * @apiVersion 0.1.0
     * 
     * @apiSuccess {object} users 用户清单
     * @apiSuccessExample {json} 成功响应:
     * {
     *      "users":{
     *          "1":{
     *              "id":"1",
     *              "workid":"8020507"
     *          }
     *      }
     * }
     * @apiSampleRequest /auth/v1/users
     * @apiPermission user_list
     */
    private function listUsers(){
        $this->view()->sendJSON(array('users'=>$this->model()->listUsers()));
    }
}

?>