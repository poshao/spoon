<?php
namespace App\Vba\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Funs extends \Spoon\Controller
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
                if (!empty($this->get('loginname'))) {
                    $this->listFunsByUserAddin();
                } else {
                    $this->listFunsByAddin();
                }
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
     * 根据加载项枚举子功能列表
     * @apiName ListFunsByAddin
     * @api {GET} /vba/v1/funs ListFunsByAddin
     * @apiDescription 根据加载项枚举子功能列表
     * @apiGroup VBA.Fun
     * @apiVersion 0.1.0
     *
     * @apiParam {string} addinname 加载项名称
     *
     * @apiSuccess {object} funs 子功能列表
     *
     * @apiSampleRequest /vba/v1/funs
     * @apiPermission app_vba_fun_list
     */
    private function listFunsByAddin()
    {
        $verify=\Spoon\DI::getDI('verify');
        if (!empty($verify)) {
            $verify->CheckPermission('app_vba_fun_list');
        }
        $addinname=$this->get('addinname');
        $funs=$this->model()->listFunsByAddin($addinname);
        $this->view()->sendJson(array('funs'=>$funs));
    }

    /**
     * 根据加载项和用户名枚举子功能列表
     * @apiName ListFunsByUserAddin
     * @api {GET} /vba/v1/funs ListFunsByUserAddin
     * @apiDescription 根据加载项和用户名枚举子功能列表
     * @apiGroup VBA.Fun
     * @apiVersion 0.1.0
     *
     * @apiParam {string} loginname 用户登录名
     * @apiParam {string} addinname 加载项名称
     *
     * @apiSuccess {object} funs 子功能列表
     *
     * @apiSampleRequest /vba/v1/funs
     */
    private function listFunsByUserAddin()
    {
        $this->view()->checkParams(array('loginname','addinname'));
        $loginname=$this->get('loginname');
        $addinname=$this->get('addinname');
        $funs=$this->model()->listFunsByUserAddin($loginname, $addinname);
        $this->view()->sendJson(array('funs'=>$funs));
    }
}
