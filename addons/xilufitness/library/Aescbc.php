<?php


namespace addons\xilufitness\library;

/**
 * Class Aescbc
 * @package app\common\library
 * AES-CBC 加密解密方式
 */
class Aescbc
{

    private static $key="xilufitness_2023";

    private static $iv = "xilufitness_2022";

    /**
     * 加密
     * $data 加密的数据 json格式
     */
    public static function encryptWithOpenssl($data){
        return base64_encode(openssl_encrypt($data, "AES-128-CBC", self::$key, OPENSSL_RAW_DATA, self::$iv));
    }

    /**
     * 解密
     */
    public static function decryptWithOpenssl($data){
        return openssl_decrypt(base64_decode($data), "AES-128-CBC", self::$key, OPENSSL_RAW_DATA, self::$iv);
    }



}