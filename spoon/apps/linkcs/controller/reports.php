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
                if ($this->view()->paramsCount()==0) {
                    $this->listReports();
                } else {
                    $this->getReportStruct();
                }
                break;
            case 'post':
                $this->createReportStruct();
                break;
            case 'put':

                break;
            case 'patch':
                
                break;
            case 'delete':
                $this->deleteReportStruct();
                break;
        }
    }

    /**
     * 新增表格定义
     * @apiName CreateReportStruct
     * @api {POST} /linkcs/v1/reports CreateReportStruct
     * @apiDescription 新增表格定义
     * @apiGroup LinkCS.Report
     * @apiVersion 0.1.0
     *
     * @apiParam {string} reportname 表格名称
     * @apiParam {JSON} struct 结构描述
     *
     * @apiSampleRequest /auth/v1/reports
     * @apiPermission app_linkcs_report_create_struct
     */
    private function createReportStruct()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_linkcs_report_create_struct');
        }

        $this->view()->checkParams(array('reportname','struct'));

        $workid=$verify->getWorkid();
        $name=$this->get('reportname');
        $struct=$this->get('struct');
        if ($this->model()->createReport($workid, $name, $struct)===true) {
            $this->view()->sendJSON(array('result'=>'ok'));
        }
    }

    /**
     * 删除表格定义
     * @apiName DeleteReportStruct
     * @api {DELETE} /linkcs/v1/reports DeleteReportStruct
     * @apiDescription 删除表格定义
     * @apiGroup LinkCS.Report
     * @apiVersion 0.1.0
     *
     * @apiParam {string} reportname 报表名称
     *
     * @apiSampleRequest /auth/v1/reports
     * @apiPermission app_linkcs_report_delete_struct
     */
    private function deleteReportStruct()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_linkcs_report_delete_struct');
        }

        $this->view()->checkParams(array('reportname'));
        $reportName=$this->get('reportname');
        $result=$this->model()->deleteReport($reportName);
        $this->view()->sendJSON(array('result'=>$result));
    }

    /**
     * 列出所有表格结构
     * @apiName ListReports
     * @api {GET} /linkcs/v1/reports ListReports
     * @apiDescription 列出所有表格结构
     * @apiGroup LinkCS.Report
     * @apiVersion 0.1.0
     *
     * @apiSampleRequest /auth/v1/reports
     */
    private function listReports()
    {
        $result=$this->model()->listReports();
        $this->view()->sendJSON(array('result'=>$result));
    }

    /**
     * 导出表结构
     * @apiName GetReportStruct
     * @api {GET} /linkcs/v1/reports GetReportStruct
     * @apiDescription 导出表结构
     * @apiGroup LinkCS.Report
     * @apiVersion 0.1.0
     *
     * @apiParam {string} reportname 报表名称
     *
     * @apiSampleRequest /auth/v1/reports
     * @apiPermission app_linkcs_report_get_struct
     */
    private function getReportStruct()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_linkcs_report_get_struct');
        }
        $this->view()->checkParams(array('reportname'));
        $reportname=$this->get('reportname');

        $result=$this->model()->getReportStruct($reportName);
        $this->view()->sendJSON(array('result'=>$result));
    }
}
