<?php
/**
 * 响应类
 */
namespace Spoon;

class Response
{
    /**
     * 状态码
     *
     * @var array
     */
    private static $statuscode_list=array(
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
    private static $_protocol='HTTP/1.1';

    /**
     * 跨域设置
     *
     * @param string $url
     * @return void
     */
    public static function allowOrigin($url='*')
    {
        \header('Access-Control-Allow-Origin:'.$url);
    }

    /**
     * 发送响应头
     *
     * @param int $code
     * @return void
     */
    public static function setStatus($code)
    {
        if (!array_key_exists($code, self::$statuscode_list)) {
            throw new \Spoon\Exception('无效状态码:'.$code);
        }
        \header(self::$_protocol.' '.$code.' '.self::$statuscode_list[$code]);
    }

    /**
     * 发送响应文件格式
     *
     * @param string $contentType
     * @return void
     */
    public static function setContentType($contentType, $charset='utf-8')
    {
        if ($charset!='') {
            \header('Content-Type:'.$contentType.';charset='.$charset);
        } else {
            \header('Content-Type:'.$contentType);
        }
    }

    /**
     * 设置文件下载的默认文件名
     *
     * @param string $filename
     * @return void
     */
    public static function setSaveFilename(string $filename)
    {
        \header("Content-Disposition: attachment; filename=" . $filename);
    }

    /**
     * 发送数据
     *
     * @param mixed $data 数据或文件名
     * @param int $code 状态码
     * @param string $contentType 数据格式
     * @param string $charset 编码格式 ''表示不发送编码参数
     * @param string $outType 'text'|'binary' 输出格式
     * @return void
     */
    public static function send($data, $code=200, $contentType='application/json', $charset='utf-8', $outType='text')
    {
        self::allowOrigin();
        self::setStatus($code);
        self::setContentType($contentType, $charset);
        switch ($outType) {
            case 'text':
                echo $data;
                break;
            case 'binary':
                readfile($data);
                // file_put_contents('php://output', $data);
                break;
        }
    }

    /**
     * 发送数据
     *
     * @param mixed $data 数据
     * @param int $code 状态码
     * @param string $contentType 数据格式
     * @param string $charset 编码格式 ''表示不发送编码参数
     * @param string $outType 'text'|'binary' 输出格式
     * @return void
     */
    public static function sendJSON($data, $code=200, $contentType='application/json', $charset='utf-8', $outType='text')
    {
        self::send(\json_encode($data), $code, $contentType, $charset, $outType);
    }

    /**
     * 发送错误信息
     *
     * @param int $code 状态码
     * @param string $message 错误信息
     * @return void
     */
    public static function sendError($code, $message)
    {
        self::send(\json_encode(array('error'=>$message)), $code);
    }


    /**
     * 发送文件
     *
     * @param array $fileInfo
     *      name: 文件短名称和后缀
     *      path: 文件路径
     * @param string $contentType
     * @return void
     */
    public static function sendFile($fileInfo, $contentType='application/octet-stream')
    {
        \Header("Accept-Ranges: bytes");
        \Header("Accept-Length: ".filesize($fileInfo['path']));
        self::setSaveFilename(\urlencode($fileInfo['name']));
        self::send($fileInfo['path'], 200, $contentType, '', 'binary');
    }
}
