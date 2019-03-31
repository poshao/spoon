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
                $this->updateStatus();
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
     * @apiGroup LinkCS.Order
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
     * @apiName ListRequest
     * @api {GET} /linkcs/v1/orders ListRequest
     * @apiDescription 获取CS订单列表
     * @apiGroup LinkCS.Order
     * @apiVersion 0.1.0
     *
     * @apiParam {JSON} option 查询设置(base64转码)
     *
     * filters:筛选条件
     * filter.operator:操作符号  =,!=,>,<,>=,<=,in,!in,like
     * filter.key: 主键
     * filter.value: 计算值
     *
     * sorts:排序条件
     * sort.key:主键名称
     * sort.order:排序顺序 'asc','desc'
     *
     * page:分页
     * page.index 页号 从0开始
     * page.count 每页行数
     *
     * @apiParamExample {json} Request-Example:
     * {
     *  option:{
     *      filters:[
     *          {operator:'=',key:'status',value:'s'},
     *          {operator:'in',key:'status',value:['a','b']}
     *      ],
     *      sorts:[
     *          {key:'id',order:'asc'},
     *          {key:'status',order:'desc'}
     *      ],
     *      page:{
     *          index:0,
     *          count:100
     *      }
     *  }
     * }
     * @apiSampleRequest /auth/v1/orders
     * @apiPermission app_linkcs_list_request
     */
    private function list()
    {
        //检查登录状态及权限
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_linkcs_list_request');
        }

        $option=$this->get('option');
        if (!empty($option)) {
            $option=\json_decode(base64_decode($option), true);
        }

        $list=$this->model()->list($option);
        // var_dump($list);
        $this->view()->sendJSON(array('list'=>$list));
    }

    /**
     * 更新订单状态
     * @apiName UpdateStatus
     * @api {PUT} /linkcs/v1/orders UpdateStatus
     * @apiDescription 更新订单状态
     * @apiGroup LinkCS.Order
     * @apiVersion 0.1.0
     *
     * @apiParam {string} orderid 订单号
     * @apiParam {string} status 订单状态(unknown,accept,reject)
     * @apiParam {string} [reason] 拒绝原因
     *
     * @apiSuccess {integer} orderid 订单号
     *
     * @apiSampleRequest /auth/v1/orders
     * @apiPermission app_linkcs_update_status
     */
    private function updateStatus()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_linkcs_update_status');
        }

        $this->view()->checkParams(array('orderid','status'), array('reason'));

        $workid=$verify->getWorkid();

        $orderid=$this->get('orderid');
        $status=$this->get('status');
        $reason=$this->get('reason');
        if ($status==='reject' && empty($reason)) {
            throw new Exception('lost reject reason', 400);
        }
        $orderid=$this->model()->updateStatus($orderid, $workid, $status, $reason);
        if ($orderid===false) {
            throw new Exception('update status failed', 400);
        }
        $this->view()->sendJSON(array('orderid'=>$orderid));
    }
}
