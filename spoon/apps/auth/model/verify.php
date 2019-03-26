<?php
namespace App\Auth\Model;

use \Spoon\Exception;

class Verify extends \Spoon\Model
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
     * 检查是否在线
     *
     * @param string $workid
     * @param string $token
     * @return bool
     */
    public function IsOnline($workid, $token)
    {
        if ($workid===false || $token===false) {
            return false;
        }
        $ip=\Spoon\Util::getIP();
        $userid=$this->db()->users()->select('id')->where('workid', $workid)->fetch()['id'];
        $online=$this->db()->sessions()->where('userid=? and token=? and ip=? and valid_time>now()', $userid, $token, $ip)->count()>0;
        
        if (!$online) {
            return false;
        }
        
        $timeout=\Spoon\Config::getByApps('auth')['token_timeout'];
        $data=array('valid_time'=>new \NotORM_Literal('now()+interval '.$timeout.' second'),'update_time'=>new \NotORM_Literal('now()'));
        $effect=$this->db()->sessions()->where('userid=? and ip=?', $userid, $ip)->update($data);
        return true;
    }

    /**
     * 验证用户权限
     *
     * @param string  $workid
     * @param string $permission
     * @return bool
     */
    public function HasPermission($workid, $permission)
    {
        if ($workid===false) {
            return false;
        }
        $userid=$this->db()->users()->select('id')->where('workid', $workid)->fetch()['id'];
        $rs=$this->db()->queryAndFetchAll('select checkPermission(:userid,:permission)', array(':userid'=>$userid,':permission'=>$permission));
        if ($rs===false) {
            return false;
        }
        return $rs[0][0]>0;
    }
}
