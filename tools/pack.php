<?php
/** 递归打包文件夹
* @param $source
* @param $destination
* @return bool
*/
function package($source, $destination)
{
    if (is_file($destination)) {
        unlink($destination);
    }
    $source=str_replace('\\', '/', $source);
    if (is_dir($source)===false) {
        return false;
    }
    $zip=new ZipArchive();
    if (false===$zip->open($destination, ZipArchive::CREATE)) {
        throw new Exception('create zip file failed');
    }

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
    foreach ($files as $file) {
        $file = str_replace('\\', '/', $file);
        // Ignore "." and ".." folders
        if (in_array(substr($file, strrpos($file, '/')+1), array('.', '..'))) {
            continue;
        }

        $file = str_replace('\\', '/', realpath($file));
        if (is_dir($file) === true) {
            $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
        } elseif (is_file($file) === true) {
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

/**
 * 文件上传
 *
 * @param string $url
 * @param string $filePath
 * @return void
 */
function uploadFile($url, $filePath)
{
    $postData=array('upgrade'=>new CURLFile($filePath, mime_content_type($filePath), 'upgrade.zip'),basename($filePath));

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

//远程更新地址
$remoteUrl='http://147.121.214.25/webapp/api.old/public/upgrade.php';
//源码目录
$sourceDir=realpath(__DIR__.'/../spoon');
//打包文件目录
$packageFile=__DIR__.'/../build/upgrade.zip';


//打包
if (package($sourceDir, $packageFile)===false) {
    throw new Exception('源码打包失败');
}
//推送服务端
echo "结果: " .uploadFile($remoteUrl, $packageFile);
