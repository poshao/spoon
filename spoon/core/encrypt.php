<?php
/**
 * 加密算法处理类
 *
 * 常用算法 MD5,SHA1,BASE64
 */
namespace Spoon;

class Encrypt
{

    /**
     * 生成加密字符串
     *
     * @param string $password 密码
     * @param string $salt
     * @return string
     */
    public static function hashPassword($password, $salt='')
    {
        if (empty($salt)) {
            $salt=Config::get('security')['salt'];
        }
        $data='helloworld'.$password.$salt;
        return \base64_encode(\md5($salt.\md5($data).$salt.\sha1($data).$salt));
    }


    /**
     * 生成随机码
     *
     * @param string $salt
     * @return boolean
     */
    public static function hashToken($salt='')
    {
        if (empty($salt)) {
            $salt=Config::get('security')['salt'];
        }
        $data=\uniqid($salt.\microtime(true));
        return \base64_encode(\md5($data));
    }

    /**
     * 生成指定长度的验证码
     *
     * @param int $len
     * @return void
     */
    public static function RandomCode(int $len,string $charlist=""){
        if(empty($charlist)){
            $charlist='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        }
        $rlst='';
        \mt_srand();
        for($i=0;$i<$len;$i++){
            $rlst.=$charlist[\mt_rand(0, \strlen($charlist)-1)];
        }
        return $rlst;
    }
}
