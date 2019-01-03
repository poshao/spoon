<?php
namespace App\Auth\Controller;
use \Spoon\Response;
use \Spoon\Exception;

class Tokens extends \Spoon\Controller{
    /**
     * 任务分配
     *
     * @return void
     */
    public function doMain(){
        switch(\strtolower($_SERVER['REQUEST_METHOD'])){
            case 'get'://
                
                break;
            case 'post'://login
                $this->login();
                break;
            case 'put'://

                break;
            case 'patch'://
                
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
     * @apiSuccess {string} token 令牌
     * @apiSuccessExample {json} 成功响应:
     * {
     *      "token":"ZTlmYjQyNWFlYjA4MWI1NzYzZTU0MzBkNGNmZWJkNWU="
     * }
     * @apiSampleRequest /auth/v1/tokens
     * @apiPermission app_auth_user_login
     */
    private function login(){
        $this->view()->checkParams(array('workid','password'));

        $workid=$this->get('workid');
        $salt=\Spoon\Config::getByApps('auth')['salt'];
        $password=\Spoon\Encrypt::hashPassword($this->get('password'),$salt);
        $token=$this->model()->login($workid,$password);
        if($token===false){
            throw new Exception('login failed',400);
        }
        $this->view()->sendJSON(array('token'=>$token));
    }

    /**
     * 用户注销
     * @apiName Logout
     * @api {DELETE} /auth/v1/tokens Logout
     * @apiDescription 用户注销
     * @apiGroup User
     * @apiVersion 0.1.0
     * 
     * @apiParam {string} workid 工号
     * @apiParam {string} token 令牌
     * @apiParamExample  {json} 请求示例:
     * {
     *      "workid":"8020507",
     *      "token":"ZTlmYjQyNWFlYjA4MWI1NzYzZTU0MzBkNGNmZWJkNWU="
     * }
     * 
     * @apiSuccessExample {json} 成功响应:
     * []
     * @apiSampleRequest /auth/v1/tokens
     * @apiPermission app_auth_user_logout
     */
    private function logout(){
        $this->view()->checkParams(array('workid','token'));
        if(!$this->model()->logout($this->get('workid'),$this->get('token'))){
            throw new Exception('maybe workid,token,ip not match',404);
        }
        $this->view()->sendJSON(array());
    }
}
?>