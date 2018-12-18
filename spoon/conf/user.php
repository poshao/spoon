<?php
/**
 * 用户配置
 * 
 * 覆盖系统默认配置
 */
return array(
    'debug'=>true,
    'logger'=>array(
        'enable'=>true,
        'level'=>'debug',
        // 'file'=>'C:/123.log'
    ),
    'apps'=>array(
        /**
         * 用户认证
         */
        'auth'=>array(
            'db'=>array(
                'driver'=>'mysql',
                'dbname'=>'Spoon',
                'host'=>'localhost',
                'user'=>'root',
                'password'=>'123456',
                'charset'=>'utf8',
                'prefix'=>'auth_'
            ),
        ),
    )
);
?>