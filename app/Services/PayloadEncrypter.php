<?php

namespace App\Services;

use Illuminate\Encryption\Encrypter;

class PayloadEncrypter
{
    private function getMasterKey(): string
    {
        $allKeys = env("PAYLOAD_KEY");

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
