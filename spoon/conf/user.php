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
            /**
             * 令牌有效时间(秒)
             */
            'token_timeout'=>3600,
            
            /**
             * 密码加密盐值(若不设置则使用 security->salt)
             */
            'salt'=>'hello world',
        ),
        /**
         * CS放单平台
         */
        'linkcs'=>array(
            'db'=>array(
                'driver'=>'mysql',
                'dbname'=>'Spoon',
                'host'=>'localhost',
                'user'=>'root',
                'password'=>'123456',
                'charset'=>'utf8',
                'prefix'=>'cs_'
            ),
        ),
    )
);
