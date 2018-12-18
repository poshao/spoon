<?php
/**
 * 加密算法处理类
 * 
 * 常用算法 MD5,SHA1,BASE64
 */
namespace Spoon;
class Encrypt{

    /**
     * 生成加密字符串
     *
     * @param string $password 密码
     * @param string $salt
     * @return string
     */
    public static function hashPassword($password,$salt=''){
        if(empty($salt))$salt=Config::get('security')['salt'];
        $data=\microtime(true).$password.$salt;
        return \base64_encode(\md5(\uniqid($data).\sha1($data).$salt));
    }


    /**
     * 生成随机码
     *
     * @param string $salt
     * @return boolean
     */
    public static function hashToken($salt=''){
        if(empty($salt))$salt=Config::get('security')['salt'];
        $data=\uniqid($salt.\microtime(true));
        return \base64_encode(\md5($data));
    }
}

?>