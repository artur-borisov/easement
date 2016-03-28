<?php 

require("../connector/grid_connector.php");
require("../connector/db_oracle.php");


$db['USSDGW']['connection_string'] = '(DESCRIPTION=(FAILOVER=ON)(CONNECT_TIMEOUT=30)(TRANSPORT_CONNECT_TIMEOUT=15)(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=10.0.2.3)(PORT=1521))(ADDRESS=(PROTOCOL=TCP)(HOST=10.0.2.13)(PORT=1521)))(CONNECT_DATA=(SERVICE_NAME=pcdbclient)))';
$db['USSDGW']['username'] = 'ussdgw';
$db['USSDGW']['password'] = 'xxnchhre';
$db['USSDGW']['character_set'] = 'AL32UTF8';
$db['USSDGW']['session_mode'] = null;


/*
sleep(1);

*/

$show['ROUTE_TABLE'] = array ('db' => 'USSDGW', 'method' => 'render_table', 'dataset' => 'T2_ROUTE_TABLE', 'columns' => 'GT_HLR_MASK,IMSI_MASK,MSISDN_MASK,RN,CALLINGGT,CALEDGT,OPERATOR,GT_HLR_GROUP_NAME');


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
