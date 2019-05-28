<?php
namespace App\Auth\Model;

use \Spoon\Exception;

class Tokens extends \Spoon\Model
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
     * 用户登录
     *
     * @param string $workid
     * @param string $password
     * @return string
     */
    public function login($workid, $password)
    {
        //验证用户
        $user=new Users();
        $userid=$user->getId($workid);
        $cnt=$this->db()->users()->where(array('id'=>$userid,'password'=>$password))->count();
        if ($cnt<1) {
            return false;
        }
        
        //创建登录缓存
        $token=\Spoon\Encrypt::hashToken();
        $ip=\Spoon\Util::getIP();
        $timeout=\Spoon\Config::getByApps('auth')['token_timeout'];
        $data=array(
            // 'userid'=>$userid,
            'token'=>$token,
            // 'ip'=>$ip,
            'valid_time'=>new \NotORM_Literal('now()+interval '.$timeout.' second'),
            'update_time'=>new \NotORM_Literal('now()')
        );

        $cnt=$this->db()->sessions()->insert_update(array('userid'=>$userid,'ip'=>$ip), $data);
        if ($cnt) {
            return $token;
        }
        return false;
    }

    /**
     * 用户注销
     *
     * @param string $workid
     * @return bool
     */
    public function logout($workid)
    {
        //验证用户
        $user=new Users();
        $userid=$user->getId($workid);

        $ip=\Spoon\Util::getIP();
        $data=array(
            'token'=>'',
            'socketid'=>'',
            'valid_time'=>new \NotORM_Literal('now()-interval 10 second'),
            'update_time'=>new \NotORM_Literal('now()')
        );
        $cnt=$this->db()->sessions()->where('userid=? and ip=?', $userid, $ip)->update($data);
        return $cnt>0;
    }

    /**
     * 强制注销(注销用户的所有登录)
     *
     * @param string $workid
     * @return void
     */
    public function forceLogout($workid)
    {
        $user=new Users();
        $userid=$user->getId($workid);

        $data=array(
            'token'=>'',
            'socketid'=>'',
            'valid_time'=>new \NotORM_Literal('now()-interval 10 second'),
            'update_time'=>new \NotORM_Literal('now()')
        );
        $cnt=$this->db()->sessions()->where('userid=?', $userid)->update($data);
        return $cnt>0;
    }

    /**
     * 更新SocketId
     *
     * @param string $workid
     * @param string $socketid
     * @return void
     */
    public function updateSocketId($workid,$socketid){
        $user=new Users();
        $userid=$user->getId($workid);

        $ip=\Spoon\Util::getIP();

        $data=array(
            'socketid'=>$socketid,
            'update_time'=>new \NotORM_Literal('now()')
        );
        $cnt=$this->db()->sessions()->where('userid=? and ip=?', $userid,$ip)->update($data);
        return $cnt>0;
    }

    /**
     * 获取用户的WebsocketID
     *
     * @param string $workid
     * @return void
     */
    public function getSocketId($workid){
        $user=new Users();
        $userid=$user->getId($workid);
        $socketids=$this->db()->sessions()->select('id,socketid')->where('userid',$userid)->fetchPairs('id');
        if($socketids===false){
            return false;
        }
        $result=array();
        foreach ($socketids as $k => $v) {
            \array_push($result,$v['socketid']);
        }
        return $result;
    }
}
