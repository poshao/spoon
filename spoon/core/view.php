<?php
/**
 * 页面接口
 */
namespace Spoon;
abstract class View{
    protected $_rules=null;

    /**
     * 检查参数
     *
     * @param array $params 参数列表
     * @return bool
     */
    abstract public function checkParams($params);

    /**
     * 验证数据格式
     *
     * @param mixed $param 参数
     * @param array $rule 规则数组
     * @return bool
     * @throws Exception
     */
    public function check($param,$rule){
        //sample
        // array('type'=>'number','max'=>100,'min'=>10,'require'=>true);
        // array('type'=>'bool');
        // array('type'=>'list','list'=>array('item1','item2'));
        // array('type'=>'text','length'=>10);
        // array('type'=>'regex','pattern'=>'/^hello$/');

        if(!\is_array($rule)){
            // Logger::debug('invalid rule: $rule is not array');
            throw new Exception('invalid rule');
        }
        switch($rule['type']){
            case 'number'://数值
                if(!\is_numeric($param)) return false;
                if(isset($rule['max']) && $param>$rule['max']) return false;
                if(isset($rule['min']) && $param<$rule['min']) return false;
                break;
            case 'regex'://正则表达式
                if(!isset($rule['pattern']) || !\preg_match($rule['pattern'],$param)) return false;
                break;
            case 'bool'://布尔值
                return \is_bool($param);
                break;
            case 'list'://列表
                return isset($rule['list']) && \is_array($rule['list']) && \in_array($param,$rule['list']);
                break;
            case 'text'://文本
                break;
            default:
                throw new Exception('type not support');
                // Logger::debug('invalid rule ==> ['.\json_encode($rule).']');
                break;
        }
        if(isset($rule['length']) && \strlen($param)!=$rule['length']) return false;
        if(isset($rule['length-max']) && \strlen($param)>$rule['length-max']) return false;
        if(isset($rule['length-min']) && \strlen($param)<$rule['length-min']) return false;
        return true;
    }
}
?>