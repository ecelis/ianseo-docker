<?php

require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/CommonLib.php');

CheckTourSession(true);
if(!($_SESSION["UseApi"] == ISK_NG_PRO_CODE or ($_SESSION["UseApi"] == ISK_NG_LIVE_CODE AND module_exists('ISK-NG_Live')))) {
    CD_redirect($CFG->ROOT_DIR);
}
checkACL(AclISKServer, AclReadWrite);

if(isset($_REQUEST['export'])) {
    $JSON=array();
    $q=safe_r_sql("select IskDvDevice, IskDvGroup, IskDvVersion, IskDvAppVersion, IskDvCode, IskDvTarget, IskDvTargetReq, IskDvAuthRequest, IskDvProActive from IskDevices");
    while($r=safe_fetch($q)) {
        $JSON[]=$r;
    }
    if($JSON) {
        JsonOut($JSON, false, 'Content-disposition: attachment; filename=devices-'.$_SESSION['TourCode'].'-'.$_SERVER['SERVER_NAME'].'-'.date('Ymd\THis').'.json');
    }
    CD_redirect($_SERVER['PHP_SELF']);
} elseif (isset($_REQUEST['delete'])) {
    $q = safe_w_sql("DELETE FROM IskDevices");
    CD_redirect($_SERVER['PHP_SELF']);
} else if(!empty($_FILES['devices']) and !$_FILES['devices']['error'] and $_FILES['devices']['size']) {
    if($Devices=json_decode(file_get_contents($_FILES['devices']['tmp_name']))) {
        $Groups=getModuleParameter('ISK-NG', 'Sequence');
        $ToReset=array();
        foreach($Devices as $Device) {
            $SQL=array( 'IskDvTournament='.$_SESSION['TourId']);
            $KeyCode='';
            if(!empty($Groups[$Device->IskDvGroup])) {
                // $KeyCode=$Groups[$Device->IskDvGroup]["type"] . ($Groups[$Device->IskDvGroup]["subtype"]??'') . $Groups[$Device->IskDvGroup]["maxdist"] . $Groups[$Device->IskDvGroup]["session"];
                $KeyCode=$Groups[$Device->IskDvGroup]["IskKey"];
                $SQL[]="IskDvSchedKey=". StrSafe_DB($KeyCode);
            } else {
                $SQL[]="IskDvSchedKey=''";
            }
            foreach($Device as $k=>$v) {
                $SQL[]="$k=".StrSafe_DB($v);
            }
            safe_w_sql("insert into IskDevices set ".implode(',', $SQL). " on duplicate key update ".implode(',', $SQL) );
        }
    }
    unlink($_FILES['devices']['tmp_name']);
    CD_redirect($_SERVER['PHP_SELF']);
}


$scheduleOpts = array();
foreach(getApiScheduledSessions() as $r) {
    $scheduleOpts[$r->keyValue] = array(
		'key'=>$r->keyValue,
	    'value'=>$r->Description,
	    'type'=>$r->Type,
	    'distances'=>($r->Type =='Q' ? count(explode(',',$r->MaxEnds)) : 0)
    );
}

$UsePersonalDevices=getModuleParameter('ISK-NG', 'UsePersonalDevices');

$PAGE_TITLE=get_text('ISK-Configuration');
$IncludeJquery = true;
$IncludeFA = true;

