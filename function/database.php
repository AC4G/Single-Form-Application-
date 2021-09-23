<?php

function getDB()
{
    static $db;
    if ($db instanceof PDO) {
        return $db;
    }
    if (!file_exists(CONFIG_DIR . '/database.php')) {
        redirectMissingConfig('config/database.php');
    }
    require_once CONFIG_DIR . '/database.php';
    $dsn = sprintf("mysql:host=%s;dbname=%s;charset=%s", DB_HOST, DB_DATABASE, DB_CHARSET);

    try{
        $db = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    }catch(PDOException $e){
        echo 'Connection failed: ' . $e;
    }
}

function printDBErrorMessage()
{
    $info = getDB()->errorInfo();
    if (isset($info[2])) {
        return $info[2];
    }
    return '';
}
