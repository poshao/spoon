<?php
namespace App\Common\Model;

use \Spoon\Exception;

class Tools extends \Spoon\Model
{
    /**
     * PDO对象
     */
    protected $_db=null;

    /**
     * 获取当前数据库实例
     *
     * @return void
     */
    public function db()
    {
        if (empty($this->_db)) {
            $this->_db=self::getPDO(\Spoon\Config::getByApps('common')['db']);
        }
        return $this->_db;
    }

    /**
     * 执行SQL查询
     *
     * @param strubg $sql
     * @param Object $fieldmap
     * @param Array $dataset
     * @return void
     */
    public function ExecSQL($sql,$fieldmap,$dataset){
        $db=$this->db();
        $rs=$db->prepare($sql);
        $cnt=0;
        $rlst=array();

        if (!empty($fieldmap) && !empty($dataset)) {
            $isMultiQuery=isset($fieldmap['rskey']);
            foreach ($dataset as $line) {
                $lineItem=array();
                foreach ($fieldmap as $k=>$v) {
                    if ($k!=='rskey') {
                        $lineItem[$k]=$line[$v];
                    }
                }
                $rs->execute($lineItem);
                if ($isMultiQuery) {
                    try {
                        $rlst[$line[$fieldmap['rskey']]]=$rs->fetchAll(\PDO::FETCH_ASSOC);
                    } catch (\PDOException $t) {
                        if($t->getCode()==='HY000'){
                            // 处理语句为删除类的异常
                            $isMultiQuery=false;
                            $rlst=null;
                        }else{
                            $rlst[$line[$fieldmap['rskey']]]=null;
                        }
                    }
                }
                $cnt+=$rs->rowCount();
                unset($lineItem);
            }
        }else{
            $rs->execute();
            $cnt=$rs->rowCount();
            try {
                $rlst=$rs->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\PDOException $th) {
                $rlst=null;
            }
        }
        
        return array('rows'=>$cnt,'dataset'=>$rlst);
    }

    /**
     * 执行MDB数据库查询
     *
     * @param string $sql
     * @param string $fieldmap
     * @param string $dataset
     * @param string $option
     * @return void
     */
    public function ExecSQLMdb($sql,$fieldmap,$dataset,$option)
    {
        $filename=$option['filename'];
        $user=isset($option['user'])?$option['user']:'sa';
        $passwd=isset($option['password'])?$option['password']:'';
        $dsn="odbc:Driver={Microsoft Access Driver (*.mdb)};dbq=".$this->u2gb($filename).';Uid='.$user.';Pwd='.$passwd.';';
        try {
            $db=new \PDO($dsn);
            $rs=$db->prepare($this->u2gb($sql));
            $cnt=0;
            $rlst=array();
            if (!empty($fieldmap) && !empty($dataset)) {
                $isMultiQuery=isset($fieldmap['rskey']);
                foreach ($dataset as $line) {
                    $lineItem=array();
                    foreach ($fieldmap as $k=>$v) {
                        if ($k!=='rskey') {
                            $lineItem[$k]=$this->u2gb($line[$v]);
                        }
                    }
                    $rs->execute($lineItem);
                    if ($isMultiQuery) {
                        try {
                            $rlst[$line[$fieldmap['rskey']]]=$this->mdbFetchAll($rs,\PDO::FETCH_ASSOC);
                        } catch (\PDOException $t) {
                            if ($t->getCode()==='HY000') {
                                // 处理语句为删除类的异常
                                $isMultiQuery=false;
                                $rlst=null;
                            } else {
                                $rlst[$line[$fieldmap['rskey']]]=null;
                            }
                        }
                    }
                    $cnt+=$rs->rowCount();
                    unset($lineItem);
                }
            }else{
                $rs->execute();
                $cnt=$rs->rowCount();
                $rlst=$this->mdbFetchAll($rs,\PDO::FETCH_ASSOC);
            }

            return array('rows'=>$cnt,'dataset'=>$rlst);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    private function mdbFetchAll($rs,$style=\PDO::FETCH_BOTH){
        $rlst=array();
        while (($row=$rs->fetch($style))!==false) {
            $drow=array();
            foreach ($row as $k => $v) {
                $drow[$this->gb2u($k)]=$this->gb2u($v);
            }
            array_push($rlst, $drow);
        }
        return $rlst;
    }

    private function gb2u($s)
    {
        return iconv('gbk', 'utf-8', $s);
    }
    private function u2gb($s)
    {
        return iconv('utf-8','gbk', $s);
    }

    /**
     * 发送邮件
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param string $cc
     * @param string $bcc
     * @param string $attachments
     * @param boolean $isHtml
     * @return void
     */
    public function SendEmail($from,$to,$subject,$body,$cc=null,$bcc=null,$attachments=null,$isHtml=false){
        // try {
            $mail=new \Spoon\Extensions\PHPMailer();
            $mail->isSMTP();
            $mail->Host='smtp-cul.averydennison.net';

            // 发件人
            $mail->setFrom($from);
            foreach (\explode(',', $to) as $v) {
                $mail->addAddress($v);
            }

            // 抄送
            if (!empty($cc)) {
                foreach (\explode(',', $cc) as $v) {
                    $mail->addCC($v);
                }
            }

            // 密送
            if (!empty($bcc)) {
                foreach (\explode(',', $bcc) as $v) {
                    $mail->addBCC($v);
                }
            }

            // 附件
            if (!empty($attachments)) {
                $attachments=$_FILES['attachments'];
                for ($i=0;$i<count($attachments['name']);$i++) {
                    $mail->addAttachment($attachments['tmp_name'][$i], $attachments['name'][$i]);
                }
            }

            // 内容
            $mail->isHTML($isHtml);
            $mail->CharSet='utf8';
            $mail->Subject=$subject;
            $mail->Body=$body;
            $mail->send();
            return true;
        // }catch(Exception $e){
        //     \Spoon\Logger::error($);
        //     return false;
        // }
    }
}
