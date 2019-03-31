<?php
namespace App\Auth\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Roles extends \Spoon\Controller
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
                    $this->listRoles();
                } elseif (!empty($this->get('workid'))) {
                    $this->listRolesByUser();
                } elseif (!empty($this->get('permissionname'))) {
                    $this->listRolesByPermission();
                }
                break;
            case 'post'://新建角色
                $this->createRole();
                break;
            case 'put'://
                $this->assignPermission();
                break;
            case 'patch'://更新角色
                $this->updateRole();
                break;
            case 'delete':
                $this->unassignPermission();
                break;
        }
        // Response::sendJSON(array('user'=>__CLASS__));
    }

    /**
     * 新建角色
     * @apiName CreateRole
     * @api {POST} /auth/v1/roles CreateRole
     * @apiDescription 新建角色
     * @apiGroup Auth.Role
     * @apiVersion 0.1.0
     *
     * @apiParam {string} rolename 角色名称
     * @apiParam {string} description 角色描述
     *
     * @apiSuccess {string} roleid 角色ID
     *
     * @apiSampleRequest /auth/v1/roles
     * @apiPermission app_auth_role_create
     */
    private function createRole()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_role_create');
        }
        $this->view()->checkParams(array('rolename','description'));
        
        $rolename=$this->get('rolename');
        $description=$this->get('description');
        $roleid=$this->model()->create($rolename, $description);
        if ($roleid===false) {
            throw new Exception('role already exists', 400);
        }

        $this->view()->sendJSON(array('roleid'=>$roleid));
    }

    /**
     * 枚举所有角色
     * @apiName ListRoles
     * @api {GET} /auth/v1/roles ListRoles
     * @apiDescription 枚举所有角色
     * @apiGroup Auth.Role
     * @apiVersion 0.1.0
     *
     * @apiSuccess {object} roles 角色列表
     *
     * @apiSampleRequest /auth/v1/roles
     * @apiPermission app_auth_role_list
     */
    private function listRoles()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_role_list');
        }
        $rolelist=$this->model()->list();
        $this->view()->sendJSON(array('roles'=>$rolelist));
    }

    /**
     * 枚举用户所有角色
     * @apiName ListRolesByUser
     * @api {GET} /auth/v1/roles ListRolesByUser
     * @apiDescription 枚举用户所有角色
     * @apiGroup Auth.Role
     * @apiVersion 0.1.0
     *
     * @apiParam {string} workid 工号
     *
     * @apiSuccess {object} roles 角色列表
     *
     * @apiSampleRequest /auth/v1/roles
     * @apiPermission app_auth_role_list
     */
    private function listRolesByUser()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_role_list');
        }
        $this->view()->checkParams(array('workid'));
        $rolelist=$this->model()->listRolesByUser($this->get('workid'));
        $this->view()->sendJSON(array('roles'=>$rolelist));
    }

    /**
     * 枚举权限所有角色
     * @apiName ListRolesByPermission
     * @api {GET} /auth/v1/roles ListRolesByPermission
     * @apiDescription 枚举权限所有角色
     * @apiGroup Auth.Role
     * @apiVersion 0.1.0
     *
     * @apiParam {string} permissionname 权限名称
     *
     * @apiSuccess {object} roles 角色列表
     *
     * @apiSampleRequest /auth/v1/roles
     * @apiPermission app_auth_role_list
     */
    private function listRolesByPermission()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_role_list');
        }
        $this->view()->checkParams(array('permissionname'));
        $rolelist=$this->model()->listRolesByPermission($this->get('permissionname'));
        $this->view()->sendJSON(array('roles'=>$rolelist));
    }

    /**
     * 更新角色描述
     * @apiName UpdateRole
     * @api {PATCH} /auth/v1/roles UpdateRole
     * @apiDescription 更新角色描述
     * @apiGroup Auth.Role
     * @apiVersion 0.1.0
     *
     * @apiParam {integer} roleid 角色ID
     * @apiParam {object} info 角色信息
     * @apiParam {string} [info.rolename] 角色名称
     * @apiParam {string} [info.description] 角色描述
     *
     * @apiSuccess {object} roleid 角色ID
     *
     * @apiSampleRequest /auth/v1/roles
     * @apiPermission app_auth_role_update
     */
    private function updateRole()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_role_update');
        }

        $this->view()->checkParams(array('roleid','info'));
        $id=$this->get('roleid');
        $info=$this->get('info');

        $roleid=$this->model()->update($id, $info);
        if ($roleid===false) {
            throw new Exception('update role info failed', 422);
        }
        $this->view()->sendJSON(array('roleid'=>$roleid));
    }

    /**
     * 关联角色权限
     * @apiName AssignPermission
     * @api {PUT} /auth/v1/roles AssignPermission
     * @apiDescription 关联角色权限
     * @apiGroup Auth.Role
     * @apiVersion 0.1.0
     *
     * @apiParam {string} rolename 角色名称
     * @apiParam {string} permissionname 权限名称
     *
     * @apiSuccess {object} result 操作结果
     *
     * @apiSampleRequest /auth/v1/roles
     * @apiPermission app_auth_assign_permission
     */
    private function assignPermission()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_assign_permission');
        }
        $this->view()->checkParams(array('rolename','permissionname'));
        $rolename=$this->get('rolename');
        $permissionname=$this->get('permissionname');
        $rlst=$this->model()->assignPermission($rolename, $permissionname);
        if ($rlst===false) {
            throw new Exception('assign permission failed', 400);
        }
        $this->view()->sendJSON(array('result'=>true));
    }

    /**
     * 取消关联角色权限
     * @apiName UnassignPermission
     * @api {DELETE} /auth/v1/roles UnassignPermission
     * @apiDescription 取消关联角色权限
     * @apiGroup Auth.Role
     * @apiVersion 0.1.0
     *
     * @apiParam {string} rolename 角色名称
     * @apiParam {string} permissionname 权限名称
     *
     * @apiSuccess {object} result 操作结果
     *
     * @apiSampleRequest /auth/v1/roles
     * @apiPermission app_auth_assign_permission
     */
    private function unassignPermission()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_assign_permission');
        }
        $this->view()->checkParams(array('rolename','permissionname'));
        $rolename=$this->get('rolename');
        $permissionname=$this->get('permissionname');
        $rlst=$this->model()->unassignPermission($rolename, $permissionname);
        if ($rlst===false) {
            throw new Exception('unassign permission failed', 400);
        }
        $this->view()->sendJSON(array('result'=>true));
    }
}
