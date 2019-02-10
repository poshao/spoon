<?php
namespace Spoon;

class Util
{
    /**
     * 获取真实IP地址
     *
     * @return string
     */
    public static function getIP()
    {
        if (isset($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]) && $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]) {
            $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]) && $HTTP_SERVER_VARS["HTTP_CLIENT_IP"]) {
            $ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
        } elseif (isset($HTTP_SERVER_VARS["REMOTE_ADDR"]) && $HTTP_SERVER_VARS["REMOTE_ADDR"]) {
            $ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
        } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } elseif (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = "Unknown";
        }
        return $ip;
    }
}
