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
     * @return int 用户编号 或 false
     */
    public function create($workid,$password){
        $id=$this->getId($workid);
        if(!empty($id)) return false;

        $data=array('workid'=>$workid,'password'=>$password);
        $row=$this->db()->users()->insert($data);
        return $row['id'];
    }

    /**
     * 更新用户信息
     *
     * @param string $workid
     * @param array $info
     * @return int 用户编号 或 false
     */
    public function update($workid,$info){
        $id=$this->getId($workid);

        $effect=$this->db()->users()->where('id',$id)->update($info);
        if($effect===false) return false;

        return $id;
    }

    /**
     * 获取用户信息
     *
     * @param string $workid
     * @param array $fields 字段名称
     * @return array
     */
    public function getUser($workid,$fields=null){
        if($fields===null){
            $fields='id,workid,username,depart';
        }
        $id=$this->getId($workid);
        $row=$this->db()->users()->select($fields)->where('id',$id)->fetch();
        return $row;
    }

    /**
     * 获取用户清单
     *
     * @return void
     */
    public function listUsers(){
        return $this->db()->users()->select('id,workid')->fetchPairs('id');
    }
}

?>