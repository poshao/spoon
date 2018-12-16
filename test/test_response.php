<?php
namespace TEST;
class Response{
    /**
     * 发送文本
     *
     * @return void
     */
    public function sendText(){
        \Spoon\Response::send(json_encode(array('hello'=>'world')));
    }

    /**
     * 发送图像
     *
     * @return void
     */
    public function sendImage(){
        $f=fopen('C:\\Users\\0115289\\Desktop\\mylogo.jpg','rb');
        $content=fread($f,filesize('C:\\Users\\0115289\\Desktop\\mylogo.jpg'));
        \Spoon\Response::send($content,200,'image/jpeg','');
        fclose($f);
    }
}
?>