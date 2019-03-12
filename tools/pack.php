<?php
/** 递归打包文件夹
* @param $source
* @param $destination
* @return bool
*/
function package($source,$destination){
  if(is_file($destination)){
    unlink($destination);
  }
  $source=str_replace('\\','/',$source);
  if(is_dir($source)===false) return false;
  $zip=new ZipArchive();
  if(false===$zip->open($destination,ZipArchive::CREATE)){
    throw new Exception('create zip file failed');
  }

  $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
  foreach($files as $file){
    $file = str_replace('\\', '/', $file);
    // Ignore "." and ".." folders
    if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
        continue;

    $file = str_replace('\\', '/',realpath($file));
    if (is_dir($file) === true)
    {
        $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
    }
    else if (is_file($file) === true)
    {
      //排除日志文件
      $reg='/.*?\.log/';
      if (preg_match($reg, $file)==0) {
          $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
      }
    }
  }
  $zip->close();
  return true;
}
$buildPath=__DIR__.'/../build/upgrade.zip';

package(realpath(__DIR__.'/../spoon'),$buildPath);
echo "pack finished!    ".$buildPath;