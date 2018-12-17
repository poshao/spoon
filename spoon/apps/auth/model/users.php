<?php
namespace App\Auth\Model;
use \Spoon\Exception;

class Users extends \Spoon\Model{
    protected $_db=null;

    /**
     * 获取当前数据库实例
     *
     * @return void
     */
    public function db(){
        if(empty($this->_db)){
            $this->_db=self::getORM(\Spoon\Config::getByApps('auth')['db']);
        }
        return $this->_db;
    }

    /**
     * 检查用户是否存在
     *
     * @param int $userid 用户编号
     * @return bool
     */
    public function exists($userid){
        return $this->db()->users()->where('id',$userid)->count()>0;
    }

    /**
     * 获取用户信息
     *
     * @return void
     */
    public function getUser(){
        return $this->db()->users()->select('*')->where('id',1)->fetch();
    }
}

?>