<?php
namespace App\Common\Model;

use \Spoon\Exception;

class Printers extends \Spoon\Model
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
    //...
}
