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
            'valid_time'=>new \NotORM_Literal('now()-interval 10 second'),
            'update_time'=>new \NotORM_Literal('now()')
        );
        $cnt=$this->db()->sessions()->where('userid=? and ip=?', $userid, $ip)->update($data);
        return $cnt>0;
    }
}
