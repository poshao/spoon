<?php
/**
 * 文件存储类
 */
class Store
{
  /**
   * 文档存储路径
   *
   * @var string
   */
  protected $store_dir='';

  /**
   * mysql连接字符串(PDO)
   *
   * @var string
   */
  protected $pdo='';

  protected $conn=null;

  public function  __construct(){

  }

  /**
   * 存储文件
   *
   * @param string $category
   * @param string $filepath
   * @param string $filename
   * @return void
   */
    public function saveFile($category, $filepath, $filename='')
    {
      if(!file_exists($filepath)){
        return false;
      }

      // 生成文件路径
      $data=array(
        'hashname'=>md5(uniqid('',true)),
        'origin_name'=> empty( $filename)?basename($filepath):$filename,
        'path'=>'test',
        'category'=>$category
      );
      
      $destFilePath =$this->store_dir.DIRECTORY_SEPARATOR.uniqid('',true);

      // 复制文件
      if(!@copy($filepath,$destFilePath)){
        return false;
      }

      // 记录到数据库

    }

    public function saveStream()
    {
    }
}
