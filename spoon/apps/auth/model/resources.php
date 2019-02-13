<?php
namespace App\Auth\Model;

use \Spoon\Exception;

class Resources extends \Spoon\Model
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
     * 获取用户头像
     *
     * @param string $workid
     * @return void
     */
    public function getAvator($workid)
    {
        $row=$this->db()->users()->select('avator')->where('workid', $workid)->fetch();
        if ($row) {
            if (empty($row['avator'])) {
                return \Spoon\Config::getByApps('auth')['avator']['dir'].'/default';
            } else {
                return \Spoon\Config::getByApps('auth')['avator']['dir'].'/'.$row['avator'];
            }
        }
        return false;
    }

    /**
     * 更新用户头像
     *
     * @param [string] $workid
     * @param [string] $file
     * @return void
     */
    public function updateAvator($workid, $filename)
    {
        $cfg=\Spoon\Config::getByApps('auth')['avator'];

        //检查处理图像
        $info=\getimagesize($filename);
        if (!$info) {
            return false;
        }

        //将文件载入到资源变量im中
        switch ($info[2]) { //1-GIF，2-JPG，3-PNG
        case 1:
            $im = imagecreatefromgif($filename);
            break;
            
        case 2:
            $im = imagecreatefromjpeg($filename);
            break;
              
        case 3:
            $im = imagecreatefrompng($filename);
            break;
        }

        $avatorSize=$cfg['size'];
        $filename=md5(uniqid().\mktime(true));
        $folder=$cfg['dir'];

        $ni=\imagecreatetruecolor($avatorSize, $avatorSize);
        \imagecopyresampled($ni, $im, 0, 0, 0, 0, $avatorSize, $avatorSize, \imagesx($im), \imagesy($im));
        \imagepng($ni, $folder.'/'.$filename);
        \imagedestroy($ni);
        \imagedestroy($im);

        //更新数据库
        $row=$this->db()->users()->select('avator')->where('workid', $workid)->fetch();
        if ($row && !empty($row['avator'])) {
            @unlink($folder.'/'.$row['avator']);
        }
        $effect=$this->db()->users()->where('workid', $workid)->update(array('avator'=>$filename));
        if ($effect===false) {
            return false;
        }
        return $filename;
    }
}
