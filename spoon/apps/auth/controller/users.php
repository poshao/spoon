<?php
namespace App\Auth\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Users extends \Spoon\Controller
{
    /**
     * 任务分配
     *
     * @return void
     */
    public function doMain()
    {
        switch (\strtolower($_SERVER['REQUEST_METHOD'])) {
            case 'get'://查询部分或完整用户信息
                if ($this->view()->paramsCount()==0) {
                    $this->listUsers();
                } else {
                    $this->getInfo();
                }
                break;
            case 'post'://create user
                $this->createUser();
                break;
            case 'put'://关联用户
                if (!empty($this->get('rolename'))) {
                    $this->assignRole();
                } elseif (!empty($this->get('groupname'))) {
                    $this->assignGroup();
                }
                break;
            case 'patch'://更新部分用户信息
                $this->updateUser();
                break;
            case 'delete'://删除用户
                if (!empty($this->get('rolename'))) {
                    $this->unassignRole();
                } elseif (!empty($this->get('groupname'))) {
                    $this->unassignGroup();
                }
                break;
        }
        // Response::sendJSON(array('user'=>__CLASS__));
    }

    /**
     * 创建用户
     * @apiName CreateUser
     * @api {POST} /auth/v1/users CreateUser
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
     * @apiPermission app_auth_user_register
     */
    private function createUser()
    {
        //检查登录状态及权限
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_user_register');
        }
        
        //检查参数
        $this->view()->checkParams(array('workid','password'));

        //检查数据库
        $password=\Spoon\Encrypt::hashPassword($this->get('password'), \Spoon\Config::getByApps('auth')['salt']);
        $userid=$this->model()->create($this->get('workid'), $password);
        if ($userid===false) {
            throw new Exception('user already register', 400);
        }

