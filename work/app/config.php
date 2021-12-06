<?php

session_start();

define('DSN', 'mysql:host=db;dbname=myapp;charset=utf8mb4');
define('DB_USER', 'myappuser');
define('DB_PASS', 'myapppass');
// define('SITE_URL', 'http://localhost:8562');
// 上記と同じ意味
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST']);

// クラスのオートロード
spl_autoload_register(function ($class) {
    $prefix = 'MyApp\\';
    // namespace($prefix)をクラス名から取り除く
    if (strpos($class, $prefix) === 0) {
        $fileName = sprintf(__DIR__ . '/%s.php', substr($class, strlen($prefix)));
        if(file_exists($fileName)) {
            // spl_auto〜を使うとクラスの重複ないので、requireでOK
            require($fileName);
        } else {
            echo 'File not found: ' . $fileName;
            exit;
        }
    }

});

