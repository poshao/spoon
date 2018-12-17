<?php
/**
 * 配置管理类
 */
namespace Spoon;
class Config{
    protected static $_configs=array();

    /**
     * 加载配置
     *
     * @return void
     */
    public static function load(){
        self::loadConfig(__DIR__.'/../conf/default.php');
        self::loadConfig(__DIR__.'/../conf/user.php');
    }

    /**
     * 从文件加载配置
     *
     * @param string $file
     * @return void
     */
    public static function loadConfig($file){
        if(file_exists($file)){
            // self::$_configs=array_merge(self::$_configs,require($file));
            self::array_merge(self::$_configs,require($file));
        }
    }

    /**
     * 合并数组
     *
     * @param array $base
     * @param array $ext
     * @return void
     */
    private static function array_merge(&$base,$ext){
        foreach($ext as $k=>$v){
            if(isset($base[$k]) && \is_array($v)){
                self::array_merge($base[$k],$v);
            }else{
                $base[$k]=$v;
            }
        }
    }

    /**
     * 获取配置值
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key,$default=null){
        if(isset(self::$_configs[$key])){
            return self::$_configs[$key];
        }else{
            return $default;
        }
    }

    /**
     * 根据应用名称获取配置
     *
     * @param string $appname 应用名称
     * @return array
     */
    public static function getByApps($appname){
        if(isset(self::get('apps')[$appname])){
            return self::get('apps')[$appname];
        }
        return null;
    }

    public static function list(){
        echo json_encode(self::$_configs);
    }
}
?>