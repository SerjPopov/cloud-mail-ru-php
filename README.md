# PHP библиотека для работы с облаком [cloud.mail.ru](http://cloud.mail.ru)

## Описание
Реализована работа с некоторыми функциями облака [cloud.mail.ru](http://cloud.mail.ru).

Для работы потребуются данные для входа в учетную запись на mail.ru.

Перед использованием ознакомьтесь с [лицензионным соглашением по использованию Сервиса Облако@mail.ru](https://cloud.mail.ru/LA/)

## Методы
* folderList - получение списка каталогов и файлов
* folderAdd - добавление каталога в облако
* fileUpload - загрузка файла
* fileRemove - удаление файла
* filePublish - публикация файла

## Использование
```php
require('vendor/autoload.php');

use SergPopov\CloudMailRu\CloudMailRu;
use SergPopov\CloudMailRu\CloudMailRuException;

$username = 'username'; // учетная запись username@mail.ru
$password = 'password';
$pathLocalFile = __DIR__.'/testfile.txt';
$pathFileOnCloud = '/testdir/testfile.txt';

$cloud = new CloudMailRu($username, $password);
try {
    $url = $cloud->login()
        ->fileRemove($pathLocalFile)
        ->fileUpload($pathLocalFile, $pathFileOnCloud)
        ->filePublish($pathFileOnCloud);
    var_dump($url);
} catch (CloudMailRuException $e) {
    echo $e->getMessage();
}
```

[Пример использования example.php](example.php)

## Описание изменений

### 2.0.0
Библиотека полностью переписана.
Требуется версия PHP 7.0 и выше

### 1.0.0
Устаревшая версия.

## Licence
GNU GPL v2.0