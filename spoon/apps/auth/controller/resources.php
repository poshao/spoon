<?php
namespace App\Auth\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Resources extends \Spoon\Controller
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
                $this->getAvator();
                break;
            case 'post'://
                $this->uploadAvator();
                break;
            case 'put'://

                break;
            case 'patch'://
                
                break;
            case 'delete'://
                
                break;
        }
    }

    /**
     * 上传头像文件
     * @apiName UploadAvator
     * @api {POST} /auth/v1/resource UploadAvator
     * @apiDescription 上传头像文件
     * @apiGroup Resource
     * @apiVersion 0.1.0
     *
     * @apiParam {binary} file 图像文件
     *
     * @apiSuccess {string} avator 文件名
     *
     * @apiSuccessExample {json} 成功响应:
     * {
     *      "avator":"9c14dcf0e6f36bbef9d4cca81dd64d91"
     * }
     * @apiSampleRequest /auth/v1/resource
     * @apiPermission app_auth_user_update
     */
    private function uploadAvator()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_auth_user_update');
        }
        $workid=$verify->getWorkid();
        $filename=$this->model()->updateAvator($workid, $_FILES['file']['tmp_name']);
        if ($filename===false) {
            throw new Exception('update avator failed', 422);
        }
        $this->view()->sendJSON(array('avator'=>$filename));
    }

    /**
     * 获取用户头像
     * @apiName GetAvator
     * @api {GET} /auth/v1/resource GetAvator
     * @apiDescription 获取用户头像
     * @apiGroup Resource
     * @apiVersion 0.1.0
     *
     * @apiParam {string} workid 工号
     *
     * @apiSuccess {image} image 图像文件
     *
     * @apiSampleRequest /auth/v1/resources
     */
    private function getAvator()
    {
        $this->view()->checkParams(array('workid'));
        $workid=$this->get('workid');
        $filename=$this->model()->getAvator($workid);
        if ($filename===false) {
            throw new Exception('not found avator', 404);
        }

        \header("content-type: image/png");
        $im=\imagecreatefrompng($filename);
        \imagepng($im);
        \imagedestroy($im);
    }
}
