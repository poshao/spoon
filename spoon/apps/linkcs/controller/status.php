<?php
namespace App\Linkcs\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Status extends \Spoon\Controller
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
                $this->getOrderCountLast7Days();
                break;
            case 'post':
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
     * 获取最近7天的日单量
     * @apiName getOrderCountLast7Days
     * @api {GET} /linkcs/v1/status getOrderCountLast7Days
     * @apiDescription 获取最近7天的日单量
     * @apiGroup Linkcs.status
     * @apiVersion 0.1.0
     *
     * @apiParam {string} type 类型
     *
     * @apiSuccess {int} userid 用户编码
     * @apiSuccessExample {json} 成功响应:
     * {
     *      "userid":1
     * }
     * @apiSampleRequest /linkcs/v1/status
     */
    private function getOrderCountLast7Days()
    {
        $rs=$this->model()->getDailyOrderCount();
        $this->view()->sendJSON(array('dataset'=>$rs));
    }
}
