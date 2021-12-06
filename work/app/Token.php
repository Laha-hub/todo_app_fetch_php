<?php

namespace MyApp;

class Token
{
    // データを送信する際は、必ずCSRF対策する（トークン使用）
    public static function create()
    {
        if(!isset($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
        }
    }

    // トークンのチェック
    public static function validate()
    {
        if (
            empty($_SESSION['token']) ||
            $_SESSION['token'] !== filter_input(INPUT_POST, 'token')
        ) {
            exit('Invalid post request');
        }
    }
}
