<?php
/**
 * Spoon 异常处理类
 */
namespace Spoon;

class Exception extends \Exception{
    
    // public function __construct($msg="",$code=0){
    //     $this->message=$msg;
    //     $this->code=$code;
    // }
    /**
     * 渲染错误信息
     *
     * @return void
     */
    public function render(){
        Response::send(json_encode(array(
            'error'=>array(
                'message'=>$this->getMessage(),
                'code'=>$this->getCode(),
                'file'=>$this->getFile(),
                'line'=>$this->getLine()
            )
        )),$this->getCode()>400?$this->getCode():500);
        \Spoon\Logger::error($this->__toString());
    }
}
?>