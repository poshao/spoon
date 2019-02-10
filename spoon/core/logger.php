<?php
/**
 * 日志记录
 */
namespace Spoon;

use \Spoon\Config;
use \Spoon\Exception;

class Logger
{
    private static $level=array(
        'debug'=>0,
        'info'=>1,
        'warn'=>2,
        'error'=>3
    );

    /**
     * 写入日志文件
     *
     * @param string $msg
     * @return void
     * @throws Exception 文件读写失败
     */
    private static function write($msg)
    {
        $logger=Config::get('logger');
        if ($logger==null) {
            return;
        }

        if (isset($logger['enable'])==false
            || $logger['enable']==false) {
            return;
        }

        unset($file);
        if (isset($logger['file'])) {
            $file=$logger['file'];
            if (\strpos($file, '\\')===false && \strpos($file, '/')===false) {
                $file=SitePath.'/log/'.$file;
            }
        }
        if (!isset($file)) {
            $file=SitePath.'/log/spoon.log';
        }
        if (\file_put_contents($file, $msg, FILE_APPEND)===false) {
            throw new Exception('日志写入失败');
        }
    }
    
    /**
     * 格式化日志输出
     *
     * @param string $level
     * @param string $msg
     * @return string
     */
    private static function format($level, $msg)
    {
        return \sprintf("%s [%s] %s\n", \date('Y-m-d H:i:s'), $level, $msg);
    }
    
    /**
     * 输出调试信息
     *
     * @param string $msg
     * @return void
     */
    public static function debug($msg)
    {
        if (self::$level[Config::get('logger')['level']]<=self::$level['debug']) {
            self::write(self::format('debug', $msg));
        }
    }

    /**
     * 输出通知信息
     *
     * @param string $msg
     * @return void
     */
    public static function info($msg)
    {
        if (self::$level[Config::get('logger')['level']]<=self::$level['info']) {
            self::write(self::format('info', $msg));
        }
    }

    /**
     * 输出警告信息
     *
     * @param string $msg
     * @return void
     */
    public static function warn($msg)
    {
        if (self::$level[Config::get('logger')['level']]<=self::$level['warn']) {
            self::write(self::format('warn', $msg));
        }
    }

    /**
     * 输出错误信息
     *
     * @param string $msg
     * @return void
     */
    public static function error($msg)
    {
        if (self::$level[Config::get('logger')['level']]<=self::$level['error']) {
            self::write(self::format('error', $msg));
        }
    }
}
