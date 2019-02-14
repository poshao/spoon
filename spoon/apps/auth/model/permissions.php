<?php
namespace App\Auth\Model;

use \Spoon\Exception;

class Permissions extends \Spoon\Model
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
     * 获取权限编号
     *
     * @param string $permission 权限名称
     * @return int 权限编号
     */
    public function getPermissionID($permission)
    {
        return $this->db()->permissions()->select('id')->where('permissionname', $permission)->fetch()['id'];
    }

    /**
     * 创建权限
     *
     * @param string $permission 权限名称
     * @param string $desc 权限描述
     * @return mixed 权限编号 或 false
     */
    public function create($permission, $desc)
    {
        //检查权限名
        $permissionid=$this->getRoleID($permission);
        if (!empty($permissionid)) {
            return false;
        }

        $data=array('permissionname'=>$permission,'description'=>$desc);
        $row=$this->db()->permissions()->insert($data);
        return $row['id'];
    }

    /**
     * 获取权限列表
     *
     * @return array 权限列表
     */
    public function list()
    {
        return $this->db()->permissions()->select('id,permissionname,description,create_time,update_time')->fetchPairs('id');
    }

    /**
     * 根据角色枚举权限
     *
     * @param string $rolename
     * @return array
     */
    public function listPermissionsByRole($rolename)
    {
        $role=new Roles();
        $roleid=$role->getRoleID($rolename);
        return $this->db()->queryAndFetchAll('select p.id,p.permissionname,p.description,p.create_time,p.update_time '.
            'from auth_ref_role_permission as r left join auth_permissions as p on r.permissionid=p.id '.
            'where r.roleid=:roleid', array(':roleid'=>$roleid));
    }

    /**
     * 更新权限信息
     *
     * @param int $id 权限编号
     * @param array $info 权限信息
     * @return int 权限编号 或 false
     */
    public function update($id, $info)
    {
        if (empty($info)) {
            return 0;
        }
        $effect=$this->db()->permissions()->where('id', $id)->update($info);
        if ($effect===false) {
            return false;
        }
        return $id;
    }
}
