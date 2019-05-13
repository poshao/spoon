<?php
namespace App\Develop\Model;

use \Spoon\Exception;

class Projects extends \Spoon\Model
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
            $this->_db=self::getORM(\Spoon\Config::getByApps('project')['db']);
        }
        return $this->_db;
    }

    /**
     * 获取状态
     *
     * @param int $projectid
     * @return void
     */
    public function getStatus($projectid){
        return $this->db()->projects()->select('status')->where('id',$projectid)->fetch()['status'];
    }

    /**
     * 日志记录
     *
     * @param int $projectid
     * @return void
     */
    private function log($projectid){
        $row=$this->db()->projects()->select('id,subject,description,files,status,last_user')->where('id',$projectid)->fetch();
        //更新日志表
        $data_log=array(
            'project_id'=>$row['id'],
            'subject'=>$row['subject'],
            'description'=>$row['description'],
            'status'=>$row['status'],
            'files'=>$row['files'],
            'operator'=>$row['last_user']
        );
        $row2=$this->db()->logs()->insert($data_log);
        return $row2!==false;
    }

    /**
     * 创建新项目
     *
     * @param string $workid
     * @param string $subject
     * @param string $description
     * @param object $files
     * @return void
     */
    public function newProject($workid, $subject, $description, $files)
    {
        $data=array(
            'subject'=>$subject,
            'description'=>$description,
            'status'=>'request',
            'request'=>$workid,
            'request_time'=>new \NotORM_Literal('now()'),
            'last_user'=>$workid,
            'last_time'=>new \NotORM_Literal('now()')
        );

        $row=$this->db()->projects()->insert($data);
        $projectid=$row['id'];
        $row['project_id']=$projectid;
        $row->update();
        
        //记录日志
        $this->log($projectid);
        return $projectid;
    }

    /**
     * 更新项目状态
     *
     * @param string $workid
     * @param string $status
     * @return void
     */
    public function updateProject($workid,$projectid,$status,$remark,$files){
        $oldStatus=$this->getStatus($projectid);
        $data=array(
            'last_user'=>$workid,
            'last_time'=>new \NotORM_Literal('now()'),
            'status'=>$status
        );

        switch($oldStatus){
            case 'request':
                $data['audit']=$workid;
                $data['audit_time']=new \NotORM_Literal('now()');
                break;
            case 'pass':
                $data['develop']=$workid;
                $data['develop_time']=new \NotORM_Literal('now()');
                break;
        }
        
        if($status==='finish'){
            $data['finish_time']=new \NotORM_Literal('now()');
        }

        $effect=$this->db()->projects()->where('id',$projectid)->update($data);
        $this->log($projectid);
    }

    /**
     * 枚举项目清单
     *
     * @return void
     */
    public function listProjects(){
        return $this->db()->projects()->select('id,subject,description,files,request,request_time,audit,audit_time,develop,develop_time,finish_time,status')->fetchPairs('id');
    }
}