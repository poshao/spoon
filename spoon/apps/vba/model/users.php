<?php
namespace App\Vba\Model;

use \Spoon\Exception;

class Users extends \Spoon\Model
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
            $this->_db=self::getORM(\Spoon\Config::getByApps('vba')['db']);
        }
        return $this->_db;
    }
    
    /**
     * 获取用户ID
     *
     * @param string $loginname
     * @return void
     */
    public function getUserID($loginname)
    {
        return $this->db()->users()->select('id')->where('loginname', $loginname)->fetch()['id'];
    }

    /**
     * 注册用户
     *
     * @param string $loginname 系统登录名
     * @param string $username 英文名
     * @return void
     */
    public function register($loginname, $username)
    {
        $data=array('loginname'=>$loginname,'username'=>$username,'update_time'=>new \NotORM_Literal('now()'));
        $effect=$this->db()->users()->insert_update(array('loginname'=>$loginname), $data);
        return !empty($effect);
    }

    /**
     * 授权加载项
     *
     * @param string $loginname
     * @param string $addinid
     * @return boolean
     */
    public function assignAddin($loginname, $addinid)
    {
        $user=new Users();
        $userid=$user->getUserID($loginname);
        $data=array('userid'=>$userid,'addinid'=>$addinid,'update_time'=>new \NotORM_Literal('now()'));
        $effect=$this->db()->ref_user_addin()->insert_update(array('userid'=>$userid,'addinid'=>$addinid), $data);
        return !empty($effect);
    }

    /**
     * 取消加载项授权
     *
     * @param string $loginname
     * @param string $addinid
     * @return boolean
     */
    public function unassignAddin($loginname, $addinid)
    {
        $user=new Users();
        $userid=$user->getUserID($loginname);
        $data=array('userid'=>$userid,'addinid'=>$addinid);
        $effect=$this->db()->ref_user_addin()->where($data)->delete();
        return !empty($effect);
    }

    /**
     * 授权用户功能块
     *
     * @param string $loginname
     * @param string $funid
     * @return boolean
     */
    public function assignFun($loginname, $funid)
    {
        $user=new Users();
        $userid=$user->getUserID($loginname);
        $data=array('userid'=>$userid,'funid'=>$funid,'update_time'=>new \NotORM_Literal('now()'));
        $effect=$this->db()->ref_user_fun()->insert_update(array('userid'=>$userid,'funid'=>$funid), $data);
        return !empty($effect);
    }

    /**
     * 取消授权用户功能块
     *
     * @param string $loginname
     * @param string $funid
     * @return boolean
     */
    public function unassignFun($loginname, $funid)
    {
        $user=new Users();
        $userid=$user->getUserID($loginname);
        $data=array('userid'=>$userid,'funid'=>$funid);
        $effect=$this->db()->ref_user_fun()->where($data)->delete();
        return !empty($effect);
    }

    /**
     * 枚举用户列表
     *
     * @return void
     */
    public function listUsers()
    {
        return $this->db()->users()->select('id,loginname,username,create_time,update_time')->fetchPairs('id');
    }
}
