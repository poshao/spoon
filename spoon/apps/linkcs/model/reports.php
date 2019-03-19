<?php
namespace App\Linkcs\Model;

use \Spoon\Exception;

class Reports extends \Spoon\Model
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
     * 创建报表结构
     *
     * @param string $workid
     * @param string $name
     * @param string $struct
     * @return boolean
     */
    public function createReport($workid, $name, $struct)
    {
        if (!is_string($struct)) {
            $struct=json_encode($struct);
        }
        $data=array('name'=>$name,'struct'=>$struct,'creator'=>$workid);
        $effect=$this->db()->detail_report()->insert_update(array('name'=>$name), $data);
        return $effect>0;
    }

    /**
     * 删除报表结构
     *
     * @param string $name
     * @return void
     */
    public function deleteReport($name)
    {
        return $this->db()->detail_report()->where('name', $name)->delete();
    }

    /**
     * 枚举所有报表结构
     *
     * @return void
     */
    public function listReports()
    {
        return $this->db()->detail_report()->select('id,name,struct,creator,create_time,update_time')->fetchPairs('id');
    }

    /**
     * 获取报表结构
     *
     * @param string $name
     * @return void
     */
    public function getReportStruct($name)
    {
        return $this->db()->detail_report()->select('id,name,struct,creator,create_time,update_time')->where('name', $name)->fetch();
    }
}
