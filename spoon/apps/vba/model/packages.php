<?php
namespace App\Vba\Model;

use \Spoon\Exception;

class Packages extends \Spoon\Model
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
            $this->_db=self::getORM(\Spoon\Config::getByApps('vba')['db']);
        }
        return $this->_db;
    }

    /**
     * 获取文件存档路径
     *
     * @return string
     */
    public function getStoreDir(){
      return \Spoon\Config::getByApps('vba')['package_dir'].'/';
    }

    /**
     * 获取包ID
     *
     * @param string $name
     * @param string $version
     * @return void
     */
    public function getPackageID($name,$version){
      return $this->db()->packages()->select('id')->where(array('name'=>$name,'version'=>$version))->fetch()['id'];
    }

    /**
     * 上传组件包
     *
     * @param string $name
     * @param string $version
     * @param string $author
     * @param string $description
     * @param object $file
     * @return void
     */
    public function uploadPackage($name,$version,$author,$description,$file){
      $packageid=$this->getPackageID($name,$version);
      if(!empty($packageid)){
        return false;
      }

      $dir=$this->getStoreDir();
      $hashname=\uniqid('vba_');
      if (!\is_dir($dir)) {
          @\mkdir($dir, 0777, true);
      }

      if (!@\copy($file['tmp_name'], $dir.$hashname)) {
          return false;
      }

      $data=array(
        'name'=>$name,
        'version'=>$version,
        'hashname'=>$hashname
      );
      $row=$this->db()->packages()->insert($data);
      return $row['id'];
    }

    /**
     * 获取下载列表
     *
     * @param string $search
     * @return void
     */
    public function listPackages($search){
      $row = $this->db()->packages()->select('id,name,version,author,description');
      if(!empty($search)){
        $row->where('name like ?','%'.$search.'%');
      }
      return $row->fetchPairs('id');
    }

    /**
     * 下载组件包
     *
     * @param string $name
     * @param string $version
     * @return void
     */
    public function downloadPackage($name,$version){
      $hashname=$this->db()->select('hashname')->where(array('name'=>$name,'version'=>$version))->fetch()['hashname'];
      $realPath=$this->getStoreDir().$hashname;
      return array('path'=>$realPath,'name'=>$name.'_'.$version.'.zip');
    }
}
