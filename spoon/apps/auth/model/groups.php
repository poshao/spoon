<?php
namespace App\Auth\Model;

use \Spoon\Exception;

class Groups extends \Spoon\Model
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
     * 获取分组编号
     *
     * @param string $group 分组名称
     * @return int 分组编号
     */
    public function getGroupID($group)
    {
        return $this->db()->groups()->select('id')->where('groupname', $group)->fetch()['id'];
    }

    /**
     * 创建分组
     *
     * @param string $group 分组名称
     * @param string $desc 分组描述
     * @param string $rolename 角色名称
     * @return mixed 分组编号 或 false
     */
    public function create($group, $desc, $rolename)
    {
        $groupid=$this->getGroupID($group);
        if (!empty($groupid)) {
            //分组已经创建
            return false;
        }
        $role=new Roles();
        $roleid=$role->getRoleID($rolename);
        if (empty($roleid)) {
            //角色不存在
            return false;
        }

        $data=array('groupname'=>$group,'description'=>$desc,'roleid'=>$roleid);
        $row=$this->db()->groups()->insert($data);
        return $row['id'];
    }

    /**
     * 获取分组列表
     *
     * @return array 分组列表
     */
    public function list()
    {
        return $this->db()->groups()->select('id,groupname,description,create_time,update_time')->fetchPairs('id');
    }

    /**
     * 更新分组信息
     *
     * @param int $id 分组编号
     * @param array $info 分组信息
     * @return int 分组编号 或 false
     */
    public function update($id, $info)
    {
        if (empty($info)) {
            return 0;
        }
        if (isset($info['rolename'])) {
            $role=new Roles();
            $roleid=$role->getRoleID($info['rolename']);
            if (empty($roleid)) {
                return false;
            }
            unset($info['rolename']);
            $info['roleid']=$roleid;
        }
        $effect=$this->db()->groups()->where('id', $id)->update($info);
        if ($effect===false) {
            return false;
        }
        return $id;
    }

    /**
     * 根据用户枚举分组
     *
     * @param string $workid
     * @return array
     */
    public function listGroupsByUser($workid)
    {
        $user=new Users();
        $userid=$user->getId($workid);
        return $this->db()->queryAndFetchAll('select g.id,g.groupname,g.description,g.create_time,g.update_time '.
        'from auth_ref_user_group as r left join auth_groups as g on r.groupid=g.id '.
        'where r.userid=:userid', array(':userid'=>$userid));
    }
}
