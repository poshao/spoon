<?php
namespace App\Common\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Tools extends \Spoon\Controller
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
                // $this->SendEmail();
                $this->ExecSQL();
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
     * 执行数据库查询
     * @apiName ExecSql
     * @api {POST} /common/v1/tools ExecSql
     * @apiDescription 执行数据库查询
     * @apiGroup Common.Tools
     * @apiVersion 0.1.0
     *
     * @apiParam {string} sql sql查询语句
     * @apiParam {json} [fieldmap] 字段映射
     * @apiParam {json} [dataset] 导入数据
     * @apiParam {json} [option] 选项
     * @apiParam {string} [option.filename] mdb文件路径(仅支持32位程序)
     *
     * @apiParamExample  {json} 请求示例:
     * {
     *      "workid":"8020507",
     *      "password":"123456"
     * }
     *
     * @apiSuccess {int} rows 记录行数/受影响的行数
     * @apiSampleRequest /common/v1/tools
     *
     */
    private function ExecSQL()
    {
        ini_set('memory_limit', '-1');
        $this->view()->CheckParams(array('sql'));

        $sql=$this->get('sql');
        $fieldmap=$this->get('fieldmap');
        $dataset=$this->get('dataset');
        $option=$this->get('option');
        if (empty($option)) {
            $result=$this->model()->ExecSQL($sql, $fieldmap, $dataset);
        } else {
            $result=$this->model()->ExecSQLMdb($sql, $fieldmap, $dataset, $option);
        }
        $this->view()->sendJSON($result);
    }

    /**
     * 发送邮件通知
     * @apiName SendMail
     * @api {POST} /common/v1/tools SendMail
     * @apiDescription 发送邮件通知
     * @apiGroup Common.Tools
     * @apiVersion 0.1.0
     *
     * @apiParam {string} from 发件人
     * @apiParam {string} to 收件人列表(用逗号隔开)
     * @apiParam {string} [cc] 抄送列表(用逗号隔开)
     * @apiParam {string} [bcc] 密送列表(用逗号隔开)
     * @apiParam {string} subject 主题
     * @apiParam {string} body 正文
     * @apiParam {boolean} [ishtml] 按html解析,默认为false
     * @apiParam {Array} [attachments] 附件,变量名使用数组形式[]
     *
     * @apiSuccess {int} rows 记录行数/受影响的行数
     * @apiSampleRequest /common/v1/tools
     *
     */
    private function SendEmail()
    {
        $from=$this->get('from');// "byron.gong@ap.averydennison.com";
        $to=$this->get('to'); //"byron.gong@ap.averydennison.com";
        $cc=$this->get('cc');// "";
        $bcc=$this->get('bcc');// "";
        $subject=$this->get('subject');// "TEST";
        $ishtml=!!$this->get('ishtml');
        $body=$this->get('body');// "hello world!";
        $attachments=$_FILES['attachments'];

        $rlst=$this->model()->SendEmail($from, $to, $subject, $body, $cc, $bcc, $attachments, $ishtml);
        
        $this->view()->sendJSON(array('result'=>$rlst));
    }
}
