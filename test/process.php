<?php
/**
 * 测试加载类
 */
namespace TEST;
include __DIR__.'/test_response.php';

// $reponse=new Response();
// $reponse->sendText();
try{
    throw new \Spoon\Exception("hello");
}catch(\Spoon\Exception $e){
    $e->render();
}
?>