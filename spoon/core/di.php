<?php
/**
 * 依赖注入管理
 */
namespace Spoon;

class DI
{
    protected static $_services=null;

    public static function getDI($name)
    {
        return self::$_services[$name];
    }

    public static function setDI($name, $obj)
    {
        self::$_services[$name]=$obj;
    }
}