        //响应客户端
        $this->view()->sendJSON(array('userid'=>$userid));
    }

    /**
     * 更新用户
     * @apiName UpdateUser
     * @api {PATCH} /auth/v1/users UpdateUser
     * @apiDescription 更新用户信息
     * @apiGroup User
     * @apiVersion 0.1.0
     *
     * @apiParam {string} workid 工号（通过认证信息获取）
     * @apiParam {object} info 用户信息
     * @apiParam {string} [info.username] 用户名字
     * @apiParam {string} [info.depart] 部门
     * @apiParam {string} [info.avator] 头像文件名
     * @apiParam {string} [info.password] 密码
     * @apiParam {string} [info.password.oldpassword] 旧密码
     * @apiParam {string} [info.password.newpassword] 新密码
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
     * @apiPermission app_auth_user_update
     */
    private function updateUser()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_user_update');
        }
        $this->view()->checkParams(array('info'));
        // $this->view()->checkParams(array('workid','info'));
        $workid=$verify->getWorkid();
        $info=$this->get('info');

        //检查密码修改
        if (isset($info['password'])) {
            $oldPassword=\Spoon\Encrypt::hashPassword($info['password']['oldpassword'], \Spoon\Config::getByApps('auth')['salt']);
            $newPassword=\Spoon\Encrypt::hashPassword($info['password']['newpassword'], \Spoon\Config::getByApps('auth')['salt']);
            if ($this->model()->changePassword($workid, $oldPassword, $newPassword)===false) {
                throw new Exception('change password failed', 422);
            }
        }
        unset($info['password']);
        $userid=$this->model()->update($workid, $info);
        if ($userid===false) {
            throw new Exception('update user info failed', 422);
        }
        $this->view()->sendJSON(array('userid'=>$userid));
    }

    /**
     * 获取用户信息
     * @apiName GetUserinfo
     * @api {GET} /auth/v1/users GetUserinfo
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
     * @apiPermission app_auth_user_getinfo
     */
    private function getInfo()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_user_getinfo');
        }

        $this->view()->checkParams(array('workid'), array('infofields'));
        $user=$this->model()->getUser($this->get('workid'), $this->get('infofields'));
        $this->view()->sendJSON(array('userinfo'=>$user));
    }

    /**
     * 获取用户清单
     * @apiName ListUsers
     * @api {GET} /auth/v1/users ListUsers
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
     * @apiPermission app_auth_user_list
     */
    private function listUsers()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_user_list');
        }

        $this->view()->sendJSON(array('users'=>$this->model()->listUsers()));
    }

    /**
     * 设定用户分组
     * @apiName AssignUserGroup
     * @api {PUT} /auth/v1/users AssignUserGroup
     * @apiDescription 设定用户分组
     * @apiGroup User
     * @apiVersion 0.1.0
     *
     * @apiParam {string} workid 工号
     * @apiParam {string} groupname 分组名称
     *
     * @apiSuccess {object} result 结果
     * @apiSuccessExample {json} 成功响应:
     * {
     *      "result":true
     * }
     * @apiSampleRequest /auth/v1/users
     * @apiPermission app_auth_user_assign_group
     */
    private function assignGroup()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_user_assign_group');
        }

        $this->checkParams(array('workid','groupname'));
        $workid=$this->get('workid');
        $groupname=$this->get('groupname');

        $rlst=$this->model()->assignGroup($workid, $groupname);
        if ($rlst===false) {
            throw new Exception('assign group failed', 400);
        }
        $this->view()->sengJSON(array('result'=>true));
    }

    /**
     * 取消设定用户分组
     * @apiName UnassignUserGroup
     * @api {DELETE} /auth/v1/users UnassignUserGroup
     * @apiDescription 取消设定用户分组
     * @apiGroup User
     * @apiVersion 0.1.0
     *
     * @apiParam {string} workid 工号
     * @apiParam {string} groupname 分组名称
     *
     * @apiSuccess {object} result 结果
     * @apiSuccessExample {json} 成功响应:
     * {
     *      "result":true
     * }
     * @apiSampleRequest /auth/v1/users
     * @apiPermission app_auth_user_assign_group
     */
    private function unassignGroup()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_user_assign_group');
        }

        $this->checkParams(array('workid','groupname'));
        $workid=$this->get('workid');
        $groupname=$this->get('groupname');

        $rlst=$this->model()->unassignGroup($workid, $groupname);
        if ($rlst===false) {
            throw new Exception('unassign group failed', 400);
        }
        $this->view()->sengJSON(array('result'=>true));
    }

    /**
     * 设定用户角色
     * @apiName AssignUserRole
     * @api {PUT} /auth/v1/users AssignUserRole
     * @apiDescription 设定用户分组
     * @apiGroup User
     * @apiVersion 0.1.0
     *
     * @apiParam {string} workid 工号
     * @apiParam {string} rolename 角色名称
     *
     * @apiSuccess {object} result 结果
     * @apiSuccessExample {json} 成功响应:
     * {
     *      "result":true
     * }
     * @apiSampleRequest /auth/v1/users
     * @apiPermission app_auth_user_assign_group
     */
    private function assignRole()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_user_assign_role');
        }

        $this->checkParams(array('workid','rolename'));
        $workid=$this->get('workid');
        $rolename=$this->get('rolename');

        $rlst=$this->model()->assignRole($workid, $rolename);
        if ($rlst===false) {
            throw new Exception('assign role failed', 400);
        }
        $this->view()->sengJSON(array('result'=>true));
    }

    /**
     * 取消设定用户角色
     * @apiName UnassignUserRole
     * @api {DELETE} /auth/v1/users UnassignUserRole
     * @apiDescription 取消设定用户角色
     * @apiGroup User
     * @apiVersion 0.1.0
     *
     * @apiParam {string} workid 工号
     * @apiParam {string} rolename 角色名称
     *
     * @apiSuccess {object} result 结果
     * @apiSuccessExample {json} 成功响应:
     * {
     *      "result":true
     * }
     * @apiSampleRequest /auth/v1/users
     * @apiPermission app_auth_user_assign_group
     */
    private function unassignRole()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_user_assign_role');
        }

        $this->checkParams(array('workid','rolename'));
        $workid=$this->get('workid');
        $rolename=$this->get('rolename');

        $rlst=$this->model()->unassignRole($workid, $rolename);
        if ($rlst===false) {
            throw new Exception('assign role failed', 400);
        }
        $this->view()->sengJSON(array('result'=>true));
    }
}
