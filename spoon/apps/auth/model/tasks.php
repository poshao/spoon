<?php
namespace App\Auth\Model;

use \Spoon\Exception;

class Tasks extends \Spoon\Model
{
    protected $_db=null;

    /**
     * 获取当前数据库实例
     *
     * @return void
     */
    public function db()
    {
        if (empty($this->_db)) {
            $this->_db=self::getORM(\Spoon\Config::getByApps('auth')['db']);
        }
        return $this->_db;
    }
    /**
     * 密码重置申请
     *
     * @param string $workid
     * @return array
     */
    public function resetPasswordByEmailRequest($workid)
    {
        $db=$this->db();
        $user=new Users();
        $userid=$user->getId($workid);
        if (empty($userid)) {
            return false;// 用户不存在
        } else {
            $db->tasks()->where('create_time<now()-interval 10 MINUTE')->delete();
            $rs=$db->tasks()->select('create_time')->where('userid', $userid)->fetch();
            if (empty($rs)) {
                // 新申请
                $code=\Spoon\Encrypt::RandomCode(6);
                $email=$user->getUser($workid, 'email')['email'];
                if (empty($email)) {
                    return false;//邮件地址无效
                } else {
                    $data=array(
                      'userid'=>$userid,
                      'code'=>$code,
                    );
                    $effect=$db->tasks()->insert($data);
                    if ($effect=0) {
                        return false;
                    }
                    return array('email'=>$email,'code'=>$code);
                }
            } else {
                // 重置过程中
                return false;
                // $error='in process; last request: '.$rs['create_time'];
            }
        }
    }

    /**
     * 重置密码确认
     *
     * @param string $workid
     * @param string $code
     * @param string $password
     * @return boolean
     */
    public function resetPasswordByEmailConfirm($workid, $code, $password)
    {
        $db=$this->db();
        $user=new Users();
        $userid=$user->getId($workid);
        if (empty($userid)) {
            return false;
        }
        $rs=$db->tasks()->select('code')->where('create_time>now()-interval 10 MINUTE and userid=?', $userid)->fetch();
        if (empty($rs)) {
            return false;
        }
        return $user->resetPassword($workid, $password);
    }
}
