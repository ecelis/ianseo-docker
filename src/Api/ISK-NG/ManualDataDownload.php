<?php

require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/CommonLib.php');

CheckTourSession(true);
if(!($_SESSION["UseApi"] == ISK_NG_LIVE_CODE AND module_exists('ISK-NG_Live'))) {
    CD_redirect($CFG->ROOT_DIR);
}
checkACL(AclISKServer, AclReadWrite);

$PAGE_TITLE=get_text('ISK-GetQRData');
$IncludeJquery = true;
//$IncludeFA = true;

$JS_SCRIPT=array(
    phpVars2js(array(
        'isLive' => ($_SESSION["UseApi"] === ISK_NG_LIVE_CODE and module_exists('ISK-NG_Live')),
        'tourCode' => $_SESSION["TourCode"],
        'SocketIP'=>getModuleParameter('ISK-NG', 'SocketIP', gethostbyname($_SERVER['HTTP_HOST'])),
        'SocketPort'=>getModuleParameter('ISK-NG', 'SocketPort', '12346'),
        'Error' => get_text('Error'),
        'WrongData' => get_text('WrongData', 'Errors')
    )),
    ($_SESSION["UseApi"] == ISK_NG_LIVE_CODE ? '<script type="text/javascript" src="./socket.js"></script>' : '<script></script>'),
    '<script type="text/javascript" src="./ManualDataDownload.js"></script>',
    '<link href="./isk.css" rel="stylesheet" type="text/css">',
);

include('Common/Templates/head.php');

echo '<table class="Tabella mb-3">'.
    '<tr><th colspan="3" class="Title">' . $PAGE_TITLE. '</th></tr>';
echo '<tr>'.
    (($_SESSION["UseApi"] === ISK_NG_LIVE_CODE) ?
        '<th class="w-15">' . get_text('ISK-ConnectionStatus', 'Api') . '</th>'.
        '<th class="w-35">' . get_text('Masters', 'Api') . '</th>'.
        '<th class="w-50">' . get_text('ISK-DeviceId', 'Api') . '</th>'
        : ''
    ).
    '</tr>';
echo '<tr>'.
    (($_SESSION["UseApi"] === ISK_NG_LIVE_CODE) ?
        '<td id="ctrConnStatus" class="socketOFF" ondblclick="changeMasterSocket()">DISCONNECTED</td>'.
        '<td class="txtFixW"><span id="ctrMastersNo" class="TargetAssigned"></span><span id="ctrMasters"></span></td>'.
        '<td class="txtFixW"><span id="runningDeviceId" class="TargetAssigned"></span><span class="ml-3" id="qrLastRead"></span></td>'
        : ''
    ).
    '</tr>';
echo '<tr><td colspan="3" class="Center"><textarea id="data" class="w-80" name="data" rows="25"></textarea></td></tr>'.
    '<tr><td colspan="3" class="Center"><input type="button" onclick="sendMsg()" value="'.get_text("CmdSend","Tournament").'"></td></tr>'.
    '</table>';

include('Common/Templates/tail.php');