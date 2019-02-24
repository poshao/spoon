<?php
namespace App\Linkcs\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Files extends \Spoon\Controller
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
                if (!empty($this->get('filename'))) {
                    $this->getFile();
                } else {
                    $this->getFilelist();
                }
                break;
            case 'post':
                $this->addFile();
                break;
            case 'put':

                break;
            case 'patch':
                
                break;
            case 'delete':
                $this->removeFile();
                break;
        }
    }

    /**
     * 添加附件
     * @apiName AddFile
     * @api {POST} /linkcs/v1/files AddFile
     * @apiDescription 添加附件
     * @apiGroup CS
     * @apiVersion 0.1.0
     *
     * @apiParam {binary} file
     *
     * @apiSampleRequest /auth/v1/files
     * @apiPermission app_linkcs_file_add
     */
    private function addFile()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_linkcs_file_add');
        }

        $workid=$verify->getWorkid();
        $name=$this->model()->addFile($workid, $_FILES['file']);
        if ($name===false) {
            throw new Exception('create file failed', 500);
        }

        $this->view()->sendJSON(array('file'=>$name));
    }

    /**
     * 获取已上传的文件列表
     * @apiName GetFileList
     * @api {GET} /linkcs/v1/files GetFileList
     * @apiDescription 获取已上传的文件列表
     * @apiGroup CS
     * @apiVersion 0.1.0
     *
     * @apiSampleRequest /auth/v1/files
     * @apiPermission app_linkcs_file_list
     */
    private function getFilelist()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_linkcs_file_list');
        }

        $workid=$verify->getWorkid();
        $list=$this->model()->getFilelist($workid);
        $this->view()->sendJSON(array('list'=>$list));
    }

    /**
     * 删除文件
     * @apiName RemoveFile
     * @api {DELETE} /linkcs/v1/files RemoveFile
     * @apiDescription 删除文件
     * @apiGroup CS
     * @apiVersion 0.1.0
     *
     * @apiParam {string} filename 文件名称
     *
     * @apiSampleRequest /auth/v1/files
     * @apiPermission app_linkcs_file_delete
     */
    private function removeFile()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_linkcs_file_delete');
        }
        $this->view()->checkParams(array('filename'));

        $workid=$verify->getWorkid();
        $name=$this->get('filename');
        $rlst=$this->model()->removeFile($workid, $name);
        if ($rlst===false) {
            throw new Exception('delete file failed', 404);
        }
        $this->view()->sendJSON(array(), 204);
    }

    /**
     * 获取文件
     * @apiName GetFile
     * @api {GET} /linkcs/v1/files GetFile
     * @apiDescription 获取文件
     * @apiGroup CS
     * @apiVersion 0.1.0
     *
     * @apiParam {string} filename 文件名称
     *
     * @apiSampleRequest /auth/v1/files
     * @apiPermission app_linkcs_file_get
     */
    private function getFile()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_linkcs_file_get');
        }
        $this->view()->checkParams(array('filename'));

        $workid=$verify->getWorkid();
        $name=$this->get('filename');
        $info=$this->model()->getFile($workid, $name);
        if ($info===false) {
            throw new Exception('file not found', 404);
        }
        $this->view()->sendFile($info);
    }
}
