<?php
namespace App\Linkcs\Model;

use \Spoon\Exception;

class Status extends \Spoon\Model
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
    //code

    public function getDailyOrderCount($days=7)
    {
        $rs=$this->db()->queryAndFetchAll('select date_format(create_time,\'%Y-%m-%d\')as dt,count(*) as cnt from cs_orders group by dt desc limit '.$days.';');
        return $rs;
    }
}
