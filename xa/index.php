<?php

$dsn1 = 'mysql:host=127.0.0.1;port=3356;dbname=test';
$dsn2 = 'mysql:host=127.0.0.1;port=3357;dbname=test';
$username = 'root';
$password = 'password';

$dbtest1 = new PDO($dsn1, $username, $password) or die("dbtest1 连接失败");
$dbtest2 = new PDO($dsn2, $username, $password) or die("dbtest2 连接失败");

//为XA事务指定一个id，xid 必须是一个唯一值。
$xid = uniqid(rand(10000, 99999));

//两个库指定同一个事务id，表明这两个库的操作处于同一事务中
$dbtest1->query("XA START '$xid'");//准备事务1
$dbtest2->query("XA START '$xid'");//准备事务2

try {
    //$dbtest1
    $return = $dbtest1->query("update classes set name = '三班' where id = 3;");
    if ($return == false) {
        throw new Exception("库test@mysql6执行update classes操作失败！");
    }
    
    //$dbtest2
    $return = $dbtest2->query("update classes set name = '四班' where id = 4;");
    // $return = $dbtest2->query("insert into classes(id, name) value (4, '4');");
    if ($return == false) {
        throw new Exception("库test@mysql7执行update classes操作失败！");
    }
    
    //阶段1：$dbtest1提交准备就绪
    $dbtest1->query("XA END '$xid'");
    $dbtest1->query("XA PREPARE '$xid'");
    //阶段1：$dbtest2提交准备就绪
    $dbtest2->query("XA END '$xid'");
    $dbtest2->query("XA PREPARE '$xid'");
    
    //阶段2：提交两个库
    $dbtest1->query("XA COMMIT '$xid'");
    $dbtest2->query("XA COMMIT '$xid'");
} catch (Exception $e) {
    //阶段2：回滚
    $dbtest1->query("XA ROLLBACK '$xid'");
    $dbtest2->query("XA ROLLBACK '$xid'");
    die($e->getMessage());
}

$dbtest1 = null;
$dbtest2 = null;