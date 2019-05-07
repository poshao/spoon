<?php
namespace App\Common\Model;

use \Spoon\Exception;

class stores extends \Spoon\Model
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
            $this->_db=self::getORM(\Spoon\Config::getByApps('common')['db']);
        }
        return $this->_db;
    }

    /**
     * 获取类别ID
     *
     * @param string $category
     * @return void
     */
    public function getCategoryId($category)
    {
        return $this->db()->file_categories()->select('id')->where('name', $category)->fetch()['id'];
    }

    /**
     * 获取存档目录
     *
     * @return void
     */
    public function getStoreDir()
    {
        return \realpath(\Spoon\Config::getByApps('common')['store_path']);
    }

    /**
     * 添加文件
     *
     * @param string $filepath
     * @param string $filename
     * @param string $workid
     * @param string $category
     * @return string hashname
     */
    public function addFile($filepath, $filename, $workid, $category='unknown')
    {
        // 确定分类
        $categoryid=$this->getCategoryId($category);
        if (empty($categoryid)) {
            return false;
        }

        $hashname=\str_replace('.', '', \uniqid("", true));
        $folder=\date('Ym');//相对目录
        $folderPath=$this->getStoreDir().'/'.$folder.'/';
        $dest_filepath=$folderPath.$hashname;

        if (!\file_exists($folderPath)) {
            \mkdir($folderPath);
        }
        
        if (!@\copy($filepath, $dest_filepath)) {
            return false;
        }

        $data=array(
            'categoryid'=>$categoryid,
            'hashname'=>$hashname,
            'owner'=>$workid,
            'folder'=>$folder,
            'origin_name'=>$filename,
            'last_user'=>$workid,
            'last_time'=>new \NotORM_Literal('now()')
        );

        $row=$this->db()->files()->insert($data);
        if ($row===false) {
            \unlink($dest_filepath);
            return false;
        }
        return $hashname;
    }

    /**
     * 获取文件
     *
     * @param string $hashname
     * @return void
     */
    public function getFile($hashname)
    {
        $row=$this->db()->files()->select('folder,origin_name')->where('hashname', $hashname)->fetch();
        if ($row===false) {
            return false;
        }

        $filename=$row['origin_name'];
        $path=$this->getStoreDir().'/'.$row['folder'].'/'.$hashname;
        return array('filename'=>$filename,'path'=>$path);
    }

    /**
     * 删除文件
     *
     * @param string $hashname
     * @return void
     */
    public function delFile($hashname)
    {
        $row=$this->db()->files()->select('folder,origin_name')->where('hashname', $hashname)->fetch();
        if ($row===false) {
            return false;
        }

        $path=$this->getStoreDir().'/'.$row['folder'].'/'.$hashname;
        if (!\unlink($path)) {
            return false;
        }
        return $row->delete()>0;
    }
}
