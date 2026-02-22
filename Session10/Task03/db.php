<?php

declare(strict_types=1);

/**
 * db.php – OOP-sabb DB kapcsolat: singleton jellegű factory.
 */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

final class Db
{
    private static ?mysqli $conn = null;

    public static function conn(): mysqli
    {
        if (self::$conn) return self::$conn;

        /** @var array{host:string,user:string,pass:string,db:string,port:int} $CFG */
        $CFG = require __DIR__ . '/config.php';

        $m = new mysqli($CFG['host'], $CFG['user'], $CFG['pass'], $CFG['db'], $CFG['port']);
        $m->set_charset('utf8mb4');
        self::$conn = $m;
        return self::$conn;
    }
}
