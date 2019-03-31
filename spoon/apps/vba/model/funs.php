<?php
namespace App\Vba\Model;

use \Spoon\Exception;

class Funs extends \Spoon\Model
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
     * 根据加载项枚举子功能
     *
     * @param string $addinname
     * @return void
     */
    public function listFunsByAddin($addinname)
    {
        return $this->db()->funs()->select('id,fun_name,addin_name,description')->where('addin_name', $addinname)->fetchPairs('id');
    }

    /**
     * 根据用户及加载项枚举子功能
     *
     * @param string $loginname
     * @param string $addinname
     * @return void
     */
    public function listFunsByUserAddin($loginname, $addinname)
    {
        $users=new Users();
        $userid=$users->getUserID($loginname);
        $funs=$this->db()->ref_user_fun()->select('funid')->where('userid', $userid);
        $filter=array('addin_name'=>$addinname,'id'=>$funs);
        return $this->db()->funs()->select('id,fun_name,addin_name,description')->where($filter)->fetchPairs('id');
    }
}