$JS_SCRIPT=array(
    phpVars2js(array(
        'isLive' => ($_SESSION["UseApi"] === ISK_NG_LIVE_CODE and module_exists('ISK-NG_Live')),
        'isPro' => ($_SESSION["UseApi"] === ISK_NG_PRO_CODE),
        'usePersonal' => ($UsePersonalDevices ? true : false),
        'tourCode' => $_SESSION["TourCode"],
        'reqAppVersion' => reqAppVersion,
        'SocketIP'=>getModuleParameter('ISK-NG', 'SocketIP', gethostbyname($_SERVER['HTTP_HOST'])),
        'SocketPort'=>getModuleParameter('ISK-NG', 'SocketPort', '12346'),
        'scheduleOpts' => array_values($scheduleOpts),
        'scheduleIndex' => array_keys($scheduleOpts),
        'msgRemove'=>get_text('ISK-Remove', 'Api'),
        'MsgConfirm'=>get_text('MsgAreYouSure'),
        'msgCmdSend' => get_text('CmdSend', 'Tournament'),
        'msgCmdCancel' => get_text('CmdCancel'),
        'msgCmdConfirm' => get_text('Confirm', 'Tournament'),
        'msgCmdInfo' => get_text('cmdInfo'),
        'msgCmdOff' => get_text('CmdOff'),
        'msgCmdOn' => get_text('CmdOn'),
        'allDistances' => get_text('AllDistances', 'Tournament'),
        'TitleDelAllTablets'=>(get_text('MsgDelAllTablets', 'Api')),
        'TitleDelTablet'=>(get_text('MsgDelTablet', 'Api')),
        'txtDeviceId'=>get_text('ISK-DeviceId', 'Api'),
        'txtTgt'=>get_text('Target'),
        'txtDevConnected'=>get_text('DevConnected', 'Api'),
        'txtDevDisconnected'=>get_text('DevDisconnected', 'Api'),
        'txtOnlyToday'=>get_text('ScheduleToday', 'Tournament'),
        'txtUnfinished'=>get_text('ScheduleUnfinished', 'Tournament'),
    )),
    ($_SESSION["UseApi"] == ISK_NG_LIVE_CODE ? '<script type="text/javascript" src="./socket.js"></script>' : '<script></script>'),
    '<script type="text/javascript" src="./Devices.js"></script>',
    '<link href="./isk.css" rel="stylesheet" type="text/css">',
);

$tmpOptionsGrp = '';
foreach(range('A','Z') as $k) {
    $tmpOptionsGrp.='<option value="'.(ord($k)-65).'">'.$k.'</option>';
}

include('Common/Templates/head.php');

$ColSpan=5+($UsePersonalDevices?1:0)+($_SESSION["UseApi"] === ISK_NG_LIVE_CODE?2:0);

echo '<table class="Tabella mb-3">';
echo '<tr><th class="Title" colspan="5">' . get_text('ISK-Configuration') . '</th></tr>';
echo '<tr>'.
    (($_SESSION["UseApi"] === ISK_NG_LIVE_CODE) ?
        '<th class="w-15">' . get_text('ISK-ConnectionStatus', 'Api') . '</th>'.
        '<th class="w-20">' . get_text('Masters', 'Api') . '</th>'
        : ''
    ).
    '<th class="w-35">' . get_text('ISK-ImportDevices', 'Api') . '</th>'.
    '<th class="w-15">' . get_text('ISK-ExportDevices', 'Api') . '</th>'.
    '<th class="w-15">' . get_text('ISK-DeleteDevices', 'Api') . '</th>'.
    '</tr>';
echo '<tr>'.
    (($_SESSION["UseApi"] === ISK_NG_LIVE_CODE) ?
        '<td id="ctrConnStatus" class="socketOFF" ondblclick="changeMasterSocket()">DISCONNECTED</td>'.
        '<td class="txtFixW"><span id="ctrMastersNo" class="TargetAssigned"></span><span id="ctrMasters"></span></td>'
        : ''
    ).
    '<td><form method="POST" enctype="multipart/form-data"><input type="file" name="devices" class="loadParams w-80"><input class="iskButton" type="submit" class="w-15" value="'.get_text('CmdImport', 'Tournament').'"></form></td>'.
    '<td class="Center"><input class="iskButton" type="button" value="'.get_text('CmdExport', 'Tournament').'" onclick="exportDevices()"></td>'.
    '<td class="Center"><input class="iskButton" type="button" value="'.get_text('CmdDelete', 'Tournament').'" onclick="deleteDevices()"></td>'.
    '</tr>';
echo '</table>';


echo '<table class="Tabella">';
echo '<thead id="hGroups"><tr><th colspan="'.(15+($UsePersonalDevices?1:0)).'">' . get_text('Controller', 'Api') . '</th></tr><tr>'.
    '<th class="w-10" colspan="2">' . get_text('API-Group', 'Api') . '</th>'.
    (($_SESSION["UseApi"] === ISK_NG_LIVE_CODE) ? '<th class="w-5">' . get_text('Target') . '</th>' : '') .
    '<th class="w-10" colspan="'.((($_SESSION["UseApi"] === ISK_NG_LIVE_CODE) ? 2:3)+($UsePersonalDevices?1:0)).'">' . get_text('ISK-DeviceEnabled', 'Api') . '</th>'.
    '<th class="w-5">' . get_text('ISK-DeviceBattery', 'Api') . '</th>'.
    '<th class="w-auto Left" colspan="4">' . get_text('ISK-Sequence', 'Api') . '</th>'.
    '<th colspan="3">' . get_text('Distance', 'Tournament') . '</th>'.
    '<th class="Left" colspan="2"></th>'.
    '</tr></thead>';
