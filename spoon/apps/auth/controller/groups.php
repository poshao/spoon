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
                } else {
                    // $this->getInfo();
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
     * @apiGroup Group
     * @apiVersion 0.1.0
     *
     * @apiParam {string} group 分组名称
     * @apiParam {string} description 分组描述
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
            $verify->CheckGroup('app_auth_group_create');
        }
        $this->view()->checkParams(array('group','description'));
        
        $groupname=$this->get('group');
        $description=$this->get('description');
        $groupid=$this->model()->create($groupname, $description);
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
     * @apiGroup Group
     * @apiVersion 0.1.0
     *
     * @apiSuccess {object} Groups 分组列表
     *
     * @apiSampleRequest /auth/v1/groups
     * @apiPermission app_auth_group_list
     */
    private function listGroups()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckGroup('app_auth_group_list');
        }
        $rolelist=$this->model()->list();
        $this->view()->sendJSON(array('Groups'=>$rolelist));
    }

    /**
     * 更新分组描述
     * @apiName UpdateGroup
     * @api {PATCH} /auth/v1/groups UpdateGroup
     * @apiDescription 更新分组描述
     * @apiGroup Group
     * @apiVersion 0.1.0
     *
     * @apiParam {integer} groupid 分组ID
     * @apiParam {object} info 分组信息
     * @apiParam {string} [info.groupname] 分组名称
     * @apiParam {string} [info.description] 分组描述
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
            $verify->CheckGroup('app_auth_group_update');
        }

        $this->view()->checkParams(array('groupid','info'));
        $id=$this->get('groupid');
        $info=$this->get('info');

        $groupid=$this->model()->update($id, $info);
        if ($groupid===false) {
            throw new Exception('update group info failed', 422);
        }
        $this->view()->sendJSON(array('groupid'=>$groupid));
    }
}
