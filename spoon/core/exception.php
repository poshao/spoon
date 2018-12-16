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
    function render(){
        Response::send(json_encode(array('error'=>$this->getMessage().' (line:'.$this->getLine().' code:'.$this->getCode().')')),500);
        \Spoon\Logger::error($this->__toString());
    }
}
?>