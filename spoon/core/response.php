<?php
/**
 * 响应类
 */
namespace Spoon;

class Response{
    /**
     * 状态码
     *
     * @var array
     */
    public static $statuscode_list=array(
        '200'=>'OK',
        '201'=>'CREATED',
        '202'=>'Accepted',
        '204'=>'NO CONTENT',
        '400'=>'INVALID REQUEST',
        '401'=>'Unauthorized',
        '403'=>'Forbidden',
        '404'=>'NOT FOUND',
        '406'=>'Not Acceptable',
        '410'=>'Gone',
        '422'=>'Unprocesable entity',
        '500'=>'INTERNAL SERVER ERROR'
    );

    /**
     * 协议版本
     *
     * @var string
     */
    public static $protocol='HTTP/1.1';

    /**
     * 状态码
     *
     * @var integer
     */
    
    /**
     * 发送响应头
     *
     * @param int $code
     * @return void
     */
    public function setStatus($code){
        if(!array_key_exists($code,self::$statuscode_list)){
            throw new \Spoon\Exception('无效状态码:'.$code);
        }
        \header(self::$protocol.' '.$code.' '.self::$statusCode[$code]);
    }

    /**
     * 发送响应文件格式
     *
     * @param string $conntentType
     * @return void
     */
    public function setContentType($conntentType,$charset='utf-8'){
        \header('Content-Type:'.$conntentType.';charset='.$charset);
    }

    /**
     * 跨域设置
     *
     * @param string $url
     * @return void
     */
    public function allowOrigin($url='*'){
        \header('Access-Control-Allow-Origin:'.$url);
    }

    /**
     * 发送默认头设置
     *
     * @return void
     */
    public function defaultHeader(){
        $this->setStatus(200);
        $this->setContentType('application/json');
        $this->allowOrigin();
    }

    /**
     * 发送数据
     *
     * @return void
     */
    public function send(){
        // do something...
    }
}
?>