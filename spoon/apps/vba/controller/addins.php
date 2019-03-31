<?php
namespace App\Vba\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Addins extends \Spoon\Controller
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
                if ($this->view()->paramsCount()===0) {
                    $this->listAddins();
                } elseif (!empty($this->get('addinid'))) {
                    $this->downloadAddin();
                } elseif (!empty($this->get('loginname'))) {
                    $this->listAddinsByUser();
                }
                break;
            case 'post':
                $this->uploadAddin();
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
     * 枚举所有加载项
     * @apiName ListAddins
     * @api {GET} /vba/v1/addins ListAddins
     * @apiDescription 枚举所有加载项
     * @apiGroup VBA.Addins
     * @apiVersion 0.1.0
     *
     * @apiSuccess {object} addins 加载项列表
     * @apiSuccessExample {json} 成功响应:
     * {
     *      "addins":{
     *          1:{name:'test','hashname':123456}
     *      }
     *
     * }
     * @apiSampleRequest /vba/v1/addins
     * @apiPermission app_vba_addin_list
     */
    private function listAddins()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_vba_addin_list');
        }
        $result=$this->model()->listAddins();
        if ($result===false) {
            throw new Exception('list addins failed', 500);
        }
        $this->view()->sendJson(array('addins'=>$result));
    }

    /**
     * 上传加载项
     * @apiName UploadAddin
     * @api {POST} /vba/v1/addins UploadAddin
     * @apiDescription 上传加载项
     * @apiGroup VBA.Addins
     * @apiVersion 0.1.0
     *
     * @apiParam {string} name 加载项名称
     * @apiParam {string} version 版本号
     * @apiParam {string} description 描述
     * @apiParam {object} file 加载项文件
     *
     * @apiSuccess {object} addinid 加载项ID
     * @apiSuccessExample {json} 成功响应:
     * {
     *      "addinid":1
     *
     * }
     * @apiSampleRequest /vba/v1/addins
     * @apiPermission app_vba_addin_upload
     */
    private function uploadAddin()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_vba_addin_upload');
        }

        $this->view()->checkParams(array('name','version','description','file'));

        $name=$this->get('name');
        $version=$this->get('version');
        $description=$this->get('description');
        $filePath=$_FILES['file']['tmp_file'];
        $row=$this->model()->uploadFile($name, $version, $description, $filePath);
        if (-1===$row) {
            throw new Exception('already uploaded', 422);
        } elseif (-2===$row) {
            throw new Exception('store file failed', 500);
        }
        $this->view()->sendJson(array('addinid'=>$row['id']));
    }

    /**
     * 下载加载项
     * @apiName DownloadAddin
     * @api {GET} /vba/v1/addins DownloadAddin
     * @apiDescription 下载加载项
     * @apiGroup VBA.Addins
     * @apiVersion 0.1.0
     *
     * @apiParam {string} addinid 加载项Id
     *
     * @apiSuccess {object} file 文件
     *
     * @apiSampleRequest /vba/v1/addins
     */
    private function downloadAddin()
    {
        $this->view()->checkParams(array('addinid'));
        $info=$this->model()->getAddinFileinfo($this->get('addinid'));
        if ($info===false) {
            throw new Exception('file not found', 404);
        }
        $this->view()->sendFile($info);
    }

    /**
     * 根据用户列举加载项列表
     * @apiName ListAddinsByUser
     * @api {GET} /vba/v1/addins ListAddinsByUser
     * @apiDescription 下载加载项
     * @apiGroup VBA.Addins
     * @apiVersion 0.1.0
     *
     * @apiParam {string} loginname 用户系统登录名
     *
     * @apiSuccess {object} addins
     *
     * @apiSampleRequest /vba/v1/addins
     */
    private function listAddinsByUser()
    {
        $this->view()->checkParams(array('loginname'));
        $result=$this->model()->listAddinsByUser($this->get('loginname'));
        if ($result===false) {
            throw new Exception('internal error', 500);
        }
        $this->view()->sendJson(array('addins'=>$result));
    }

    /**
     * 升级所有用户的同名加载项
     * @apiName UpgradeAddin
     * @api {PATCH} /vba/v1/addins UpgradeAddin
     * @apiDescription 升级所有用户的同名加载项
     * @apiGroup VBA.Addins
     * @apiVersion 0.1.0
     *
     * @apiParam {string} addinname 加载项名称
     * @apiParam {string} version 版本
     *
     * @apiSuccess {object} result
     *
     * @apiSampleRequest /vba/v1/addins
     * @apiPermission app_vba_user_assign_addin
     */
    private function upgradeAddin()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_vba_user_assign_addin');
        }
        $addinname=$this->get('addinname');
        $version=$this->get('version');
        $result=$this->model()->upgradeAddin($addinname, $version);
        $this->view()->sendJson(array('result'=>$result?'ok':'failed'));
    }
}
