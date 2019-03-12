
<?php
//删除指定文件夹以及文件夹下的所有文件
function deldir($dir)
{
    //先删除目录下的文件：
    $dh=opendir($dir);
    while ($file=readdir($dh)) {
        if ($file!="." && $file!="..") {
            $fullpath=$dir."/".$file;
            if (!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath);
            }
        }
    }
  
    closedir($dh);
    //删除当前文件夹：
    if (rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}

if (!isset($_FILES['upgrade'])) {
    return;
}

//备份配置文件
define('RootDir', __DIR__.'/../spoon');
define('ConfigDir', RootDir.'/conf');

rename(ConfigDir, __DIR__.'/conf');
deldir(RootDir);
$zip=new ZipArchive();
$zip->open($_FILES['upgrade']['tmp_name']);
$zip->extractTo(RootDir);
$zip->close();
rename(__DIR__.'/conf', ConfigDir);
echo '升级完成';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upgrade</title>
</head>
<body>
    <h1>系统升级</h1>
    <form action="#">
    <input type="file" name="upgrade"/>
    <button type="submit">上传</button>
    </form>
</body>
</html>