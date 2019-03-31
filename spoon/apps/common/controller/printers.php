<?php
namespace App\Common\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Printers extends \Spoon\Controller
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
     * 获取打印机列表
     * @apiName ListPrinters
     * @api {GET} /common/v1/printers ListPrinters
     * @apiDescription 获取打印机列表
     * @apiGroup Common.Printer
     * @apiVersion 0.1.0
     *
     * @apiSuccess {string} printers 打印机列表
     * @apiSampleRequest /common/v1/printers
     */
    private function listPrinters()
    {
    }
}
