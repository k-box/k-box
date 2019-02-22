<?php
/**
 * Script for testing that the database service is running
 *
 */
$dbname = 'dms_test';
$dbuser = 'dms';
$dbpass = 'unsafe';
$dbhost = '127.0.0.1';

$start = time();

while (true) {
    try {
        $dsn = "mysql:host=$dbhost;dbname=$dbname;charset=utf8";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, $dbuser, $dbpass, $opt);

        fwrite(STDOUT, 'Docker container started!'.PHP_EOL);
        exit(0);
    } catch (Exception $exception) {
        $elapsed = time() - $start;

        if ($elapsed > 30) {
            fwrite(STDERR, 'Docker container did not start in time...'.PHP_EOL);
            exit(1);
        }

        fwrite(STDOUT, 'Waiting for container to start...'.PHP_EOL);
        sleep(1);
    }
}
