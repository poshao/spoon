<?php
namespace App\Linkcs\Model;

use \Spoon\Exception;

class Orders extends \Spoon\Model
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
        //处理附件
        $files=new Files();
        $storeDir=$files->getStoreDir();
        $tempDir=$files->getTempDir($workid);
        $detail['files']= array();
        $list=$files->getFileList($workid);
        foreach ($list as $k=>$v) {
            $path=$tempDir.iconv('utf-8', 'gb2312', $v['name']);
            $hashname=\uniqid('file');
            if (rename($path, $storeDir.$hashname)) {
                array_push($detail['files'], array('name'=>$v['name'],'hashname'=>$hashname));
            }
        }

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
        return $this->db()->detail()->select('id,dnei,json_detail,creator,create_time,level,status,assign,reject_reason')->fetchPairs('id');
    }

    /**
     * 更新订单状态
     *
     * @param string $orderid
     * @param string $assign
     * @param string $status
     * @param string $reason
     * @return void
     */
    public function updateStatus($orderid, $assign, $status, $reason)
    {
        $data=array('update_time'=>new \NotORM_Literal('now()'),'assign'=>$assign,'status'=>$status,'reject_reason'=>$reason);
        $effect=$this->db()->detail()->where('id', $orderid)->update($data);
        if ($effect===0) {
            return false;
        }
        return $orderid;
    }
}
