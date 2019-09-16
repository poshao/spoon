<?php
namespace App\Auth\Controller;

use \Spoon\Response;
use \Spoon\Exception;

class Tasks extends \Spoon\Controller
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
            $this->resetPasswordByEmail();
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
   * 通过邮件重置密码
   * @apiName ResetPasswordByEmail
   * @api {POST} /auth/v1/tasks ResetPasswordByEmail
   * @apiDescription 通过邮件重置密码
   * @apiGroup Auth.Tasks
   * @apiVersion 0.1.0
   *
   * @apiParam {string} workid 工号
   * @apiParam {string} [code] 验证码
   * @apiParam {string} [password] 新密码
   *
   * @apiSuccess {object} users 用户清单
   * @apiSampleRequest /auth/v1/Tasks
   */
  private function resetPasswordByEmail(){
    // $verify=\Spoon\DI::getDI('verify');
    // if (!empty($verify)) {
    // $verify->CheckPermission('permissionname');
    // }
    $workid=$this->get('workid');
    $code=$this->get('code');
    $password=$this->get('password');
    
    $result=false;
    if(empty($code)){
        $rq=$this->model()->resetPasswordByEmailRequest($workid);
        if($rq===false){
          throw new Exception('1.invalid workid 2.email not set',400);
        }
        $tool=new \App\Common\Model\Tools();
        $body='验证码:'.$rq['code'];
        $tool->SendEmail('byron.gong@ap.averydennison.com',$rq['email'],'[LIS]Reset Password',$body);
        $result=true;
    }else{
        // 验证身份并修改密码
        $password=\Spoon\Encrypt::hashPassword($password, \Spoon\Config::getByApps('auth')['salt']);
        $result=$this->model()->resetPasswordByEmailConfirm($workid,$code,$password);
    }
    $this->view()->sendJSON(array('result'=>$result));
  }
}