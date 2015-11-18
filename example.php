<?php

require 'CloudMailRu.php';

$cloud = new CloudMailRu('user','pass');

if ($cloud->login()) {
    
    $file = dirname(__FILE__).'/test_file.txt';
    $file_cloud = '/dir/test_file.txt';
    
    $url = $cloud->loadFileAhdPublish($file, $file_cloud);
    
    if ($url !== "error") {
        echo 'ссылка для скачивания - '.$url;
    } else {
        echo 'загрузка в облако не удалась';
    }
    
} else {
    echo 'не прошли авторизацию';
}

unset($cloud);
