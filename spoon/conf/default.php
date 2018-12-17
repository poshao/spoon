<?php
/**
 * 默认配置
 */
return array(
    /**
     * 调试模式
     * 
     * 可选值: true, false
     * 默认值: false
     */
    'debug'=>false,

    /**
     * 应用相关设置
     */
    'apps'=>array(

    ),
    /**
     * 日志相关设置
     */
    'logger'=>array(
        /**
         * 日志开关
         * 
         * 可选值: true,false
         * 默认值: true
         */
        'enable'=>false,
        
        /**
         * 日志记录级别
         * 
         * 可选值: debug < info < warn < error
         * 默认值: info
         */ 
        'level'=>'info',

        /**
         * 文件名
         * 
         * 可选值: 文件路径(若是相对路径,默认在logs文件夹)
         * 默认值: 'spoon.log'
         */
        'file'=>'spoon.log',
    ),

    /**
     * 时区
     * 
     * 可选值: php中TimeZone设置可选值
     * 默认值: 'Asia/Shanghai'
     */
    'timezone'=>'Asia/Shanghai',
    
);
?>