<?php

/**
 * Script for testing that the database service is running
 *
 * CLI usage:
 * php -f db-connect-test.php -- -d database -u user -p password -H hostname
 */

$options = getopt("d:u:p:H:");

$dbname = $options["d"];
$dbuser = $options["u"];
$dbpass = $options["p"];
$dbhost = $options["H"];

function err_exit($msg)
{
    fwrite(STDERR, "$msg\n");
    exit(1); // A response code other than 0 is a failure
}

try
{

    $dsn = "mysql:host=$dbhost;dbname=$dbname;charset=utf8";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $dbuser, $dbpass, $opt);

}
catch(Exception $ex)
{
    err_exit($ex->getMessage());
}