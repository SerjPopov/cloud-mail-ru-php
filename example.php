<?php

require('vendor/autoload.php');

use SergPopov\CloudMailRu\CloudMailRu;
use SergPopov\CloudMailRu\CloudMailRuException;

$username = 'username'; // учетная запись username
$domain = 'mail.ru';
$password = 'password';

$cloud = new CloudMailRu($username, $domain, $password);

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
        ->fileRemove($pathFileOnCloud)
        ->fileUpload($pathLocalFile, $pathFileOnCloud)
        ->filePublish($pathFileOnCloud);
    var_dump($url);
} catch (CloudMailRuException $e) {
    echo $e->getMessage();
}