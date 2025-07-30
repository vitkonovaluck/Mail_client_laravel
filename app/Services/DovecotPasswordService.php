<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;

class DovecotPasswordService
{
    public static function hash(string $password): string
    {
        // Спосіб 1: Виклик doveadm (потрібен доступ до shell)
        $result = Process::run("doveadm pw -s SHA512-CRYPT -p '".addslashes($password)."'");
        return trim($result->output());

        // Спосіб 2: Чистий PHP (менш точний, але без doveadm)
        // $salt = bin2hex(random_bytes(8));
        // return crypt($password, '$6$rounds=656000$'.$salt.'$');
    }
}
