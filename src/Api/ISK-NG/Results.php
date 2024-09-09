<?php

require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/CommonLib.php');

CheckTourSession(true);
if(!($_SESSION["UseApi"] == ISK_NG_PRO_CODE or ($_SESSION["UseApi"] == ISK_NG_LIVE_CODE AND module_exists('ISK-NG_Live')))) {
    CD_redirect($CFG->ROOT_DIR);
}
checkACL(AclISKServer, AclReadWrite);

$PAGE_TITLE=get_text('ISK-Results');
$IncludeJquery = true;
$IncludeFA = true;

$JS_SCRIPT=array(
    phpVars2js(array(
        'isLive' => ($_SESSION["UseApi"] === ISK_NG_LIVE_CODE and module_exists('ISK-NG_Live')),
        'tourCode' => $_SESSION["TourCode"],
        'SocketIP'=>getModuleParameter('ISK-NG', 'SocketIP', gethostbyname($_SERVER['HTTP_HOST'])),
        'SocketPort'=>getModuleParameter('ISK-NG', 'SocketPort', '12346'),
        'msgRemove'=>get_text('ISK-Remove', 'Api'),
        'MsgConfirm'=>get_text('MsgAreYouSure'),
        'msgCmdCancel' => get_text('CmdCancel'),
        'msgCmdConfirm' => get_text('Confirm', 'Tournament'),
        'EmptyTemporaryOfGroup'=>(get_text('MsgEmptyTemporaryOfGroup','Api')),
        'Group'=>(get_text('API-Group', 'Api')),
	    'msgGetConnected' => get_text('CheckConnected', 'Api'),
	    'msgDownload' => get_text('Download', 'HTT'),
	    'msgCmdImport' => get_text('CmdImport','Api'),
	    'msgForceLoad' => get_text('Reload', 'Tournament'),
	    'msgTruncate' => get_text('ISK-TruncateTable', 'Api'),
        'msgPartialImportAll' => get_text('PartialImportAll', 'Api'),
    )),
    '<script type="text/javascript" src="./HackTimer.js"></script>',
    ($_SESSION["UseApi"] == ISK_NG_LIVE_CODE ? '<script type="text/javascript" src="./socket.js"></script>' : '<script></script>'),
    '<script type="text/javascript" src="./Results.js"></script>',
    '<link href="./isk.css" rel="stylesheet" type="text/css">',
);

include('Common/Templates/head.php');

echo '<table class="Tabella mb-3">';
echo '<tr><th class="Title" colspan="5">' . get_text('ISK-Configuration') . '</th></tr>';
if($_SESSION["UseApi"] === ISK_NG_LIVE_CODE) {
    echo '<tr>' .
        '<th class="w-15">' . get_text('ISK-ConnectionStatus', 'Api') . '</th>' .
        '<th class="w-20">' . get_text('Masters', 'Api') . '</th>' .
        '<th class="w-65">' . get_text('Notes', 'Tournament') . '</th>' .
        '</tr>';
    echo '<tr>' .
        '<td id="ctrConnStatus" class="socketOFF" ondblclick="changeMasterSocket()">DISCONNECTED</td>' .
        '<td class="txtFixW"><span id="ctrMastersNo" class="TargetAssigned"></span><span id="ctrMasters"></span></td>' .
        '<td id="IskLog"></td>' .
        '</tr>';
}
echo '</table>';


echo '<table class="Tabella">';
echo '<thead id="hGroups"><tr><th colspan="15">' . get_text('Controller', 'Api') . '</th></tr><tr>'.
    '<th class="w-5">' . get_text('API-Group', 'Api') . '</th>'.
    '<th class="w-35">' . get_text('Session') . '</th>'.
    '<th class="w-5">' . get_text('Distance', 'Tournament') . '</th>'.
    '<th class="w-5">' . get_text('Volee', 'HTT') . '</th>';
if($_SESSION["UseApi"] === ISK_NG_LIVE_CODE) {
	echo '<th class="w-5">' . get_text('Download', 'HTT') . '</th>';
}
echo '<th class="w-5">' . get_text('Reload', 'Tournament') . '</th>'.
    '<th class="w-5">' . get_text('CmdImport','Api') . '</th>'.
    '<th class="w-5">' . get_text('ScoringCount','Api') . '</th>'.
    '<th class="w-5">' . get_text('ViewSize', 'Api') . '</th>'.
    '<th class="w-5">' . get_text('AutoImport', 'Api') . '</th>'.
    '<th class="w-5">' . get_text('PartialImport', 'Api') . '</th>'.
    '<th class="w-15">' . get_text('ISK-TruncateTable', 'Api') . '</th>'.
    '</tr></thead>';
echo '<tbody id="bGroups"></tbody>';
echo '</table>';

echo '<div id="DeviceGroupsContainers"></div>';

echo '<div class="footer mt-5" style="display:flex; flex-wrap: wrap">';
foreach(['Let-1','Let-2','Let-3','Let-4','Let-O','Let-G','Let-F'] as $l) {
	echo '<div class="d-inline-block m-2 mx-5">
		<span class="'.$l.' mr-2" style="padding:0 1em;">&nbsp;&nbsp;</span>'.get_text('Desc-'.$l, 'Api').'
		</div>';
}
echo '</div>';

include('Common/Templates/tail.php');