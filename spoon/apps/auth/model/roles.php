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
     * 根据用户枚举角色列表
     *
     * @param string $workid
     * @return array
     */
    public function listRolesByUser($workid)
    {
        $user=new Users();
        $userid=$user->getId($workid);

        return $this->db()->queryAndFetchAll('select o.id,o.rolename,o.description,o.create_time,o.update_time '.
            'from auth_ref_user_role as r left join auth_roles as o on r.roleid=o.id '.
            'where r.userid=:userid', array(':userid'=>$userid));
    }

    /**
     * 根据权限枚举角色列表
     *
     * @param string $permissionname
     * @return array
     */
    public function listRolesByPermission(string $permissionname)
    {
        $permission=new Permissions();
        $permissionid=$permission->getPermissionID($permissionname);

        return $this->db()->queryAndFetchAll('select o.id,o.rolename,o.description,o.create_time,o.update_time '.
            'from auth_ref_role_permission as r left join auth_roles as o on r.roleid=o.id '.
            'where r.permissionid=:permissionid', array(':permissionid'=>$permissionid));
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

    /**
     * 绑定角色权限
     *
     * @param string $rolename
     * @param string $permissionname
     * @return boolean
     */
    public function assignPermission($rolename, $permissionname)
    {
        $roleid=$this->getRoleID('rolename');
        $permission=new Permissions();
        $permissionid=$permission->getPermissionID($permissionname);
        if (empty($roleid) || empty($permissionid)) {
            return false;
        }
        $data=array(
            // 'roleid'=>$roleid,
            // 'permissionid'=>$permissionid,
            'update_time'=>new \NotORM_Literal('now')
        );
        $effect=$this->db()->ref_role_permission()->insert_update(array('roleid'=>$roleid,'permissionid'=>$permissionid), $data);
        return !empty($effect);
    }

    /**
     * 取消关联权限
     *
     * @param string $rolename
     * @param string $permissionname
     * @return void
     */
    public function unassignPermission($rolename, $permissionname)
    {
        $roleid=$this->getRoleID('rolename');
        $permission=new Permissions();
        $permissionid=$permission->getPermissionID($permissionname);
        if (empty($roleid) || empty($permissionid)) {
            return false;
        }
        $data=array('roleid'=>$roleid,'permissionid'=>$permissionid);
        $effect=$this->db()->ref_role_permission()->where($data)->delete();
        return !empty($effect);
    }
}
