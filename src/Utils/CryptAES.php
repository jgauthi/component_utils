<?php
namespace Jgauthi\Component\Utils;

class CryptAES
{
    public const METHOD_AES_128 = 'AES-128-CBC';
    public const METHOD_AES_256 = 'AES-256-CBC';

    public function decrypt(string $encryptedMessage, string $aesMethod, string $secretHash, string $iv, int $options = 0): string
    {
        return openssl_decrypt($encryptedMessage, $aesMethod, $secretHash, $options, $iv);
    }

    public function encrypt(string $textToEncrypt, string $aesMethod, string $secretHash, string $iv, int $options = 0): string
    {
        return openssl_encrypt($textToEncrypt, $aesMethod, $secretHash, $options, $iv);
    }

    public function getRandomIv(string $aesMethod): string
    {
        return openssl_random_pseudo_bytes(openssl_cipher_iv_length($aesMethod));
    }
}
