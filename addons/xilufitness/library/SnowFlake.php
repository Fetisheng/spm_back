<?php


namespace addons\xilufitness\library;


/**
 * 雪花算法生成唯一id
 * Class SnowFlake
 * @package app\xilufitness\library
 */

class SnowFlake {

    protected static $instance;
    private static $machineId;

    private function __construct() {
        self::$machineId = ip2long(gethostbyname(gethostname()));
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createID(){
        // 假设一个机器id
//        $machineId = ip2long(gethostbyname(gethostname()));

        //41bit timestamp(毫秒)
        $time = floor(microtime(true) * 1000);

        //0bit 未使用
        $suffix = 0;

        //datacenterId  添加数据的时间
        $base = decbin(pow(2,40) - 1 + $time);

        //workerId  机器ID
        $machineid = decbin(pow(2,9) - 1 + self::$machineId);

        //毫秒类的计数
        $random = mt_rand(1, pow(2,11)-1);

        $random = decbin(pow(2,11)-1 + $random);
        //拼装所有数据
        $base64 = $suffix.$base.$machineid.$random;
        //将二进制转换int
        $base64 = bindec($base64);

        return sprintf('%.0f', $base64);
    }
}

//$id = (SnowFlake::getInstance())->createID();
