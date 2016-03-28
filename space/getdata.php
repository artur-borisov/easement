<?php 

//require("ddos.php");


require("../connector/grid_connector.php");
require("../connector/db_oracle.php");

/* DATABASES */

$db['SPACE01']['connection_string'] = '(DESCRIPTION=(FAILOVER=ON)(CONNECT_TIMEOUT=30)(TRANSPORT_CONNECT_TIMEOUT=15)(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=10.0.2.3)(PORT=1521))(ADDRESS=(PROTOCOL=TCP)(HOST=10.0.2.13)(PORT=1521)))(CONNECT_DATA=(SERVICE_NAME=pcdbclient)))';
$db['SPACE01']['username'] = 'cpa_user';
$db['SPACE01']['password'] = 'ghjrtyu2';
$db['SPACE01']['character_set'] = 'AL32UTF8';
$db['SPACE01']['session_mode'] = null;

$db['INVOICE04']['connection_string'] = '(DESCRIPTION=(FAILOVER=ON)(CONNECT_TIMEOUT=30)(TRANSPORT_CONNECT_TIMEOUT=15)(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=10.0.2.1)(PORT=1521))(ADDRESS=(PROTOCOL=TCP)(HOST=10.0.2.11)(PORT=1521)))(CONNECT_DATA=(SERVICE_NAME=B4CLIENT)))';
$db['INVOICE04']['username'] = 'BORISOV_AAN';
$db['INVOICE04']['password'] = 'hN8Aw!nM';
$db['INVOICE04']['character_set'] = 'AL32UTF8';
$db['INVOICE04']['session_mode'] = null;


/*
sleep(1);

$db['SPACE01']['connection_string'] = '(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 127.0.0.1)(PORT = 1521)))(CONNECT_DATA = (SERVER = DEDICATED)(SERVICE_NAME = XE)))';
$db['SPACE01']['username'] = 'TEST';
$db['SPACE01']['password'] = 'TEST';
$db['SPACE01']['character_set'] = 'AL32UTF8';
$db['SPACE01']['session_mode'] = null;

$db['INVOICE04']['connection_string'] = '(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 127.0.0.1)(PORT = 1521)))(CONNECT_DATA = (SERVER = DEDICATED)(SERVICE_NAME = XE)))';
$db['INVOICE04']['username'] = 'TEST';
$db['INVOICE04']['password'] = 'TEST';
$db['INVOICE04']['character_set'] = 'AL32UTF8';
$db['INVOICE04']['session_mode'] = null;
*/

$show['SERVICES'] = array ('db' => 'SPACE01', 'method' => 'render_table', 'dataset' => 'T2_SERVICES', 'columns' => 'PROVIDER_NAME,PROVIDER_STATUS,SERVICE_NAME,SERVICE_STATUS,SERVICE_DESC,SERVICE_NUMBER,SERVICE_NUMBER_DEFAULT_FWD,SERVICE_TRAFFIC_TYPE,ACCOUNT_TRANSPORT_TYPE,SYSTEM_ID,ATTR_UNLIMPUSH,ATTR_CONFIRMAOC,ATTR_TARIFICATIONTYPE,ATTR_CHARGELEVEL,ATTR_CHARGELEVEL_COST,ATTR_CHARGELEVEL_COST_WO_TAX');
$show['PROVIDERS'] = array ('db' => 'SPACE01', 'method' => 'render_table', 'dataset' => 'T2_PROVIDERS', 'columns' => 'NAME,STATUS,EMAIL,DSC,CREATED,MODIFIED,SYSTEM_ID,PASS,TRANSPORT');

$show['INVOICECOST'] = array ('db' => 'INVOICE04', 'method' => 'render_sql', 'dataset' => 'SELECT SP_PHONE_NUM, TRPL_NAME, BRANCH_NAME, SERV_NAME, DECODE(CALL_TYPE,1,\'Исходящий\',CALL_TYPE) AS CALL_TYPE, CALT_NAME, METHOD, CONNECTION_PRICE, P_PRVD_NAME FROM BIBIK_VA.TEMP_BIBIK_CONTENT ORDER BY 1,2,3,4,5,6,7,8,9', 'columns' => 'SP_PHONE_NUM, TRPL_NAME, BRANCH_NAME, SERV_NAME, CALL_TYPE, CALT_NAME, METHOD, CONNECTION_PRICE, P_PRVD_NAME');


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