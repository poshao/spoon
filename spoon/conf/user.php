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
                'dbname'=>'avery_logistics',
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

            /**
             * 头像设定
             */
            'avator'=>array(
                /**
                 * 用户头像文件命名规则(未生效)
                 */
                // 'name'=>'{date}/{hash:8}.{ext}',
                /**
                 * 头像尺寸(像素)
                 */
                'size'=>48,
                /**
                 * 用户头像存储文件夹
                 */
                'dir'=>realpath(__DIR__.'/../../data/avators'),
            ),
        ),
        /**
         * CS放单平台
         */
        'linkcs'=>array(
            'db'=>array(
                'driver'=>'mysql',
                'dbname'=>'avery_logistics',
                'host'=>'localhost',
                'user'=>'root',
                'password'=>'123456',
                'charset'=>'utf8',
                'prefix'=>'cs_'
            ),
            //用户存档的附件文件夹
            'data_dir'=>__DIR__.'/../../data/linkcs/store' ,
            //用户临时存放附件的文件夹
            'temp_dir'=>__DIR__.'/../../data/linkcs/temp',
        ),
        // VBA加载管理
        'vba'=>array(
            //数据库链接
            'db'=>array(
                'driver'=>'mysql',
                'dbname'=>'avery_logistics',
                'host'=>'localhost',
                'user'=>'root',
                'password'=>'123456',
                'charset'=>'utf8',
                'prefix'=>'vba_'
            ),
            //加载项存档文件夹
            'store_dir'=>__DIR__.'/../../data/addins',
        ),
    )
);
