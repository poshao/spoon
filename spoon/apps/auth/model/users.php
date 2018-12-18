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
     * 获取用户ID
     *
     * @param string $workid 用户编号
     * @return int
     */
    public function getId($workid){
        return $this->db()->users()->select('id')->where('workid',$workid)->fetch()['id'];
    }

    /**
     * 创建用户
     *
     * @param string $workid 工号
     * @param string $password 密码
     * @return int 用户工号 或 false
     */
    public function create($workid,$password){
        $id=$this->getId($workid);
        if(!empty($id)) return false;

        $data=array('workid'=>$workid,'password'=>$password);
        $row=$this->db()->users()->insert($data);
        return $row['id'];
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