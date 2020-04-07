<?php

$dsn1 = 'mysql:host=127.0.0.1;port=3356;dbname=test';
$dsn2 = 'mysql:host=127.0.0.1;port=3357;dbname=test';
$username = 'root';
$password = 'password';

$dbtest1 = new PDO($dsn1, $username, $password) or die("dbtest1 连接失败");
$dbtest2 = new PDO($dsn2, $username, $password) or die("dbtest2 连接失败");


try {
    //$dbtest1
    $dbtest1->beginTransaction();
    $return = $dbtest1->exec("update classes set name = '111' where id = 3;");
    if ($return == false) {
        throw new Exception("库test@mysql6执行update classes操作失败！");
    }
    
    //$dbtest2
    $dbtest2->beginTransaction();
    // $return = $dbtest2->query("update classes set name = '四班1' where id = 4;");
    $return = $dbtest2->query("insert into classes(id, name) value (4, '4');");
    if ($return == false) {
        throw new Exception("库test@mysql7执行update classes操作失败！");
    }
    $dbtest1->commit();
    $dbtest2->commit();
} catch (Exception $e) {
    //阶段2：回滚
    $dbtest1->rollBack();
    $dbtest2->rollBack();
    die($e->getMessage());
}

$dbtest1 = null;
$dbtest2 = null;