<?php
namespace App\Linkcs\Model;

use \Spoon\Exception;

class Detail extends \Spoon\Model
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
     * 增加新纪录
     *
     * @param [string] $workid
     * @param [JSON] $detail
     * @return void
     */
    public function newRequest($workid, $detail)
    {
        if (is_string($detail)===false) {
            $detail=\json_encode($detail);
        }
        $data=array('creator'=>$workid,'json_detail'=>$detail);
        $row=$this->db()->detail()->insert($data);
        return $row['id'];
    }

    /**
     * 获取订单列表
     *
     * @return array
     */
    public function list()
    {
        return $this->db()->detail()->select('id,dnei,json_detail,creator,create_time,level')->fetchPairs('id');
    }
}