echo '<tbody id="bGroups"></tbody>';

echo '<tr class="divider HideSelection"><td colspan="17"></td></tr>';
echo '<tr class="HideSelection">'.
    '<th colspan="6" rowspan="2" class="deviceGroup">' . get_text('SelectedDevicesAction', 'Api') . '</th>'.
    '<td colspan="9" class="Bold">'.get_text('SelectedMove2Group', 'Api').'<select class="mx-3" id="MoveToGroup">'.$tmpOptionsGrp.'</select><input class="iskButton" type="button" value="'.get_text('CmdGo', 'Tournament').'" onclick="MoveToGroup()"></td>'.
    '</tr>';
echo '<tr class="HideSelection">'.
    '<td colspan="9" class="Bold">'.get_text('SelectedReNumber', 'Api').'<input class="mx-3" id="RenumberFrom" size="3"><input class="iskButton" type="button" value="'.get_text('CmdGo', 'Tournament').'" onclick="AssignFrom()"></td>'.
    '</tr>';
echo '<tr class="divider"><td colspan="17"></td></tr>';

echo '<thead id="hDevices"><tr>'.
    '<th class="" onclick="selectDevices()"></th>'.
    '<th class="w-5">' . get_text('API-Group', 'Api') . '</th>'.
    '<th class="w-5">' . get_text('Target') . '</th>'.
    '<th class="w-5">' . get_text('Tournament', 'Tournament') . '</th>'.
    ($UsePersonalDevices ? '<th class="w-5">' . get_text('UsePersonalDevices-Granted', 'Api') . '</th>' : '').
    '<th class="w-5">' . get_text('ISK-DeviceEnabled', 'Api') . '</th>'.
    '<th class="w-5">' . get_text('ISK-DeviceBattery', 'Api') . '</th>'.
    (($_SESSION["UseApi"] == ISK_NG_LIVE_CODE or $_SESSION["UseApi"] == ISK_NG_PRO_CODE) ? '<th class="w-10">' . get_text('ISK-ReloadConfig', 'Api') . '</th>' : '') .
    ($_SESSION["UseApi"] == ISK_NG_LIVE_CODE ? '<th class="w-10">' . get_text('ISK-SendQrSetup', 'Api') . '</th>' : '') .
    '<th  class="w-5">' . get_text('ISK-DeviceUsed', 'Api') . '</th>'.
    '<th  class="w-15">' . get_text(($_SESSION["UseApi"] == ISK_NG_PRO_CODE ? 'ISK-DeviceLastSeen':'ISK-DeviceStatus'), 'Api') . '</th>'.
    '<th colspan="2" class="w-10">' . get_text('ISK-AppInfo', 'Api') . '</th>'.
    '<th colspan="2" class="w-15">' . get_text('ISK-DeviceId', 'Api') . '</th>'.
    '<th class="w-5">&nbsp;</th>'.
    '</tr></thead>';
echo '<tbody id="bDevices"></tbody>';
echo '</table>';

echo '<div id="PopUp" class="PopUp"><div class="PopUpContent">'.
        '<div class="PopUpHeader">'.get_text('ManageGroupTarget', 'Api').'</div>'.
        '<table class="Tabella">'.
            '<tr><td class="Right w-40">'.get_text('ISK-DeviceId', 'Api').'</td><td id="PopDevice" class="w-60"></td></tr>'.
            '<tr><td class="Right">'.get_text('API-Group', 'Api').'</td><td id="PopGroup"></td></tr>'.
            '<tr><td class="Right">'.get_text('Target').'</td><td id="PopTarget"></td></tr>'.
            '<tr><td class="Right Bold">'.get_text('NewGroup', 'Api').'</td><td><select id="NewGroup">'.$tmpOptionsGrp.'</select></td></tr>'.
            '<tr><td class="Right Bold">'.get_text('NewTarget', 'Api').'</td><td><input type="number" id="NewTarget" min="1" max="999"></td></tr>'.
        '</table>'.
        '<div class="PopUpFooter">'.
            '<input type="button" id="PopupImport" value="'.get_text('CmdOk').'" onclick="setGroupTarget()">'.
            '<input type="button" value="'.get_text('Close').'" onclick="closePopup()">'.
        '</div>'.
    '</div></div>';

include('Common/Templates/tail.php');
