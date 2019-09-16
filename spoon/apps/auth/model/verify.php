<?php
namespace App\Auth\Model;

use \Spoon\Exception;

class Verify extends \Spoon\Model
{
    protected $_db=null;

    protected $_last_userid='';
    protected $_last_token='';

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
     * 获取用户ID
     *
     * @param string $token
     * @return mixed boolean|string
     */
    public function GetUserId($token)
    {
        if (empty($token)) {
            return false;
        }

        if ($this->_last_token!==$token || empty($this->_last_userid)) {
            $ip=\Spoon\Util::getIP();
            $row=$this->db()->sessions()->select('userid')->where('token=? and ip=? and valid_time>now()', $token, $ip)->fetch();
            if ($row===false) {
                return false;
            }
            $this->_last_token=$token;
            $this->_last_userid=$row['userid'];
        }
        return $this->_last_userid;
    }

    /**
     * 判断用户权限
     *
     * @param string $token
     * @param string $permission
     * @return void
     */
    public function HasPermission($token, $permission)
    {
        $userid=$this->GetUserId($token);
        $rs=$this->db()->queryAndFetchAll('select checkPermission(:userid,:permission)', array(':userid'=>$userid,':permission'=>$permission), \PDO::FETCH_NUM);
        if ($rs===false) {
            return false;
        }
        return $rs[0][0]>0;
    }

    /**
     * 检查是否在线
     *
     * @param string $token
     * @return bool
     */
    public function IsOnline($token)
    {
        $ip=\Spoon\Util::getIP();
        // $userid=$this->db()->users()->select('id')->where('workid', $workid)->fetch()['id'];
        // $online=$this->db()->sessions()->where('userid=? and token=? and ip=? and valid_time>now()', $userid, $token, $ip)->count()>0;
        
        // offline
        if (empty($this->GetUserId($token))) {
            return false;
        }
        
        // 延长有效期
        $timeout=\Spoon\Config::getByApps('auth')['token_timeout'];
        $data=array('valid_time'=>new \NotORM_Literal('now()+interval '.$timeout.' second'),'update_time'=>new \NotORM_Literal('now()'));
        $effect=$this->db()->sessions()->where('token=?', $token)->update($data);
        return true;
    }

    public function GetWorkId($token)
    {
        $userid=$this->GetUserId($token);
        return $this->db()->users()->select('workid')->where('id', $userid)->fetch()['workid'];
    }
    // /**
    //  * 验证用户权限
    //  *
    //  * @param string  $workid
    //  * @param string $permission
    //  * @return bool
    //  */
    // public function HasPermission($workid, $permission)
    // {
    //     if ($workid===false) {
    //         return false;
    //     }
    //     $userid=$this->db()->users()->select('id')->where('workid', $workid)->fetch()['id'];
    //     $rs=$this->db()->queryAndFetchAll('select checkPermission(:userid,:permission)', array(':userid'=>$userid,':permission'=>$permission));
    //     if ($rs===false) {
    //         return false;
    //     }
    //     return $rs[0][0]>0;
    // }
}
