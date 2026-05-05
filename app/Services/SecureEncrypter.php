<?php

namespace App\Services;

use Illuminate\Encryption\Encrypter;

class SecureEncrypter
{
    private function getMasterKey(): string
    {
        $allKeys =
            env("KEY_1") .
            env("KEY_2") .
            env("KEY_3") .
            env("KEY_4") .
            env("KEY_5");

        return hash("sha256", $allKeys, true);
    }

    public function encrypt($value)
    {
        $encrypter = new Encrypter($this->getMasterKey(), "AES-256-CBC");
        return $encrypter->encrypt($value);
    }

    public function decrypt($payload)
    {
        $encrypter = new Encrypter($this->getMasterKey(), "AES-256-CBC");
        return $encrypter->decrypt($payload);
    }
}
