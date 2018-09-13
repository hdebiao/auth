<?php
require_once './vendor/autoload.php';

use Hdb\Auth\Auth;

header("Content-type: text/html; charset=utf-8");

$dataConfig = array(
    'host' => 'localhost',
    'user' => 'root',
    'password' => '1122',
    'port' => 3306,
    'database' => 'auth'
);

$action = array_key_exists('action', $_GET) ? $_GET['action'] : '';
$uid = array_key_exists('uid', $_GET) ? $_GET['uid'] : 0;

//if (empty($action) || empty($uid)) {
//    echo '参数错误';
//    exit();
//}
//switch ($action) {
//
//    case 'addRule' :
//
//        break;
//
//    case  'deleteRule' :
//        break;
//
//    default :
//        break;
//}


$auth = new Auth($dataConfig);


$tt = $auth->checkRule('checkProject', 2);
//print_r($tt);exit();
if (true === $tt) {
    echo '有权限';
} else {
    echo '没有权限';
}


function checkProject($uid, $projectId)
{

}

function callProjectUser($uid, $projectId)
{

}


function deleteRule($ruleId, $uid)
{

}


function checkPostParams($data)
{
    $res = array();
    $params = array(
        'uid' => array('type' => 'int'),
        'action' => array('type' => 'string'),
        'name' => array('type' => 'string', 'length' => '255'),
        'groups',
        'status',
        'rules',
        'username'
    );

    foreach ($params as $k => $v) {

    }

}






