<?php
namespace App\Auth\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Permissions extends \Spoon\Controller
{
    /**
     * 任务分配
     *
     * @return void
     */
    public function doMain()
    {
        switch (\strtolower($_SERVER['REQUEST_METHOD'])) {
            case 'get'://查询
                if ($this->view()->paramsCount()==0) {
                    $this->listPermissions();
                } elseif (!empty($this->get('rolename'))) {
                    $this->listPermissionsByRole();
                }
                break;
            case 'post'://新建权限
                $this->createPermission();
                break;
            case 'put'://

                break;
            case 'patch'://更新权限
                $this->updatePermission();
                break;
            case 'delete':

                break;
        }
        // Response::sendJSON(array('user'=>__CLASS__));
    }

    /**
     * 新建权限
     * @apiName CreatePermission
     * @api {POST} /auth/v1/permissions CreatePermission
     * @apiDescription 新建权限
     * @apiGroup Auth.Permission
     * @apiVersion 0.1.0
     *
     * @apiParam {string} permission 权限名称
     * @apiParam {string} description 权限描述
     *
     * @apiSuccess {string} permissionid 权限ID
     *
     * @apiSampleRequest /auth/v1/permissions
     * @apiPermission app_auth_permission_create
     */
    private function createPermission()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_permission_create');
        }
        $this->view()->checkParams(array('permission','description'));
        
        $permissionname=$this->get('permission');
        $description=$this->get('description');
        $permissionid=$this->model()->create($permissionname, $description);
        if ($permissionid===false) {
            throw new Exception('permission already exists', 400);
        }
        $this->view()->sendJSON(array('permissionid'=>$permissionid));
    }

    /**
     * 枚举所有权限
     * @apiName ListPermissions
     * @api {GET} /auth/v1/permissions ListPermissions
     * @apiDescription 枚举所有权限
     * @apiGroup Auth.Permission
     * @apiVersion 0.1.0
     *
     * @apiSuccess {object} permissions 权限列表
     *
     * @apiSampleRequest /auth/v1/permissions
     * @apiPermission app_auth_permission_list
     */
    private function listPermissions()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_permission_list');
        }
        $rolelist=$this->model()->list();
        $this->view()->sendJSON(array('permissions'=>$rolelist));
    }

    /**
     * 根据角色枚举所有权限
     * @apiName ListPermissionsByRole
     * @api {GET} /auth/v1/permissions ListPermissionsByRole
     * @apiDescription 根据角色枚举所有权限
     * @apiGroup Auth.Permission
     * @apiVersion 0.1.0
     *
     * @apiParam {string} rolename 角色名称
     *
     * @apiSuccess {object} permissions 权限列表
     *
     * @apiSampleRequest /auth/v1/permissions
     * @apiPermission app_auth_permission_list
     */
    private function listPermissionsByRole()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_permission_list');
        }
        $this->view()->checkParams(array('rolename'));

        $rolelist=$this->model()->listPermissionsByRole($this->get('rolename'));
        $this->view()->sendJSON(array('permissions'=>$rolelist));
    }

    /**
     * 更新权限描述
     * @apiName UpdatePermission
     * @api {PATCH} /auth/v1/permissions UpdatePermission
     * @apiDescription 更新权限描述
     * @apiGroup Auth.Permission
     * @apiVersion 0.1.0
     *
     * @apiParam {integer} permissionid 权限ID
     * @apiParam {object} info 权限信息
     * @apiParam {string} [info.permissionname] 权限名称
     * @apiParam {string} [info.description] 权限描述
     *
     * @apiSuccess {object} permissionid 权限ID
     *
     * @apiSampleRequest /auth/v1/permissions
     * @apiPermission app_auth_permission_update
     */
    private function updatePermission()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_permission_update');
        }

        $this->view()->checkParams(array('permissionid','info'));
        $id=$this->get('permissionid');
        $info=$this->get('info');

        $permissionid=$this->model()->update($id, $info);
        if ($permissionid===false) {
            throw new Exception('update permission info failed', 422);
        }
        $this->view()->sendJSON(array('permissionid'=>$permissionid));
    }
}
