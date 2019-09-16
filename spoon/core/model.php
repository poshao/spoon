<?php
/**
 * 资源操作
 */
namespace Spoon;

class Model
{

    /**
     * 获取ORM数据库实例
     *
     * @param array $config 配置信息 driver,dbname,host,user,password,charset,prefix
     * @return NotORM
     */
    public static function getORM($config)
    {
        // if (empty($config)) {
        //     throw new Exception('NotORM配置信息异常');
        // }

        // $dsn=$config['driver'].':dbname='.$config['dbname'].';host='.$config['host'].';';
        // $user=$config['user'];
        // $password=$config['password'];
        // $charset=$config['charset'];

        // $pdo=new \PDO($dsn, $user, $password);
        // $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        // $pdo->exec('set names '. $charset);

        $pdo=self::getPDO($config);

        return new \Spoon\Extensions\NotORM($pdo, new \NotORM_Structure_Convention('id', '', '', $config['prefix']));
    }

    /**
     * 获取PDO实例
     *
     * @param array $config 配置信息 driver,dbname,host,user,password,charset,prefix
     * @return void
     */
    public static function getPDO($config)
    {
        if (empty($config)) {
            throw new Exception('NotORM配置信息异常');
        }

        $dsn=$config['driver'].':dbname='.$config['dbname'].';host='.$config['host'].';';
        $user=$config['user'];
        $password=$config['password'];
        $charset=$config['charset'];

        $pdo=new \PDO($dsn, $user, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_PERSISTENT, true);

        $pdo->exec('set names '. $charset);
        return $pdo;
    }
}
