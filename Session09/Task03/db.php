<?php

declare(strict_types=1);

/**
 * db.php – MySQLi kapcsolat létrehozása
 * - config.php (saját géphez) nincs verziókezelve, config.sample.php alapján készítendő
 * - mysqli_report(): a MySQLi hibákat kivétellé alakítja (könnyebb try/catch)
 */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once __DIR__ . '/config.php';

function db(): mysqli
{
    /** @var array{host:string,user:string,pass:string,db:string,port:int} $CFG */
    $CFG = require __DIR__ . '/config.php';

    $mysqli = new mysqli($CFG['host'], $CFG['user'], $CFG['pass'], $CFG['db'], $CFG['port']);
    $mysqli->set_charset('utf8mb4'); // UTF-8
    return $mysqli;
}
