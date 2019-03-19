<?php
namespace App\Auth\Model;

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
    public function getId($workid)
    {
        return $this->db()->users()->select('id')->where('workid', $workid)->fetch()['id'];
    }

    /**
     * 创建用户
     *
     * @param string $workid 工号
     * @param string $password 密码
     * @return int 用户编号 或 false
     */
    public function create($workid, $password)
    {
        $id=$this->getId($workid);
        if (!empty($id)) {
            return false;
        }

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
    public function update($workid, $info)
    {
        if (empty($info)) {
            return 0;
        }
        $id=$this->getId($workid);

        $effect=$this->db()->users()->where('id', $id)->update($info);
        if ($effect===false) {
            return false;
        }

        return $id;
    }

    /**
     * 获取用户信息
     *
     * @param string $workid
     * @param array $fields 字段名称
     * @return array
     */
    public function getUser($workid, $fields=null)
    {
        if ($fields===null) {
            $fields='id,workid,username,depart,avator,email,phone';
        }
        $id=$this->getId($workid);
        $row=$this->db()->users()->select($fields)->where('id', $id)->fetch();
        return $row;
    }

    /**
     * 获取用户清单
     *
     * @return void
     */
    public function listUsers()
    {
        return $this->db()->users()->select('id,workid,username,depart,create_time,update_time')->fetchPairs('id');
    }

    /**
     * 修改密码
     *
     * @param string $old
     * @param string $new
     * @return boolean
     */
    public function changePassword($workid, $old, $new)
    {
        $userid=$this->getId($workid);
        //update users set password=$new where workid=$workid and password=$old
        $effect=$this->db()->users()->where(array('password'=>$old,'id'=>$userid))->update(array('password'=>$new));
        if ($effect===false) {
            return false;
        } else {
            //强制注销
            $token=new Tokens();
            return $token->logout($workid);
        }
    }

    /**
     * 关联用户分组
     *
     * @param string $workid
     * @param string $groupname
     * @return boolean
     */
    public function assignGroup($workid, $groupname)
    {
        $group=new Groups();
        $groupid=$group->getGroupID($groupname);
        $userid=$this->getId($workid);
        if (empty($groupid) || empty($userid)) {
            return false;
        }
        $data=array(
            'userid'=>$userid,
            'groupid'=>$groupid,
            'update_time'=>new \NotORM_Literal('now')
        );
        $effect=$this->db()->ref_user_group()->insert_update(array('userid'=>$userid,'groupid'=>$groupid,), $data);
        return !empty($effect);
    }

    /**
     * 取消关联用户分组
     *
     * @param string $workid
     * @param string $groupname
     * @return void
     */
    public function unassignGroup($workid, $groupname)
    {
        $group=new Groups();
        $groupid=$group->getGroupID($groupname);
        $userid=$this->getId($workid);
        if (empty($groupid) || empty($userid)) {
            return false;
        }
        $data=array('userid'=>$userid,'groupid'=>$groupid);
        $effect=$this->db()->ref_user_group()->where($data)->delete();
        return !empty($effect);
    }

    /**
     * 关联用户角色
     *
     * @param string $workid
     * @param string $rolename
     * @return false
     */
    public function assignRole($workid, $rolename)
    {
        $role=new Roles();
        $roleid=$role->getRoleID($rolename);
        $userid=$this->getId($workid);
        if (empty($roleid) || empty($userid)) {
            return false;
        }

        $data=array('update_time'=>new \NotORM_Literal('now()'));
        $effect=$this->db()->ref_user_role()->insert_update(array('userid'=>$userid,'roleid'=>$roleid), $data);
        return !empty($effect);
    }

    /**
     * 取消关联用户角色
     *
     * @param string $workid
     * @param string $rolename
     * @return false
     */
    public function unassignRole($workid, $rolename)
    {
        $role=new Roles();
        $roleid=$role->getRoleID($rolename);
        $userid=$this->getId($workid);
        if (empty($roleid) || empty($userid)) {
            return false;
        }

        $data=array('userid'=>$userid,'roleid'=>$roleid);
        $effect=$this->db()->ref_user_role()->where($data)->delete();
        return !empty($effect);
    }

    /**
     * 根据角色名枚举所有用户
     *
     * @param string $rolename
     * @return void
     */
    public function listUsersByRole($rolename)
    {
        $role=new Roles();
        $roleid=$role->getRoleID($rolename);
        $users=$this->db()->ref_user_role()->select('userid')->where('roleid', $roleid);
        return $this->db()->users()->select('id,workid,username,depart,create_time,update_time')->where('id', $users)->fetchPairs('id');
    }
}
