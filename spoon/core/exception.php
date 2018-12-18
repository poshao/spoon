<?php
/**
 * Spoon 异常处理类
 */
namespace Spoon;

class Exception extends \Exception{
    
    /**
     * 渲染错误信息
     *
     * @return void
     */
    public function render(){
        $debug=\Spoon\Config::get('debug');

        if($debug){
            $data=array(
                'error'=>array(
                    'message'=>$this->getMessage(),
                    'code'=>$this->getCode(),
                    'file'=>$this->getFile(),
                    'line'=>$this->getLine()
                )
            );
        }else{
            $data=array('error'=>$this->getMessage());
        }

        Response::send(json_encode($data),$this->getCode()>=400?$this->getCode():500);
        \Spoon\Logger::error($this->__toString());
    }
}
?>