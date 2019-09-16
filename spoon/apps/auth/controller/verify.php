<?php
/**
 * 验证登录信息
 */
namespace App\Auth\Controller;

use \Spoon\Exception;

class Verify extends \Spoon\Controller
{
    public function doMain()
    {
        return false;
    }

    /**
     * 获取工号
     *
     * @return string
     */
    public function getWorkid()
    {
        $workid=$this->model()->GetWorkId($this->getToken());
        if(empty($workid)){
            return false;
        }
        return $workid;
    }

    /**
     * 获取令牌
     *
     * @return string
     */
    private function getToken()
    {
        if (!isset($_POST['_xtoken'])) {
            return false;
        }
        return $_POST['_xtoken'];
    }

    /**
     * 检查用户登录状态
     *
     * @return bool
     */
    public function CheckIsOnline()
    {
        return $this->model()->IsOnline($this->getToken());
    }

    /**
     * 检查用户权限
     *
     * @param string $permission
     * @return bool
     */
    public function CheckPermission($permission)
    {
        if ($this->CheckIsOnline()===false) {
            throw new \Spoon\Exception('please login first', 401);
        }
        if ($this->model()->HasPermission($this->getToken(), $permission)===false) {
            throw new \Spoon\Exception('need permission ['.$permission.']', 403);
        }
        return true;
    }
}
