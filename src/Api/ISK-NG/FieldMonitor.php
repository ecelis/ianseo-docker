<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Fun_Modules.php');

if(empty($_SESSION["TourId"])) {
    $TourCode=GetIsParameter('IsCode');
    $TourId=getIdFromCode($TourCode);
    // opens the competition...
    CreateTourSession($TourId);
}
if(!($_SESSION["UseApi"] == ISK_NG_PRO_CODE or ($_SESSION["UseApi"] == ISK_NG_LIVE_CODE AND module_exists('ISK-NG_Live')))) {
    CD_redirect($CFG->ROOT_DIR);
}
checkACL(AclISKServer, AclReadOnly);
$PAGE_TITLE=get_text('FieldMonitor', 'Api');
$IncludeJquery = true;
$IncludeFA = true;

$JS_SCRIPT=array(
    phpVars2js(array(
        'isLive' => ($_SESSION["UseApi"] === ISK_NG_LIVE_CODE and module_exists('ISK-NG_Live')),
        'tourCode' => $_SESSION["TourCode"],
        'SocketIP'=>getModuleParameter('ISK-NG', 'SocketIP', gethostbyname($_SERVER['HTTP_HOST'])),
        'SocketPort'=>getModuleParameter('ISK-NG', 'SocketPort', '12346'),
    )),
    ($_SESSION["UseApi"] == ISK_NG_LIVE_CODE ? '<script type="text/javascript" src="./socket.js"></script>' : '<script></script>'),
    '<script type="text/javascript" src="./FieldMonitor.js"></script>',
    '<link href="./isk.css" rel="stylesheet" type="text/css">',
);

//Socket Header
echo '<table class="Tabella mb-3">';
echo '<tr><th class="Title" colspan="3">' . $PAGE_TITLE . '</th></tr>';
if($_SESSION["UseApi"] === ISK_NG_LIVE_CODE) {
    echo '<tr>' .
        '<th class="w-15">' . get_text('ISK-ConnectionStatus', 'Api') . '</th>' .
        '<td id="ctrConnStatus" class="socketOFF" ondblclick="changeMasterSocket()">DISCONNECTED</td>' .
        '</tr>';
}
echo '</table>';


//Groups
echo '<table class="Tabella">';
echo '<thead id="hGroups"><tr><th colspan="5">' . get_text('Controller', 'Api') . '</th></tr><tr>'.
    '<th class="w-20">' . get_text('API-Group', 'Api') . '</th>'.
    '<th class="w-30">' . get_text('Session') . '</th>'.
    '<th class="w-15">' . get_text('Distance', 'Tournament') . '</th>'.
    '<th class="w-25">' . get_text('Volee', 'HTT') . '</th>'.
    '<th class="w-10">' . get_text('ScoringCount','Api') . '</th>'.
    '</tr></thead>';
echo '<tbody id="bGroups"></tbody>';
echo '</table>';

echo '<div id="DeviceGroupsContainers"></div>';



include('Common/Templates/head-min.php');

include('Common/Templates/tail.php');