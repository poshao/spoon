<?php
namespace App\Vba\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Packages extends \Spoon\Controller
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
     * 枚举所有组件包
     * @apiName ListPackages
     * @api {GET} /vba/v1/orders ListPackages
     * @apiDescription 枚举所有组件包
     * @apiGroup VBA.Packages
     * @apiVersion 0.1.0
     *
     * @apiParam {string} search 搜索内容
     * @apiSuccess {json} packages 库列表
     *
     * @apiSampleRequest /vba/v1/packages
     */
    private function listPackages()
    {
      // $this->view()->checkParams(array('search'));
      $search=$this->get('search');
      $result=$list=$this->view()->listPackages($search);
      $this->view()->sendJSON(array('packages'=>$result));
    }

    /**
     * 上传组件包
     * @apiName UploadPackage
     * @api {POST} /vba/v1/packages UploadPackage
     * @apiDescription 上传组件包
     * @apiGroup VBA.Packages
     * @apiVersion 0.1.0
     *
     * @apiParam {string} name 包名称
     * @apiParam {string} version 版本号
     * @apiParam {string} author 作者
     * @apiParam {string} description 描述
     * @apiParam {object} file 文件
     * @apiSuccess {string} packageid 
     *
     * @apiSampleRequest /vba/v1/packages
     */
    private function uploadPackage()
    {
      $this->view()->checkParams(array('name','version','author','description'));
      $name=$this->get('name');
      $version=$this->get('version');
      $author=$this->get('author');
      $description=$this->get('description');
      $file=$_FILES['file'];
      $packageid=$this->model()->uploadPackage($name,$version,$author,$description,$file);
      if(empty($packageid)){
        throw new Exception('save file failed',400);
      }
      $this->view()->sendJSON(array('packageid'=>$packageid));
    }

    /**
     * 下载组件包
     * @apiName DownloadPackage
     * @api {GET} /vba/v1/packages DownloadPackage
     * @apiDescription 下载组件包
     * @apiGroup VBA.Packages
     * @apiVersion 0.1.0
     *
     * @apiParam {string} name 包名称
     * @apiParam {string} version 版本号
     *
     * @apiSuccess {object} file 文件
     * 
     * @apiSampleRequest /vba/v1/packages
     */
    private function DownloadPackage()
    {
      $this->view()->checkParams(array('name','version'));
      $name=$this->get('name');
      $version=$this->get('version');
      $file=$this->model()->downloadPackage($name,$version);
      $this->view()->sendFile($file);
    }
}
