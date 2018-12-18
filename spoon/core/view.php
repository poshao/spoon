<?php
/**
 * 页面接口
 */
namespace Spoon;
abstract class View{
    /**
     * 验证规则
     *
     * @var array
     */
    protected $_rules=null;
    /**
     * 参数
     *
     * @var array
     */
    protected $_params=null;

    public function __construct(){
        //初始化参数列表
        $this->_params=array();
        if(!empty($_GET)) $this->_params=array_merge($this->_params,$_GET);
        if(!empty($_POST)) $this->_params=array_merge($this->_params,$_POST);

        $payload=file_get_contents('php://input');//请求原始数据
        if(!empty($payload) && $_SERVER['CONTENT_TYPE']=='application/json'){
            $this->_params=array_merge($this->_params,json_decode($payload,true));
        }
    }

    /**
     * 获取参数值
     *
     * @param string $key
     * @return mixed
     */
    public function get($key){
        return $this->_params[$key];
    }

    /**
     * 新增一条验证规则
     *
     * @param string $key 参数名
     * @param array $rule 验证规则
     * @return void
     */
    public function addRule($key,$rule){
        $this->_rules[$key]=$rule;
    }

    /**
     * 删除一条验证规则
     *
     * @param string $key
     * @return void
     */
    public function delRule($key){
        unset($this->_rules[$key]);
    }

    /**
     * 检查参数
     *
     * @param array $req_params 必选参数
     * @param array $opt_params 可选参数
     * @return bool
     * @throws Exception
     */
    public function checkParams($req_params,$opt_params=null){
        foreach($req_params as $k=>$v){
            if(!isset($this->_params[$v]) || !$this->check($this->_params[$v],$this->_rules[$v])){
                throw new Exception('参数检查错误 '.$v.':'.json_encode($this->_rules[$v]));
            }
        }

        if(!empty($opt_params)){
            foreach($opt_params as $k=>$v){
                if(isset($this->_params[$v]) && !$this->check($this->_params[$v],$this->_rules[$v])){
                    throw new Exception('可选参数检查错误 '.$v.':'.json_encode($this->_rules[$v]));
                }
            }
        }
    }

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
    
    /**
     * 发送Json序列化数据
     *
     * @param array $data 响应数据
     * @return void
     */
    public function sendJSON($data){
        \Spoon\Response::sendJSON($data);
    }
}
?>