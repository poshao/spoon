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

        if (!\is_dir($dir)) {
            @\mkdir($dir, 0777, true);
        }

        if (@\copy($file['tmp_name'], $path)) {
            return $file['name'];
        }
        return false;
    }

    /**
     * 清空用户文件夹
     *
     * @param string $workid
     * @return void
     */
    public function clearUserFolder($workid)
    {
        $userFolder=$this->getTempDir($workid);
        $this->rrmdir($userFolder);
        \mkdir($userFolder, 0777, true);
    }

    /**
     * 删除目录
     *
     * @param string $dir
     * @return void
     */
    private function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        rrmdir($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /**
     * 撤回用户文件夹
     *
     * @param string $workid
     * @param string $hashname
     * @param string $filename
     * @return bool
     */
    public function revokeFileToUserFolder($workid, $hashname, $filename)
    {
        $srcPath=$this->getStoreDir().$hashname;
        $destPath=$this->getTempDir($workid).$filename;
        \Spoon\Logger::error($srcPath);
        return \copy($srcPath, $destPath);
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
                    // array_push($list, array('name'=> $file));
                    array_push($list, array('name'=>$file));
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
     * @param string $filename
     * @param string $hashname
     * @return void
     */
    public function getFile($filename, $hashname)
    {
        $realPath=$this->getStoreDir().'/'.$hashname;
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
        $path=$dir.'/'.$name;
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
        $path=$dir.$name;
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
