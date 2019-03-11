<?php
namespace App\Linkcs\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Reports extends \Spoon\Controller
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
     * 新增表格定义
     * @apiName CreateReportStruct
     * @api {POST} /linkcs/v1/reports CreateReportStruct
     * @apiDescription 新增表格定义
     * @apiGroup CS
     * @apiVersion 0.1.0
     *
     * @apiParam {JSON} detail
     * @apiSampleRequest /auth/v1/reports
     * @apiPermission app_linkcs_report_create_struct
     */
    private function createReportStruct()
    {
    }

    /**
     * 更新表格定义
     * @apiName UpdateReportStruct
     * @api {PATCH} /linkcs/v1/reports UpdateReportStruct
     * @apiDescription 更新表格定义
     * @apiGroup CS
     * @apiVersion 0.1.0
     *
     * @apiParam {JSON} detail
     * @apiSampleRequest /auth/v1/reports
     * @apiPermission app_linkcs_report_update_struct
     */
    private function updateReportStruct()
    {
    }

    /**
     * 删除表格定义
     * @apiName DeleteReportStruct
     * @api {DELETE} /linkcs/v1/reports DeleteReportStruct
     * @apiDescription 删除表格定义
     * @apiGroup CS
     * @apiVersion 0.1.0
     *
     * @apiParam {JSON} detail
     * @apiSampleRequest /auth/v1/reports
     * @apiPermission app_linkcs_report_delete_struct
     */
    private function deleteReportStruct()
    {
    }

    /**
     * 导出报表
     * @apiName ExportReoort
     * @api {GET} /linkcs/v1/reports ExportReoort
     * @apiDescription 导出报表
     * @apiGroup CS
     * @apiVersion 0.1.0
     *
     * @apiParam {reportid} 报表序号
     *
     * @apiSampleRequest /auth/v1/reports
     */
    private function exportReport()
    {
    }
}
