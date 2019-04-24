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
     * 获取父ID
     *
     * @param string $id
     * @return void
     */
    private function getParentId($id)
    {
        return $this->db()->orders()->select('parentid')->where('id', $id)->fetch()['parentid'];
    }

    /**
     * 获取订单状态
     *
     * @param string $orderid
     * @return void
     */
    public function getStatus($orderid)
    {
        return $this->db()->orders()->select('status')->where('id', $orderid)->fetch()['status'];
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

        //提取固定字段信息
        $order=$detail['dnei'];
        unset($detail['dnei']);
        $system=$detail['system'];
        unset($detail['system']);
        $level=$detail['level'];
        unset($detail['level']);
        $status='sended';
        $originId='';
        $parentid='';
        if (isset($detail['parentid'])) {
            $parentid=$detail['parentid'];
            $originId= $this->getParentId($detail['parentid']);
            if (empty($parentid)) {
                throw new Exception('invalid parent id', 400);
            }
            unset($detail['parentid']);
        }

        if (is_string($detail)===false) {
            $detail=\json_encode($detail);
        }

        $data=array(
            'dnei'=>$order,
            'system'=>$system,
            'level'=>$level,
            'status'=>$status,
            'detail'=>$detail,
            'creator'=>$workid,
            'create_time'=>new \NotORM_Literal('now()'),
            'last_user'=>$workid,
            'last_time'=>new \NotORM_Literal('now()')
        );
        //$data=array('creator'=>$workid,'json_detail'=>$detail);
        $row=$this->db()->orders()->insert($data);
        if (empty($parentid)) {
            $row['parentid']=$row['id'];
            $row->update();
        } else {
            $this->updateStatus($workid, $parentid, 'resend', '');
            $row['parentid']=$originId;
            $row->update();
        }
        
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
            'id','dnei','system','level','detail','creator','create_time','assign','assign_time','status','reject_reason','parentid','last_user','last_time'
        );
        if (\in_array($name, $fieldList)) {
            return $name;
        } else {
            return 'json_unquote(json_extract(`detail`,\'$.'.$name.'\'))';
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
        $rs=$this->db()->orders()->select('id,dnei,system,detail as json_detail,creator,create_time,level,status,assign,reject_reason,assign_time');
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
        //总行数
        $listCount=$rs->count();
        //分页处理
        if (isset($option['page'])) {
            $pageIndex=$option['page']['index'];
            $pageCount=$option['page']['count'];
            $rs->limit($pageCount, $pageIndex*$pageCount);
        }

        //返回结果
        return array(
            'total'=>$listCount,
            'list'=>$rs->fetchPairs('id')
        );
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
    public function updateStatus($workid, $orderid, $status, $reason)
    {
        $data=array(
            'status'=>$status,
            'last_user'=>$workid,
            'last_time'=>new \NotORM_Literal('now()')
        );
        if ($status==='finish' || $status==='reject') {
            $data['assign']=$workid;
            $data['assign_time']=new \NotORM_Literal('now()');
        }
        if ($status==='reject') {
            $data['reject_reason']=$reason;
        }

        $effect=$this->db()->orders()->where('id', $orderid)->update($data);
        if ($effect===0) {
            return false;
        }
        return $orderid;
    }
}
