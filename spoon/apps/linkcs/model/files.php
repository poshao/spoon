<?php
namespace App\Linkcs\Model;

use \Spoon\Exception;

class Files extends \Spoon\Model
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
            $this->_db=self::getORM(\Spoon\Config::getByApps('linkcs')['db']);
        }
        return $this->_db;
    }

    /**
     * 获取临时目录
     *
     * @return void
     */
    public function getTempDir($workid)
    {
        return \Spoon\Config::getByApps('linkcs')['temp_dir'].'/'.$workid.'/';
    }

    /**
     * 获取存档目录
     */
    public function getStoreDir()
    {
        return \Spoon\Config::getByApps('linkcs')['data_dir'].'/';
    }

    /**
     * 添加一个文件
     *
     * @param string $workid
     * @param object $file
     * @return mixed
     */
    public function addFile($workid, $file)
    {
        $dir=$this->getTempDir($workid);
        $path=$dir.$file['name'];
        $path=iconv('utf-8', 'gbk', $path);

        if (!\is_dir($dir)) {
            @\mkdir($dir, 0777, true);
        }

        if (@\copy($file['tmp_name'], $path)) {
            return $file['name'];
        }
        return false;
    }

    /**
     * 获取用户文件列表
     *
     * @param string $workid
     * @return array
     */
    public function getFileList($workid)
    {
        $dir=$this->getTempDir($workid);
        $list=array();
        if (is_dir($dir)===false) {
            return $list;
        }
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file!=='.' && $file!=='..') {
                    array_push($list, array('name'=>iconv('gbk', 'utf-8', $file)));
                    // echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
                }
            }
            closedir($dh);
        }
        return $list;
    }

    /**
     * 获取文件
     *
     * @param string $hashname
     * @return void
     */
    public function getFile($filename, $hashname)
    {
        $realPath=$this->getStoreDir().'/'.iconv('utf-8', 'gbk', $hashname);
        if (!file_exists($realPath)) {
            return false;
        }
        return array('path'=>$realPath,'name'=>$filename);
    }

    /**
     * 获取文件(用户临时文件)
     *
     * @param string $workid
     * @param string $name
     * @return array
     */
    public function getUploadedFile($workid, $name)
    {
        $dir=$this->getTempDir($workid);
        $path=$dir.'/'.iconv('utf-8', 'gbk', $name);
        if (is_file($path)===false) {
            return false;
        }
        return array('path'=>$path,'name'=>$name);
    }

    /**
     * 移除一个文件
     *
     * @param string $workid
     * @param string $name
     * @return boolean
     */
    public function removeFile($workid, $name)
    {
        $dir=$this->getTempDir($workid);
        if (is_dir($dir)===false) {
            return true;
        }
        $path=$dir.iconv('utf-8', 'gbk', $name);
        if (!is_file($path)) {
            return true;
        }
        return @\unlink($path);
    }

    /**
     * 提交所有文件
     *
     * @param string $workid
     * @return boolean
     */
    public function commit($workid)
    {
    }
}