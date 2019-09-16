<?php
function ConnectMDB($filePath, $user, $passwd)
{
    $connectionString = "Driver={Microsoft Access Driver (*.mdb)};Dbq=". $filePath .";Uid=" . $user . ";Pwd=" . $passwd . ";";
    $_conn=new COM("ADODB.Connection");
    $_conn->Open($connectionString);
    return $_conn;
}

$conn=ConnectMDB('C:\Users\0115289\Desktop\all_in_one.mdb', 'sa', '');
$rs=$conn->Execute('select * from Hello');
var_dump($rs->Clone());
$conn->Close();
