<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once 'config.php';


$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$mysqli->set_charset("utf8");

function dd()
{
    echo '<pre>';
    array_map(function ($x) {
        print_r($x);
    }, func_get_args());
    die();
}

$result = $mysqli->query("SHOW TABLES LIKE '" . TABLE_NAME . "'");
if ($result->num_rows != 1) {
    $query = 'CREATE TABLE ' . TABLE_NAME . ' ( 
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
        email VARCHAR(50) NOT NULL,
        vorname VARCHAR(50) NOT NULL,
        nachname VARCHAR(50) NOT NULL,
        company VARCHAR(50) NOT NULL,
        password_hash VARCHAR(60) NULL,
        temp_hash VARCHAR(64) NULL,
        active BOOLEAN NOT NULL DEFAULT 1,
        welcome_mail TIMESTAMP NULL,
        import_time TIMESTAMP NULL,
        first_login TIMESTAMP NULL)';

    $mysqli->query($query);
}
