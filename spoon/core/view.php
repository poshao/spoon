<?php
/**
 * 页面接口
 */
namespace Spoon;

abstract class View
{
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

    public function __construct()
    {
        //初始化参数列表
        $this->_params=array();
        if (!empty($_GET)) {
            $this->_params=array_merge($this->_params, $_GET);
        }
        if (!empty($_POST)) {
            $this->_params=array_merge($this->_params, $_POST);
        }

        //默认类型设定
        $contentType=array(
            'mime'=>'application/json',
            'option'=>array(
                'charset'=>'utf-8'
            )
        );
        //处理CONTENT_TYPE类型
        // if (!empty($_SERVER['CONTENT_TYPE'])) {
        // empty($_SERVER['CONTENT_TYPE']
        // return;
        // }
        if (!isset($_SERVER['CONTENT_TYPE'])) {
            $_SERVER['CONTENT_TYPE']='application/json';
        }
        $contentType_list=explode(';', \strtolower($_SERVER['CONTENT_TYPE']));
        $contentType['mime']=$contentType_list[0];

        $contentType_option=$contentType['option'];
        for ($i=1;$i<\count($contentType_list);$i++) {
            $arrTmp=explode('=', $contentType_list[$i], 2);
            $contentType_option[trim($arrTmp[0])]=trim($arrTmp[1]);
        }

        $payload=file_get_contents('php://input');//请求原始数据
        if (!empty($payload)) {
            if ($contentType['mime']==='application/json') {
                $this->_params=array_merge($this->_params, json_decode($payload, true));
            } else {
                Logger::error('无效输入格式,['+$_SERVER['CONTENT_TYPE']+']');
            }
        }
        // }
    }

    /**
     * 获取参数值
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        }
        return null;
    }

    /**
     * 设置参数值
     *
     * @param string $key 键名
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        return $this->_params[$key]=$value;
    }

    /**
     * 参数个数
     *
     * @return int
     */
    public function paramsCount()
    {
        $ignoreCount=0;
        if (isset($this->_params['auth_workid'])) {
            $ignoreCount++;
        }
        if (isset($this->_params['auth_token'])) {
            $ignoreCount++;
        }

        return \count($this->_params)-$ignoreCount;
    }
    
    /**
     * 新增一条验证规则
     *
     * @param string $key 参数名
     * @param array $rule 验证规则
     * @return void
     */
    public function addRule($key, $rule)
    {
        $this->_rules[$key]=$rule;
    }

    /**
     * 删除一条验证规则
     *
     * @param string $key
     * @return void
     */
    public function delRule($key)
    {
        unset($this->_rules[$key]);
    }

    /**
     * 检查参数
     *
     * @param array $req_params 必选参数
     * @param array $opt_params 可选参数
     * @param array $params 参数数组(默认为请求参数)
     * @return bool
     * @throws Exception
     */
    public function checkParams($req_params, $opt_params=null, $params=null)
    {
        if (!\is_array($params)) {
            $params=$this->_params;
        }
        foreach ($req_params as $k=>$v) {
            if (!isset($params[$v]) || !$this->check($params[$v], $this->_rules[$v])) {
                throw new Exception('参数检查错误 '.$v.':'.json_encode($this->_rules[$v]));
            }
        }

        if (!empty($opt_params)) {
            foreach ($opt_params as $k=>$v) {
                if (isset($params[$v]) && !$this->check($params[$v], $this->_rules[$v])) {
                    throw new Exception('可选参数检查错误 '.$v.':'.json_encode($this->_rules[$v]));
                }
            }
        }
        return true;
    }

    /**
     * 验证数据格式
     *
     * @param mixed $param 参数
     * @param array $rule 规则数组
     * @return bool
     * @throws Exception
     */
    public function check($param, $rule)
    {
        //sample
        // array('type'=>'number','max'=>100,'min'=>10,'require'=>true);
        // array('type'=>'bool');
        // array('type'=>'list','list'=>array('item1','item2'));
        // array('type'=>'text','length'=>10);
        // array('type'=>'regex','pattern'=>'/^hello$/');
        // array('type'=>'array','require'=>array('rule1','rule1'),'optional'=>array('rule3','rule4'))

        if (!\is_array($rule)) {
            // Logger::debug('invalid rule: $rule is not array');
            throw new Exception('invalid rule');
        }
        switch ($rule['type']) {
            case 'number'://数值
                if (!\is_numeric($param)) {
                    return false;
                }
                if (isset($rule['max']) && $param>$rule['max']) {
                    return false;
                }
                if (isset($rule['min']) && $param<$rule['min']) {
                    return false;
                }
                break;
            case 'regex'://正则表达式
                if (!isset($rule['pattern']) || !\preg_match($rule['pattern'], $param)) {
                    return false;
                }
                break;
            case 'bool'://布尔值
                return \is_bool($param);
                break;
            case 'list'://列表
                return isset($rule['list']) && \is_array($rule['list']) && \in_array($param, $rule['list']);
                break;
            case 'text'://文本

                break;
            case 'array'://数组
                return $this->checkParams($rule['require'], $rule['optional'], $param);
                break;
            case 'file'://文件
                return (isset($_FILES[$param]) && $_FILES[$param]['error']===0);
                break;
            default:
                throw new Exception('type not support');
                // Logger::debug('invalid rule ==> ['.\json_encode($rule).']');
                break;
        }
        if (isset($rule['length']) && \strlen($param)!=$rule['length']) {
            return false;
        }
        if (isset($rule['length-max']) && \strlen($param)>$rule['length-max']) {
            return false;
        }
        if (isset($rule['length-min']) && \strlen($param)<$rule['length-min']) {
            return false;
        }
        return true;
    }
    
    /**
     * 发送Json序列化数据
     *
     * @param array $data 响应数据
     * @return void
     */
    public function sendJSON($data, $code=200)
    {
        \Spoon\Response::sendJSON($data, $code);
    }

    /**
     * 发送文件
     *
     * @param array $fileInfo
     *      name: 文件短名称和后缀
     *      path: 文件路径
     * @return void
     */
    public function sendFile($fileInfo)
    {
        \Spoon\Response::sendFile($fileInfo);
    }
}
