<?php
namespace App\Develop\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Projects extends \Spoon\Controller
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
                $this->listProjects();
                break;
            case 'post':
                $this->newProject();
                break;
            case 'put':
                break;
            case 'patch':
                break;
            case 'delete':
                break;
        }
    }

    /**
     * 新建项目
     * @apiName NewProject
     * @api {POST} /develop/v1/projects NewProject
     * @apiDescription 新建项目
     * @apiGroup Develop.Projects
     * @apiVersion 0.1.0
     *
     * @apiParam {string} subject 项目主题
     * @apiParam {string} description 描述
     * @apiParam {object} [files] 附件
     *
     * @apiSuccess {string} projectid 项目ID
     * @apiSampleRequest /develop/v1/projects
     * @apiPermission app_develop_project_create
     */
    private function newProject()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_develop_project_create');
        }

        $this->view()->CheckParams(array('subject','description'));
        $workid=$verify->getWorkid();
        $subject=$this->get('subject');
        $description=$this->get('description');

        $projectid=$this->model()->newProject($workid, $subject, $description, null);

        $this->view()->sendJSON(array('projectid'=>$projectid));
    }

    /**
     * 更新项目资料
     * @apiName UpdateProject
     * @api {POST} /develop/v1/projects UpdateProject
     * @apiDescription 更新项目资料
     * @apiGroup Develop.Projects
     * @apiVersion 0.1.0
     *
     * @apiParam {string} subject 项目主题
     * @apiParam {string} description 描述
     * @apiParam {object} [files] 附件
     *
     * @apiSuccess {string} projectid 项目ID
     * @apiSampleRequest /develop/v1/projects
     * @apiPermission app_develop_project_update_status_request2pass
     * @apiPermission app_develop_project_update_status_request2cancel
     * @apiPermission app_develop_project_update_status_pass2check
     * @apiPermission app_develop_project_update_status_check2process
     * @apiPermission app_develop_project_update_status_check2pending
     * @apiPermission app_develop_project_update_status_process2pending
     * @apiPermission app_develop_project_update_status_pending2check
     * @apiPermission app_develop_project_update_status_pending2cancel
     * @apiPermission app_develop_project_update_status_process2finish
     */
    private function updateProject()
    {
        /**
         * request --> pass --> check --> process --> (finish)
         *   |                    ↑↓        |
         *   |-->(cancel) <--  pending  <-- |
         */

        $this->view()->CheckParams(array('projectid','status','files','remark'));
        $projectid=$this->get('projectid');
        $status=$this->get('status');
        $remark=$this->get('remark');

        $currentStatus=$this->model()->getStatus($projectid);
        if(empty($currentStatus)){
            throw new Exception('invalid project id',400);
        }

        $permissionlist=array(
            'request>pass'=>'app_develop_project_update_status_request2pass',
            'request>cancel'=>'app_develop_project_update_status_request2cancel',
            'pass>check'=>'app_develop_project_update_status_pass2check',
            'check>process'=>'app_develop_project_update_status_check2process',
            'check>pending'=>'app_develop_project_update_status_check2pending',
            'process>pending'=>'app_develop_project_update_status_process2pending',
            'pending>check'=>'app_develop_project_update_status_pending2check',
            'pending>cancel'=>'app_develop_project_update_status_pending2cancel',
            'process>finish'=>'app_develop_project_update_status_process2finish'
        );

        $route=$currentStatus.'>'.$status;
        if (!isset($permissionlist[$route])) {
            throw new Exception('status unavaliable', 400);
        }

        //权限检查
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission($permissionlist[$route]);
        }
        $workid=$verify->getWorkid();
        $id=$this->model()->updateProject($workid,$projectid,$status,$remark,null);
        $this->view()->sendJSON(array('projectid'=>$id));
    }

    /**
     * 列举项目清单
     * @apiName ListProjects
     * @api {GET} /develop/v1/projects ListProjects
     * @apiDescription 列举项目清单
     * @apiGroup Develop.Projects
     * @apiVersion 0.1.0
     *
     *
     * @apiSuccess {json} projects 项目清单
     * @apiSampleRequest /develop/v1/projects
     * @apiPermission ???
     */
    private function listProjects()
    {
        $projects=$this->model()->ListProjects();
        $this->view()->sendJSON(array('projects'=>$projects));
    }
}
