<?php 

require("../connector/grid_connector.php");
require("../connector/db_oracle.php");


$db['SMSC01']['connection_string'] = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=10.0.2.30)(PORT=1521)))(CONNECT_DATA=(SERVICE_NAME=smsc01_dir)))';
$db['SMSC01']['username'] = 'SMSC01_DIR';
$db['SMSC01']['password'] = 'smsco3tech';
$db['SMSC01']['character_set'] = 'AL32UTF8';
$db['SMSC01']['session_mode'] = null;

$db['SMSC02']['connection_string'] = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=10.0.2.30)(PORT=1521)))(CONNECT_DATA=(SERVICE_NAME=smsc01_dir)))';
$db['SMSC02']['username'] = 'MNSMSC02_DIR';
$db['SMSC02']['password'] = 'smsco3tech';
$db['SMSC02']['character_set'] = 'AL32UTF8';
$db['SMSC02']['session_mode'] = null;

$db['SMSC03']['connection_string'] = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=10.0.2.30)(PORT=1521)))(CONNECT_DATA=(SERVICE_NAME=smsc01_dir)))';
$db['SMSC03']['username'] = 'MNSMSC03_DIR';
$db['SMSC03']['password'] = 'smsco3tech';
$db['SMSC03']['character_set'] = 'AL32UTF8';
$db['SMSC03']['session_mode'] = null;

/*
sleep(1);

$db['SMSC01']['connection_string'] = '(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 127.0.0.1)(PORT = 1521)))(CONNECT_DATA = (SERVER = DEDICATED)(SERVICE_NAME = XE)))';
$db['SMSC01']['username'] = 'TEST';
$db['SMSC01']['password'] = 'TEST';
$db['SMSC01']['character_set'] = 'AL32UTF8';
$db['SMSC01']['session_mode'] = null;

$db['SMSC02']['connection_string'] = '(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 127.0.0.1)(PORT = 1521)))(CONNECT_DATA = (SERVER = DEDICATED)(SERVICE_NAME = XE)))';
$db['SMSC02']['username'] = 'TEST';
$db['SMSC02']['password'] = 'TEST';
$db['SMSC02']['character_set'] = 'AL32UTF8';
$db['SMSC02']['session_mode'] = null;

$db['SMSC03']['connection_string'] = '(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 127.0.0.1)(PORT = 1521)))(CONNECT_DATA = (SERVER = DEDICATED)(SERVICE_NAME = XE)))';
$db['SMSC03']['username'] = 'TEST';
$db['SMSC03']['password'] = 'TEST';
$db['SMSC03']['character_set'] = 'AL32UTF8';
$db['SMSC03']['session_mode'] = null;
*/

$show['DELIVERY_SCHEME01'] = array ('db' => 'SMSC01', 'method' => 'render_table', 'dataset' => 'T2_DELIVERY_SCHEME', 'columns' => 'DELIVERY_NM,DELIVERY_AB,ATTEMPT,NETWORK_NM,ERROR_ID,ERROR_MAP,ERROR_NM,DELAY,DELAY_SRC');
$show['DELIVERY_SCHEME02'] = array ('db' => 'SMSC02', 'method' => 'render_table', 'dataset' => 'T2_DELIVERY_SCHEME', 'columns' => 'DELIVERY_NM,DELIVERY_AB,ATTEMPT,NETWORK_NM,ERROR_ID,ERROR_MAP,ERROR_NM,DELAY,DELAY_SRC');
$show['DELIVERY_SCHEME03'] = array ('db' => 'SMSC03', 'method' => 'render_table', 'dataset' => 'T2_DELIVERY_SCHEME', 'columns' => 'DELIVERY_NM,DELIVERY_AB,ATTEMPT,NETWORK_NM,ERROR_ID,ERROR_MAP,ERROR_NM,DELAY,DELAY_SRC');


if (! isset($_GET['show'])) {
    print "Error. Required parameter is not specified.";
    exit;
}

if (! array_key_exists($_GET['show'], $show)) {
    print "Error. Required parameter is invalid.";
    exit;
}

require("ddos.php");
$ddos=ddos(strtr($_SERVER["SCRIPT_NAME"], "/", "_").".".$_GET['show'].".".$_SERVER["PHP_AUTH_USER"].".lock", 30);
if ($ddos != 0) {
  print "Error. A lot of queries. Wait $ddos seconds.";
  exit;
}


$conn = oci_connect($db[$show[$_GET['show']]['db']]['username'], $db[$show[$_GET['show']]['db']]['password'], $db[$show[$_GET['show']]['db']]['connection_string'], $db[$show[$_GET['show']]['db']]['character_set'], $db[$show[$_GET['show']]['db']]['session_mode']);

if ($conn) {

    $stid = oci_parse($conn, "alter session set nls_date_format='DD.MM.YYYY HH24:MI'");
    if ($stid) {
      oci_execute($stid);
    }

    $gridConn = new GridConnector($conn,"Oracle");

    switch ($show[$_GET['show']]['method']) {
        case "render_table":
            $gridConn->render_table($show[$_GET['show']]['dataset'], "", $show[$_GET['show']]['columns']);
            break;
        case "render_sql":
            $gridConn->render_sql($show[$_GET['show']]['dataset'], "", $show[$_GET['show']]['columns']);
            break;
    }

    oci_close($conn);
}

?>
