<?php
namespace App\Auth\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Tokens extends \Spoon\Controller
{
    /**
     * 任务分配
     *
     * @return void
     */
    public function doMain()
    {
        switch (\strtolower($_SERVER['REQUEST_METHOD'])) {
            case 'get'://
                $this->getSocketId();
                break;
            case 'post'://login
                $this->login();
                break;
            case 'put'://

                break;
            case 'patch'://
                $this->updateSocketId();
                break;
            case 'delete'://
                $this->logout();
                break;
        }
    }


    /**
     * 用户登录
     * @apiName Login
     * @api {POST} /auth/v1/tokens Login
     * @apiDescription 用户登录
     * @apiGroup Auth.Token
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
     * @apiSuccess {string} token 令牌
     * @apiSuccessExample {json} 成功响应:
     * {
     *      "token":"ZTlmYjQyNWFlYjA4MWI1NzYzZTU0MzBkNGNmZWJkNWU="
     * }
     * @apiSampleRequest /auth/v1/tokens
     * @apiPermission app_auth_user_login
     */
    private function login()
    {
        $this->view()->checkParams(array('workid','password'));

        $workid=$this->get('workid');
        $salt=\Spoon\Config::getByApps('auth')['salt'];
        $password=\Spoon\Encrypt::hashPassword($this->get('password'), $salt);
        $token=$this->model()->login($workid, $password);
        if ($token===false) {
            throw new Exception('login failed', 401);
        }
        $this->view()->sendJSON(array('token'=>$token));
    }

    /**
     * 用户注销
     * @apiName Logout
     * @api {DELETE} /auth/v1/tokens Logout
     * @apiDescription 用户注销
     * @apiGroup Auth.Token
     * @apiVersion 0.1.0
     *
     * @apiParam {string} workid 工号(通过认证获取)
     *
     * @apiSuccessExample {json} 成功响应:
     * []
     * @apiSampleRequest /auth/v1/tokens
     * @apiPermission app_auth_user_logout
     */
    private function logout()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_user_logout');
        }

        $workid=$verify->getWorkid();

        // $this->view()->checkParams(array('workid','token'));
        if (!$this->model()->logout($workid)) {
            throw new Exception('are you logined?', 404);
        }
        $this->view()->sendJSON(array());
    }

    /**
     * 更新SocketId
     * @apiName UpdateSocketID
     * @api {PATCH} /auth/v1/tokens UpdateSocketID
     * @apiDescription 更新SocketId
     * @apiGroup Auth.Token
     * @apiVersion 0.1.0
     *
     * @apiParam {string} workid 工号(通过认证获取)
     * @apiParam {string} socketid
     *
     * @apiSuccess {string} socketid
     *
     * @apiSuccessExample {json} 成功响应:
     * {
     *  "socketid":"***socket***"
     * }
     * @apiSampleRequest /auth/v1/tokens
     * @apiPermission app_auth_user_login
     */
    private function updateSocketId()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_user_login');
        }
        $this->view()->CheckParams(array('socketid'));

        $workid=$verify->getWorkid();
        $socketid=$this->get('socketid');

        $result=$this->model()->updateSocketId($workid, $socketid);

        if (!$result) {
            throw new Exception('update socketid failed', 500);
        }

        $this->view()->sendJSON(array('socketid'=>$socketid));
    }

    /**
     * 获取SocketId
     * @apiName GetSocketId
     * @api {GET} /auth/v1/tokens GetSocketId
     * @apiDescription 获取SocketId
     * @apiGroup Auth.Token
     * @apiVersion 0.1.0
     *
     * @apiParam {string} workid 工号
     *
     * @apiSuccess {string} list
     *
     * @apiSuccessExample {json} 成功响应:
     * {
     *  "socketid":"***socket***"
     * }
     * @apiSampleRequest /auth/v1/tokens
     * @apiPermission app_auth_user_login
     */
    private function getSocketId()
    {
        // $verify=\Spoon\DI::getDI('verify');
        // if (!empty($verify)) {
        //     $verify->CheckPermission('app_auth_user_login');
        // }

        $this->view()->CheckParams(array('workid'));

        $workid=$this->get('workid');
        $socktetIdlist=$this->model()->getSocketId($workid);
        if ($socktetIdlist===false) {
            throw new Exception('user\'s socketid not found', 404);
        }
        $this->view()->sendJSON(array('list'=>$socktetIdlist));
    }
}
