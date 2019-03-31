<?php
namespace App\Vba\Model;

use \Spoon\Exception;

class Addins extends \Spoon\Model
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
     * 获取加载项ID
     *
     * @param string $name
     * @param string $version
     * @return void
     */
    public function getAddinId($name, $version)
    {
        return $this->db()->addins()->select('id')->where(array('addin_name'=>$name,'version'=>$version))->fetch()['id'];
    }


    /**
     * 获取存储路径
     *
     * @param string $filename
     * @return void
     */
    public function getStoreDir($filename)
    {
        return \Spoon\Config::getByApps('vba')['store_dir'].DIRECTORY_SEPARATOR.$filename;
    }

    /**
     * 枚举所有加载项
     *
     * @return array
     */
    public function listAddins()
    {
        return $this->db()->addins()->select('id,addin_name,version,hashname,create_time,update_time')->fetchPairs('id');
    }

    /**
     * 上传加载项
     *
     * @param string $name
     * @param string $version
     * @param string $description
     * @param string $filePath
     * @return object -1:重名 -2:文件复制失败 | 行记录
     */
    public function uploadAddin($name, $version, $description, $filePath)
    {
        $addinid=$this->getAddinId($name, $version);
        if (!empty($addinid)) {
            //重名异常
            return -1;
        }
        $hashname=\uniqid('vba');

        if (\copy($filePath, $this->getStoreDir($hashname))===false) {
            //文件复制失败
            return -2;
        }
        $data=array(
            'name'=>$name,
            'version'=>$version,
            'description'=>$description,
            'hashname'=>$hashname,
            'create_time'=>new \NotORM_Literal('now()')
        );
        return $this->db()->addins()->insert($data);
    }

    /**
     * 获取加载项文件信息
     *
     * @param string $addinid
     * @return void
     */
    public function getAddinFileinfo($addinid)
    {
        $row=$this->db()->addins()->select('id,name,version')->where('id', $addinid)->fetch();
        if ($row===false) {
            return false;
        }
        $filepath=$this->getStoreDir($row['hashname']);
        if (!file_exists($filePath)) {
            return false;
        }
        return array('path'=>$filePath,'name'=>$row['name'].'_'.$row['version'].'.xla');
    }

    /**
     * 获取用户加载项列表
     *
     * @param string $loginname
     * @return void
     */
    public function listAddinsByUser($loginname)
    {
        $user=new Users();
        $userid=$user->getUserID($loginname);

        $addins=$this->db()->ref_user_addin()->select('addinid')->where('userid', $userid);
        return $this->db()->addins()->select('id,addin_name,version,hashname,create_time,update_time')->where('id', $addins)->fetchPairs('id');
    }

    /**
     * 升级加载项
     *
     * @param string $addinname
     * @param string $version
     * @return boolean
     */
    public function upgradeAddin($addinname, $version)
    {
        $addinid=$this->getAddinId($addinname, $version);

        $addins=$this->db()->addins()->select('id')->where('addin_name', $addinname);
        $data=array('addinid'=>$addinid,'update_time'=>new \NotORM_Literal('now()'));
        $effect=$this->db()->ref_user_addin()->where('addinid', $addins)->update($data);
        return !empty($effect);
    }
}
