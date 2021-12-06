<?php

namespace MyApp;

class Database
{
    // DB接続を１つにするための変数
    private static $instance;

    public static function getInstance()
    {
        try {
            if (!isset(self::$instance)) {
                self::$instance = new \PDO(
                    DSN,
                    DB_USER,
                    DB_PASS,
                    [
                        // namespaceを使用する場合、PDO標準クラスに'\'を付ける
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
                        // 指定した型でデータを取得
                        \PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            }

            return self::$instance;
        } catch (\PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }
}
