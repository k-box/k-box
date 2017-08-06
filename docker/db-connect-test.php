<?php

# Fill our vars and run on cli
# $ php -f db-connect-test.php -- -d database -u user -p password -H hostname
#
#

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

$connect = mysql_connect($dbhost, $dbuser, $dbpass) or err_exit("Unable to Connect to '$dbhost'");
mysql_select_db($dbname) or err_exit("Could not open the db '$dbname'");
$test_query = "SHOW TABLES FROM $dbname";
$result = mysql_query($test_query);
$tblCnt = 0;
while ($tbl = mysql_fetch_array($result)) {
    $tblCnt++;
  #echo $tbl[0]."<br />\n";
}
if (! $tblCnt) {
    echo "There are no tables<br />\n";
} else {
    echo "There are $tblCnt tables<br />\n";
}
