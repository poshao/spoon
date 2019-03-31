<?php
namespace App\Vba\Controller;

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
            case 'get':
                $this->listUsers();
                break;
            case 'post':
                $this->register();
                break;
            case 'put':

                break;
            case 'patch':
                if (!empty($this->get('addinid'))) {
                    $this->assignAddin();
                } elseif (!empty($this->get('funid'))) {
                    $this->assignFun();
                }
                break;
            case 'delete':
                if (!empty($this->get('addinid'))) {
                    $this->unassignAddin();
                } elseif (!empty($this->get('funid'))) {
                    $this->unassignFun();
                }
                break;
        }
    }

    /**
     * 用户注册
     * @apiName Register
     * @api {POST} /vba/v1/users Register
     * @apiDescription 用户注册
     * @apiGroup VBA.User
     * @apiVersion 0.1.0
     *
     * @apiParam {string} loginname 登录名
     * @apiParam {string} username 用户名
     *
     * @apiSuccess {string} userid 用户ID
     * @apiSampleRequest /vba/v1/users
     */
    private function register()
    {
        $this->view()->checkParams(array('loginname','username'));
        $loginname=$this->get('loginname');
        $username=$this->get('username');
        $result=$this->model()->register($loginname, $username);
        if ($result===false) {
            throw new Exception('register failed', 500);
        } else {
            $this->view()->sendJson(array('result'=>'ok'));
        }
    }

    /**
     * 授权加载项
     * @apiName AssignAddin
     * @api {PATCH} /vba/v1/users AssignAddin
     * @apiDescription 授权加载项
     * @apiGroup VBA.User
     * @apiVersion 0.1.0
     *
     * @apiParam {string} loginname 用户登录账号
     * @apiParam {string} addinid 加载项ID
     *
     * @apiSuccess {string} result 结果
     * @apiSampleRequest /vba/v1/users
     * @apiPermission app_vba_user_assign_addin
     */
    private function assignAddin()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_vba_user_assign_addin');
        }

        $this->view()->checkParams(array('loginname','addinid'));

        $loginname=$this->get('loginname');
        $addinid=$this->get('addinid');
        
        $result=$this->model()->assignAddin($loginname, $addinid);
        $this->view()->sendJson(array('result'=>$result?'ok':'failed'));
    }

    /**
     * 取消授权加载项
     * @apiName UnassignAddin
     * @api {DELETE} /vba/v1/users UnassignAddin
     * @apiDescription 取消授权加载项
     * @apiGroup VBA.User
     * @apiVersion 0.1.0
     *
     * @apiParam {string} loginname 用户登录账号
     * @apiParam {string} addinid 加载项ID
     *
     * @apiSuccess {string} result 结果
     * @apiSampleRequest /vba/v1/users
     * @apiPermission app_vba_user_assign_addin
     */
    private function unassignAddin()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_vba_user_assign_addin');
        }

        $this->view()->checkParams(array('loginname','addinid'));

        $loginname=$this->get('loginname');
        $addinid=$this->get('addinid');
        
        $result=$this->model()->unassignAddin($loginname, $addinid);
        $this->view()->sendJson(array('result'=>$result?'ok':'failed'));
    }

    /**
     * 授权子功能项
     * @apiName AssignFun
     * @api {PATCH} /vba/v1/users AssignFun
     * @apiDescription 授权子功能项
     * @apiGroup VBA.User
     * @apiVersion 0.1.0
     *
     * @apiParam {string} loginname 用户登录账号
     * @apiParam {string} funid 子功能ID
     *
     * @apiSuccess {string} result 结果
     * @apiSampleRequest /vba/v1/users
     * @apiPermission app_vba_user_assign_fun
     */
    private function assignFun()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_vba_user_assign_fun');
        }
        $loginname=$this->get('loginname');
        $funid=$this->get('funid');

        $result=$this->model()->assignFun($loginname, $funid);
        $this->view()->sendJson(array('result'=>$result?'ok':'failed'));
    }

    /**
     * 取消授权子功能项
     * @apiName UnassignFun
     * @api {DELETE} /vba/v1/users UnassignFun
     * @apiDescription 取消授权子功能项
     * @apiGroup VBA.User
     * @apiVersion 0.1.0
     *
     * @apiParam {string} userid 用户ID
     * @apiParam {string} funid 子功能ID
     *
     * @apiSuccess {string} result 结果
     * @apiSampleRequest /vba/v1/users
     * @apiPermission app_vba_user_assign_fun
     */
    private function unassignFun()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_vba_user_assign_fun');
        }
        $loginname=$this->get('loginname');
        $funid=$this->get('funid');

        $result=$this->model()->unassignFun($loginname, $funid);
        $this->view()->sendJson(array('result'=>$result?'ok':'failed'));
    }

    /**
     * 枚举用户列表
     * @apiName ListUsers
     * @api {GET} /vba/v1/users ListUsers
     * @apiDescription 枚举用户列表
     * @apiGroup VBA.User
     * @apiVersion 0.1.0
     *
     * @apiSuccess {string} users 用户列表
     * @apiSampleRequest /vba/v1/users
     * @apiPermission app_vba_user_list
     */
    private function listUsers()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_vba_user_list');
        }
        $users=$this->model()->listUsers();
        $this->view()->sendJson(array('users'=>$users));
    }
}
