<?php

require('vendor/autoload.php');

use SergPopov\CloudMailRu\CloudMailRu;
use SergPopov\CloudMailRu\CloudMailRuException;

$username = 'username'; // учетная запись username@mail.ru
$password = 'password';

$cloud = new CloudMailRu($username, $password);

try {
    $list = $cloud->login()->folderList('/');
    var_dump($list);
} catch (CloudMailRuException $e) {
    echo $e->getMessage();
}

$pathLocalFile = __DIR__.'/testfile.txt';
$pathFileOnCloud = '/testdir/testfile.txt';
try {
    $url = $cloud->login()
        ->fileRemove($pathLocalFile)
        ->fileUpload($pathLocalFile, $pathFileOnCloud)
        ->filePublish($pathFileOnCloud);
    var_dump($url);
} catch (CloudMailRuException $e) {
    echo $e->getMessage();
}