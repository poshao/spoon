# NotORM使用说明
高性能的ORM操作库

## 1.建立连接
``` php
    $dsn='mysql:dbname=test;host=localhost';
    $user='root';
    $password='123456';
    //使用PDO连接数据库
    $pdo=new \PDO($dsn,$user,$password);
    //设置PDO显示错误信息
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    //设置字符集
    $pdo->exec('set names '. $charset);
    $db=new \NotORM($pdo,new \NotORM_Structure_Convention('id','','','table_prefix'));//主键,外键,表名,表前缀
```
## 2.查询
``` php
    $db->tableName()->select('*')->where('id',1)->fetch();
    $db->tableName()->select('id,name')->fetch();
```
|where 结构|说明|
|-|-|
|$table->where("field", "x")|Translated to field = 'x' (with automatic escaping)|
|$table->where("field", null)|Translated to field IS NULL|
|$table->where("field", array("x", "y"))|Translated to field IN ('x', 'y') (with automatic escaping)|
|$table->where("NOT field", array("x", "y"))|Translated to NOT field IN ('x', 'y') (with automatic escaping)|
|$table->where("field", $db->$tableName())|Translated to "field IN (SELECT $primary FROM $tableName)"|
|$table->where("field", $db->$tableName()->select($column))|Translated to "field IN (SELECT $column FROM $tableName)"|
|$table->where("field > ? AND field < ?", "x", "y")|Bound by PDO|
|$table->where("(field1, field2)", array(array(1, 2), array(3, 4)))|Translated to (field1, field2) IN ((1, 2), (3, 4)) (with automatic escaping)|
|$table->where(array("field" => "x", "field2" => "y"))|Translated to field = 'x' AND field2 = 'y' (with automatic escaping)|

## 3.插入
``` php
    /**
     * 插入一行数据,返回行记录
     * sql:
     *  insert into table value(1,2,3)
     */
    $db->tableName()->insert($array);

    /**
     * 插入多行数据,返回第一行数据
     * sql:
     *  insert into table values(1,2,3),(4,5,6)...
     */
    $db->tableName()->insert($array1,$array2,...);

    /**
     * 将查询结果插入指定表,返回受影响的行数
     * sql:
     *  insert into table select * from ...
     */
    $db->tableName()->insert($result);

    /**
     * 插入多行数据,返回受影响的行数
     * sql:
     *  insert into table values(1,2,3);
     *  insert into table values(1,2,3);
     */
    $db->tableName()->insert_muti(array($array1,$array2,...));

    /**
     * 返回最新的id号
     */
    $db->tableName()->insert_id();

    /**
     * 插入或更新数据,返回受影响的行数
     * 参数:
     *  $unique array 查询条件
     *  $insert array 插入的数据
     *  $update array 更新的数据,未赋值时使用$insert数据
     * sql:
     *  INSERT INTO application (id, title) VALUES (5, 'NotORM') ON DUPLICATE KEY UPDATE modified = NOW();
     */
    $db->tableName()->insert_update($unique,$insert,$update);

```
## 4.更新
``` php
    /**
     * 更新表格中所有记录,返回受影响的行数(一般需要配合where一起使用)
     * sql:
     *  update table set a=b
     */
    $db->tableName()->update($array);

    /**
     * 更新指定行数据,返回受影响的行数
     */
    $row->update($array);

    /**
     * 使用$row[]赋值后更新,返回受影响的行数
     */
    $row['name']='hello';
    $row->update();
    
```
## 5.删除
``` php
    /**
     * 删除表中所有数据,返回受影响的行数
     */
    $db->tableName()->delete();

    /**
     * 删除当前行的数据,返回受影响的行数
     */
    $row->delete();
```