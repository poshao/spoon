<?php
namespace App\Linkcs\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Orders extends \Spoon\Controller
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
            $this->list();
            break;
        case 'post'://login
            // $this->login();
            $this->newRequest();
            break;
        case 'put'://

            break;
        case 'patch'://
            
            break;
        case 'delete'://
            // $this->logout();
            break;
    }
    }

    /**
     * 新增记录
     * @apiName NewRequest
     * @api {POST} /linkcs/v1/orders NewRequest
     * @apiDescription CS创建订单
     * @apiGroup CS
     * @apiVersion 0.1.0
     *
     * @apiParam {JSON} detail
     * @apiSampleRequest /auth/v1/orders
     * @apiPermission app_linkcs_newrequest
     */
    private function newRequest()
    {
        //检查登录状态及权限
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_linkcs_newrequest');
        } else {
            throw new Exception('认证模块未启用', 500);
        }

        $this->view()->checkParams(array('detail'));
        
        $workid=$verify->getWorkid();
        $detail=$this->get('detail');
        
        $requestid=$this->model()->newRequest($workid, $detail);
        $this->view()->sendJSON(array('id'=>$requestid));
    }

    /**
     * 获取列表
     * @apiName listRequest
     * @api {GET} /linkcs/v1/orders listRequest
     * @apiDescription 获取CS订单列表
     * @apiGroup CS
     * @apiVersion 0.1.0
     *
     * @apiParam {JSON} detail
     * @apiSampleRequest /auth/v1/orders
     * @apiPermission app_linkcs_list_request
     */
    private function list()
    {
        //检查登录状态及权限
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_linkcs_newrequest');
        }

        $list=$this->model()->list();
        // var_dump($list);
        $this->view()->sendJson(array('list'=>$list));
    }
}
