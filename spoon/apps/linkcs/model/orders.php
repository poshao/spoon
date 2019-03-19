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
            $path=$tempDir.$v['name'];
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
     * 转化虚拟字段名
     *
     * @param string $name
     * @return string
     */
    private function ConvertFieldName($name)
    {
        $name=strtolower(trim($name));
        $fieldList=array(
            'id','creator','assign','status','reject_reason','create_time','update_time'
        );
        if (\in_array($name, $fieldList)) {
            return $name;
        } else {
            return 'json_unquote(json_extract(`json_detail`,\'$.'.$name.'\'))';
        }
    }
    /**
     * 获取订单列表
     *
     * @param array $option 筛选条件
     * @return array
     */
    public function list($option)
    {
        $rs=$this->db()->detail()->select('id,dnei,json_detail,creator,create_time,level,status,assign,reject_reason');
        //字段选择

        //提取筛选条件
        if (isset($option['filters'])) {
            foreach ($option['filters'] as $k=>$v) {
                //处理key
                $key=$this->ConvertFieldName($v['key']);
                //处理value
                $value=$v['value'];
                //处理operator
                $operator=$v['operator'];
                switch ($operator) {
                    case '=':
                    case '>':
                    case '<':
                    case '>=':
                    case '<=':
                    case '!=':
                        $rs->where($key.' ' .$operator.' ?', $value);
                    break;
                    case 'like':
                        $value='%'.trim($value, '%').'%';
                        $rs->where($key.' ' .$operator.' ?', $value);
                    break;
                    case 'in':
                        $rs->where($key, $value);
                    break;
                    case '!in':
                        $rs->where($key.' not in ?', $value);
                    break;
                }
            }
        }
        //提取排序规则
        if (isset($option['sorts'])) {
            foreach ($option['sorts'] as $k=>$v) {
                $rs->order($this->ConvertFieldName($v['key']).' '.$v['order']);
            }
        }
        //分页处理
        if (isset($option['page'])) {
            $pageIndex=$option['page']['index'];
            $pageCount=$option['page']['count'];
            $rs->limit($pageCount, $pageIndex*$pageCount);
        }

        //返回结果
        return $rs->fetchPairs('id');
        // return $this->db()->detail()->select('id,dnei,json_detail,creator,create_time,level,status,assign,reject_reason')->fetchPairs('id');
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
