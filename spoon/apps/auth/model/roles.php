<?php
namespace App\Auth\Model;

use \Spoon\Exception;

class Roles extends \Spoon\Model
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
     * 获取角色编号
     *
     * @param string $role 角色名称
     * @return int 角色编号
     */
    public function getRoleID($role)
    {
        return $this->db()->roles()->select('id')->where('rolename', $role)->fetch()['id'];
    }

    /**
     * 创建角色
     *
     * @param string $role 角色名称
     * @param string $desc 角色描述
     * @return mixed 角色编号 或 false
     */
    public function create($role, $desc)
    {
        //检查角色名
        $roleid=$this->getRoleID($role);
        if (!empty($roleid)) {
            return false;
        }

        $data=array('rolename'=>$role,'description'=>$desc);
        $row=$this->db()->roles()->insert($data);
        return $row['id'];
    }

    /**
     * 获取角色列表
     *
     * @return array 角色列表
     */
    public function list()
    {
        return $this->db()->roles()->select('id,rolename,description,create_time,update_time')->fetchPairs('id');
    }

    /**
     * 更新角色信息
     *
     * @param int $id 角色编号
     * @param array $info 角色信息
     * @return int 角色编号 或 false
     */
    public function update($id, $info)
    {
        if (empty($info)) {
            return 0;
        }
        $effect=$this->db()->roles()->where('id', $id)->update($info);
        if ($effect===false) {
            return false;
        }
        return $id;
    }
}
