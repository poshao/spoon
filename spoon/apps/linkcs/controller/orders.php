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
                $this->updateOrder();
                break;
            case 'patch'://
                $ss=$this->get('orderid');
                if ($this->view()->paramsCount()==1 && null!==$this->get('orderid')) {
                    $this->resetAttachments();
                } else {
                    $this->updateStatus();
                }
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
     * @apiParam {string} [detail.parentid] 重发的原始记录ID
     *
     * @apiSuccess {string} orderid 订单编号
     *
     * @apiSampleRequest /auth/v1/orders
     * @apiPermission app_linkcs_newrequest
     * @apiPermission app_linkcs_orders_update_status_resend
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
        
        //检查重发情况
        if (isset($detail['parentid'])) {
            $verify->CheckPermission('app_linkcs_orders_update_status_resend');
        }

        $requestid=$this->model()->newRequest($workid, $detail);

        $this->view()->sendJSON(array('orderid'=>$requestid));
    }

    /**
     * 获取列表
     * @apiName ListRequest
     * @api {GET} /linkcs/v1/orders ListRequest
     * @apiDescription 获取CS订单列表
     * @apiGroup LinkCS.Order
     * @apiVersion 0.1.0
     *
     * @apiParam {string} option 查询设置(base64转码)
     *
     * filters:筛选条件
     * filters.operator:操作符号  =,!=,>,<,>=,<=,in,!in,like
     * filters.key: 主键
     * filters.value: 计算值
     *
     * sorts:排序条件
     * sorts.key:主键名称
     * sorts.order:排序顺序 'asc','desc'
     *
     * page:分页
     * page.index 页号 从0开始
     * page.count 每页行数
     *
     * @apiParamExample {json} Request-Example:
     * {
     *  option:{
     *      filter:{
     *          "and":[
     *              {op:'=',key:'status',val:'s'},
     *              {op:'in',key:'status',val:['a','b']}
     *          ]
     *      },
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
     * @apiSuccess {object} list 数据列表
     * @apiSuccess {integer} total 满足条件的总行数
     *
     * @apiSampleRequest /auth/v1/orders
     * @apiPermission app_linkcs_list_request
     */
    private function list()
    {
        //检查登录状态及权限
        // $verify=\Spoon\DI::getDI('verify');
        // if (!empty($verify)) {
        //     $verify->CheckPermission('app_linkcs_list_request');
        // }

        $option=$this->get('option');
        if (!empty($option)) {
            $option=\json_decode(base64_decode($option), true);
        }

        $result=$this->model()->list($option);
        // var_dump($list);
        $this->view()->sendJSON($result);
    }

    /**
     * 更新订单状态
     * @apiName UpdateStatus
     * @api {PATCH} /linkcs/v1/orders UpdateStatus
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
     * @apiPermission app_linkcs_orders_update_status_presend
     * @apiPermission app_linkcs_orders_update_status_sended
     * @apiPermission app_linkcs_orders_update_status_lock
     * @apiPermission app_linkcs_orders_update_status_pass
     * @apiPermission app_linkcs_orders_update_status_reject
     * @apiPermission app_linkcs_orders_update_status_finish
     * @apiPermission app_linkcs_orders_update_status_cancel
     * @apiPermission app_linkcs_orders_update_status_resend
     */
    private function updateStatus()
    {
        $verify=\Spoon\DI::getDI('verify');

        $this->view()->checkParams(array('orderid','status'), array('reason'));

        $workid=$verify->getWorkid();

        $orderid=$this->get('orderid');
        $status=$this->get('status');
        $reason=$this->get('reason');
        if ($status==='reject' && empty($reason)) {
            throw new Exception('lost reject reason', 400);
        }

        //状态检查
        /**
         * pre_send <--> sended --> received --> finish
         *                             |--> reject --> resend
         *                                    |--> cancel
         *
         */
        $permissionlist=array(
            'pre_send>sended'=>'app_linkcs_orders_update_status_sended',
            'pre_send>cancel'=>'app_linkcs_orders_update_status_cancel',
            'sended>received'=>'app_linkcs_orders_update_status_lock',
            'sended>pre_send'=>'app_linkcs_orders_update_status_presend',
            'sended>cancel'=>'app_linkcs_orders_update_status_cancel',
            // 'lock>pass'=>'app_linkcs_orders_update_status_pass',
            'received>finish'=>'app_linkcs_orders_update_status_finish',
            'received>reject'=>'app_linkcs_orders_update_status_reject',
            'reject>resend'=>'app_linkcs_orders_update_status_resend',
            'reject>cancel'=>'app_linkcs_orders_update_status_cancel'
        );

        $curStatus=$this->model()->getStatus($orderid);
        $statusRoute=$curStatus.'>'.$status;

        if (!isset($permissionlist[$statusRoute])) {
            throw new Exception('status unavaliable', 400);
        }

        //权限检查
        $verify->CheckPermission($permissionlist[$statusRoute]);
        
        //针对锁定状态必须由操作用户解除
        if ($status==='finish' || $status==='reject') {
            $originAssign=$this->model()->getAssign($orderid);
            if ($originAssign!==$workid) {
                throw new Exception('only '.$originAssign.' can do it', 400);
            }
        }
        //撤销功能需要匹配下单用户
        if ($status==='pre_send') {
            $originCreator=$this->model()->getCreator($orderid);
            if ($originCreator!==$workid) {
                throw new Exception('only '.$originCreator.' can do it', 400);
            }
        }

        $orderid=$this->model()->updateStatus($workid, $orderid, $status, $reason);

        // 拒绝状态通知下单用户
        if($status==='reject'){
            $createId=$this->model()->getCreator($orderid);
            $user=new \App\Auth\Model\Users();
            $email=$user->getUser($createId,'email')['email'];
            if(!empty($email)){
                $tool=new \App\Common\Model\Tools();
                $url=\Spoon\Config::getByApps('linkcs')['host'].'list?orderid=';
                $body='<h3>Order '.$orderid.' rejected</h3><p>'.$reason.'</p><p><i>detail please click <a href="'.$url.$orderid.'">here</a></i></p>';
                $tool->SendEmail('byron.gong@ap.averydennison.com',$email,'[Notice](LIS) Order Release',$body,null,null,null,true);
            }
        }

        if ($orderid===false) {
            throw new Exception('update status failed', 400);
        }
        $this->view()->sendJSON(array('orderid'=>$orderid));
    }

    /**
     * 更新订单
     * @apiName UpdateOrder
     * @api {PUT} /linkcs/v1/orders UpdateOrder
     * @apiDescription 更新订单
     * @apiGroup LinkCS.Order
     * @apiVersion 0.1.0
     *
     * @apiParam {string} orderid 订单号
     * @apiParam {JSON} detail
     *
     * @apiSuccess {integer} orderid 订单号
     *
     * @apiSampleRequest /auth/v1/orders
     * @apiPermission app_linkcs_orders_update_status_sended
     */
    private function updateOrder()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_linkcs_orders_update_status_sended');
        }

        $this->view()->CheckParams(array('orderid','detail'));

        
        $workid=$verify->getWorkid();
        $orderid=$this->get('orderid');
        $detail=$this->get('detail');

        // 检查状态
        $currentStatus=$this->model()->getStatus($orderid);
        if ($currentStatus!=='pre_send') {
            throw new Exception('status unavaliable', 400);
        }

        // 更新数据
        $orderid=$this->model()->newRequest($workid, $detail, $orderid);
        if (empty($orderid)) {
            throw new Exception('update order failed', 400);
        }
        $this->view()->sendJSON(array('orderid'=>$orderid));
    }

    /**
     * 重置附件
     * @apiName ResetAttachments
     * @api {PATCH} /linkcs/v1/orders ResetAttachments
     * @apiDescription 重置附件
     * @apiGroup LinkCS.Order
     * @apiVersion 0.1.0
     *
     * @apiParam {string} orderid 订单号
     *
     *
     * @apiSampleRequest /auth/v1/orders
     * @apiPermission app_linkcs_orders_update_status_sended
     */
    private function resetAttachments()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            // $verify->CheckPermission('permissionname');
        }
        $workid=$verify->getWorkid();
        $orderid=$this->get('orderid');
        $this->view()->CheckParams(array('orderid'));
        $result=$this->model()->resetAttachments($orderid, $workid);
        if ($result===false) {
            throw new Exception('reset attachments failed', 500);
        }
        $this->view()->sendJSON(array('result'=>'ok'));
    }
}
