<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/CommonLib.php');

CheckTourSession(true);
if($_SESSION["UseApi"] != ISK_NG_LIVE_CODE or !module_exists('ISK-NG_Live')) {
    CD_redirect($CFG->ROOT_DIR);
}
checkACL(AclISKServer, AclReadWrite);

$PAGE_TITLE=get_text('MenuLM_Get Info');
$IncludeJquery = true;
$IncludeFA = true;

$JS_SCRIPT=array(
    phpVars2js(array(
        'isLive' => ($_SESSION["UseApi"] === ISK_NG_LIVE_CODE and module_exists('ISK-NG_Live')),
        'tourCode' => $_SESSION["TourCode"],
        'SocketIP'=>getModuleParameter('ISK-NG', 'SocketIP', gethostbyname($_SERVER['HTTP_HOST'])),
        'SocketPort'=>getModuleParameter('ISK-NG', 'SocketPort', '12346')
    )),
    '<script type="text/javascript" src="./socket.js"></script>',
    '<script type="text/javascript" src="./DevicesInfo.js"></script>',
    '<link href="isk.css" rel="stylesheet" type="text/css">'
);

include('Common/Templates/head.php');
echo '<table class="Tabella mb-3">';
echo '<tr><th class="Title" colspan="5">' . get_text('ISK-Configuration') . '</th></tr>';

$grpOptions = array();
$Sql = "SELECT DISTINCT IskDvGroup FROM IskDevices WHERE IskDvTournament=" . $_SESSION["TourId"] . " ORDER BY IskDvGroup";
$q=safe_r_SQL($Sql);
if(safe_num_rows($q) != 1) {
    $grpOptions[-1] = '<option value="-1">---</option>';
}
while($r=safe_fetch($q)) {
    $grpOptions[$r->IskDvGroup] = '<option value="'.$r->IskDvGroup.'">'.chr(65+$r->IskDvGroup).'</option>';
}

echo '<tr>'.
    '<th class="w-15">' . get_text('ISK-ConnectionStatus', 'Api') . '</th>'.
    '<th class="w-20">' . get_text('Masters', 'Api') . '</th>'.
    '<th class="w-10">' . get_text('API-Group', 'Api') . '</th>'.
    '<th class="w-45">' . get_text('API-Targets', 'Api') . '</th>'.
    '<th class="w-10">&nbsp;</th>'.
    '</tr>';
echo '<tr>'.
    '<td id="ctrConnStatus" class="socketOFF" ondblclick="changeMasterSocket()">DISCONNECTED</td>'.
    '<td class="txtFixW"><span id="ctrMastersNo" class="TargetAssigned"></span><span id="ctrMasters"></span></td>'.
    '<td class="Center"><select id="infoGroup">'.implode($grpOptions).'</select></td>'.
    '<td><input type="text" id="infoTarget" class="w-100"></td>'.
    '<td class="Center"><input type="button" value="'.get_text('CmdOk').'" onclick="getInfo()"></td>'.
    '</tr>';
echo '</table>';

echo '<table class="Tabella">';
echo '<thead id="hGroups"><tr>'.
    '<th class="w-5">' . get_text('API-Group', 'Api') . '</th>'.
    '<th class="w-5">' .  get_text('Target') . '</th>'.
    '<th class="w-15">' . get_text('ISK-DeviceId', 'Api') . '</th>'.
    '<th colspan="3">' . get_text('SetupPDA', 'Tournament') . '</th>'.
    '</tr></thead>';
echo '<tbody id="bDevices"></tbody>';
echo '</table>';

include('Common/Templates/tail.php');