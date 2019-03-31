<?php
namespace App\Auth\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Groups extends \Spoon\Controller
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
                    $this->listGroups();
                } elseif (!empty($this->get('workid'))) {
                    $this->listGroupsByUser();
                }
                break;
            case 'post'://新建分组
                $this->createGroup();
                break;
            case 'put'://

                break;
            case 'patch'://更新分组
                $this->updateGroup();
                break;
            case 'delete':

                break;
        }
        // Response::sendJSON(array('user'=>__CLASS__));
    }

    /**
     * 新建分组
     * @apiName CreateGroup
     * @api {POST} /auth/v1/groups CreateGroup
     * @apiDescription 新建分组
     * @apiGroup Auth.Group
     * @apiVersion 0.1.0
     *
     * @apiParam {string} groupname 分组名称
     * @apiParam {string} description 分组描述
     * @apiParam {string} rolename 角色名称
     *
     * @apiSuccess {string} groupid 分组ID
     *
     * @apiSampleRequest /auth/v1/groups
     * @apiPermission app_auth_group_create
     */
    private function createGroup()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_group_create');
        }
        $this->view()->checkParams(array('groupname','description','rolename'));
        
        $groupname=$this->get('groupname');
        $description=$this->get('description');
        $role=$this->get('rolename');
        $groupid=$this->model()->create($groupname, $description, $role);
        if ($groupid===false) {
            throw new Exception('group already exists', 400);
        }
        $this->view()->sendJSON(array('groupid'=>$groupid));
    }

    /**
     * 枚举所有分组
     * @apiName ListGroups
     * @api {GET} /auth/v1/groups ListGroups
     * @apiDescription 枚举所有分组
     * @apiGroup Auth.Group
     * @apiVersion 0.1.0
     *
     * @apiSuccess {object} groups 分组列表
     *
     * @apiSampleRequest /auth/v1/groups
     * @apiPermission app_auth_group_list
     */
    private function listGroups()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_group_list');
        }
        $rolelist=$this->model()->list();
        $this->view()->sendJSON(array('groups'=>$rolelist));
    }

    /**
     * 更新分组描述
     * @apiName UpdateGroup
     * @api {PATCH} /auth/v1/groups UpdateGroup
     * @apiDescription 更新分组描述
     * @apiGroup Auth.Group
     * @apiVersion 0.1.0
     *
     * @apiParam {string} groupname 分组名称
     * @apiParam {object} info 分组信息
     * @apiParam {string} [info.groupname] 新分组名称
     * @apiParam {string} [info.description] 分组描述
     * @apiParam {string} [info.rolename] 角色名称
     *
     * @apiSuccess {object} groupid 分组ID
     *
     * @apiSampleRequest /auth/v1/groups
     * @apiPermission app_auth_group_update
     */
    private function updateGroup()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_group_update');
        }

        $this->view()->checkParams(array('groupname','info'));
        $groupname=$this->get('groupname');
        $info=$this->get('info');

        $groupid=$this->model()->update($groupname, $info);
        if ($groupid===false) {
            throw new Exception('update group info failed', 422);
        }
        $this->view()->sendJSON(array('groupid'=>$groupid));
    }

    /**
     * 枚举用户拥有的所有分组
     * @apiName ListGroupsByUser
     * @api {GET} /auth/v1/groups ListGroupsByUser
     * @apiDescription 枚举用户拥有的所有分组
     * @apiGroup Auth.Group
     * @apiVersion 0.1.0
     *
     * @apiParam {string} workid 工号
     *
     * @apiSuccess {object} groups 分组列表
     *
     * @apiSampleRequest /auth/v1/groups
     * @apiPermission app_auth_group_list
     */
    private function listGroupsByUser()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_group_list');
        }

        $this->view()->checkParams(array('workid'));
        $list=$this->model()->listGroupsByUser($this->get('workid'));
        $this->view()->sendJSON(array('groups'=>$list));
    }
}
