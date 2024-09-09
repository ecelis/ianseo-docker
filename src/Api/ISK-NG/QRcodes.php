<?php
global $CFG;
require_once(dirname(__FILE__).'/config.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Lib/CommonLib.php');

CheckTourSession(true);
checkACL(AclISKServer, AclReadWrite);


$CanPrint=false;
$QrCode=[];
$Content='';

switch($_SESSION['UseApi']??'') {
	case ISK_NG_LITE_CODE:
	case ISK_NG_PRO_CODE:
		if(getModuleParameter('ISK-NG', 'ServerUrl')) {
			$Opts=array();
			$Opts['u']=getModuleParameter('ISK-NG', 'ServerUrl');
			$Opts['c']=$_SESSION['TourCode'];
			$tmpPin = getModuleParameter('ISK-NG', 'ServerUrlPin');
			if(!empty($tmpPin)) {
				$Opts['c'] .= '|'.$tmpPin;
			}

			setModuleParameter('ISK-NG', 'QRCode-Setup', $Opts);

			CD_redirect('./QRcodesPDF.php');
		}
		CD_redirect($CFG->ROOT_DIR);
		break;
	case ISK_NG_LIVE_CODE:
		if(!module_exists('ISK-NG_Live')) {
			cd_redirect($CFG->ROOT_DIR);
		}
		$QrCode=[
            'action'=>'',
			'WifiSSID'=>[],
			'WifiPWD'=>[],
			'WifiTgtF'=>[],
			'WifiTgtT'=>[],
			'WifiReconnectTO'=>10,
			'WifiSearch'=>60,
			'WifiResetCounter'=>5,
			'WifiDELETE'=>0,
			'enableGPS'=>0,
			'gpsFrequency'=>60,
			'enableWIFIManagement'=>0,
			'hideTotals'=>0,
			'spottingMode'=>0,
			'kioskMode'=>0,
			'askTotals'=>0,
            'enableHaptics'=>0,
			'askSignature'=>0,
            'settingsPinCode'=>'',
		];
		if($Items=getModuleParameter('ISK-NG', 'QRCode-Setup', [])) {
			$CanPrint=true;
		}

		foreach($QrCode as $k => &$v) {
			if(isset($Items[$k])) {
				$v=$Items[$k];
				unset($Items[$k]);
			}
		}
        unset($v);
		if($Items) {
			$CanPrint=false;
		}

		$Content = '<tr><th class="Title" colspan="5">'.get_text('ISK-WiFi','Api').'</th></tr>';
		$Content.= '<tr>
			<th colspan="2">' . get_text('ISK-enableWIFIManagement','Api') . '</th>
			<td colspan="3"><input type="checkbox" id="enableWIFIManagement" onClick="showWifiPart()" name="enableWIFIManagement" value="1" '. ($QrCode['enableWIFIManagement'] ? 'checked="checked"' : '') .'></td>
			</tr>';

		$Content.= '<tbody class="d-none" id="WifiManagement" numWifi="'.count($QrCode['WifiSSID']).'">';
		$Content.= '<tr>
			<th colspan="2">' . get_text('ISK-WifiReconnectTO','Api') . '</th>
			<td colspan="3"><input type="text" name="WifiReconnectTO" onchange="manageButtons()" value="'. $QrCode['WifiReconnectTO'] .'"></td>
			</tr>';
		$Content.= '<tr>
			<th colspan="2">' . get_text('ISK-WifiSearch','Api') . '</th>
			<td colspan="3"><input type="text" name="WifiSearch" onchange="manageButtons()" value="'. $QrCode['WifiSearch'] .'"></td>
			</tr>';
		$Content.= '<tr>
			<th colspan="2">' . get_text('ISK-WifiResetCounter','Api') . '</th>
			<td colspan="3"><input type="text" name="WifiResetCounter" onchange="manageButtons()" value="'. $QrCode['WifiResetCounter'] .'"></td>
			</tr>';
		$Content.= '<tr>
			<th colspan="2">' . get_text('ISK-WifiDELETE','Api') . '</th>
			<td colspan="3"><input type="checkbox" name="WifiDELETE" onclick="manageButtons()" value="1" '. ($QrCode['WifiDELETE'] ? 'checked="checked"' : '') .'"></td>
			</tr>';
		// header of wifi management
		$Content.= '<tr>
			<th class="Title"><i class="fa fa-lg fa-plus-square mr-3" onclick="addWifiRow()"></i></th>
			<th class="Title">' . get_text('ISK-WifiSSID','Api') . '</th>
			<th class="Title">' . get_text('ISK-WifiPWD','Api') . '</th>
			<th class="Title" colspan="2">' . get_text('ISK-WifiTargetRange','Api') . '</th>
			</tr>';
		foreach($QrCode['WifiSSID'] as $k=>$v) {
			$Content.= '<tr id="wifiRow_'.$k.'">
				<th><i class="far fa-lg fa-trash-can mr-3" onclick="deleteWifiRow('.$k.')"></i><span>'.($k+1).'</span></th>
				<td><input type="text" class="w-100" name="WifiSSID['.$k.']" onchange="manageButtons()" value="'. $v .'"></td>
				<td><input type="text" class="w-100" name="WifiPWD['.$k.']" onchange="manageButtons()" value="'. base64_decode($QrCode['WifiPWD'][$k]??'').'"></td>
				<td><input type="number" class="w-100" name="WifiTgtF['.$k.']" onchange="manageButtons()" value="'. ($QrCode['WifiTgtF'][$k]??'') .'"></td>
				<td><input type="number" class="w-100" name="WifiTgtT['.$k.']" onchange="manageButtons()" value="'. ($QrCode['WifiTgtT'][$k]??'') .'"></td>
				</tr>';
		}
		$Content.= '<tr class="divider"></tr>';
		$Content.= '</tbody>';


		$Content.= '<tr><th class="Title" colspan="5">' . get_text('ISK-Gps','Api') . '</th></tr>';
		$Content.= '<tr>
			<th colspan="2">' . get_text('ISK-enableGPS','Api') . '</th>
			<td colspan="3"><input type="checkbox" id="enableGPS" onClick="showGPSPart();" name="enableGPS" value="1" '. ($QrCode['enableGPS'] ? 'checked="checked"' : '') .'"></td>
			</tr>';
		$Content.= '<tr class="d-none" id="GpsFrequency">
			<th colspan="2">' . get_text('ISK-gpsFrequency','Api') . '</th>
			<td colspan="3"><input type="text" name="gpsFrequency" onchange="manageButtons()" value="'. $QrCode['gpsFrequency'] .'"></td>
			</tr>';

		$Content.= '<tr><th class="Title" colspan="5">' . get_text('ISK-Options','Api') . '</th></tr>';
		$Content.= '<tr>
			<th colspan="2">' . get_text('ISK-spottingMode','Api') . '</th>
			<td colspan="3"><input type="checkbox" name="spottingMode" onclick="manageButtons()" '. ($QrCode['spottingMode'] ? 'checked="checked"' : '') .'"></td>
			</tr>';
		$Content.= '<tr>
			<th colspan="2">' . get_text('ISK-hideTotals','Api') . '</th>
			<td colspan="3"><input type="checkbox" name="hideTotals" onclick="manageButtons()" '. ($QrCode['hideTotals'] ? 'checked="checked"' : '') .'"></td>
			</tr>';
		$Content.= '<tr>
			<th colspan="2">' . get_text('ISK-askTotals','Api') . '</th>
			<td colspan="3"><input type="checkbox" name="askTotals" onclick="manageButtons()" '. ($QrCode['askTotals'] ? 'checked="checked"' : '') .'"></td>
			</tr>';
        $Content.= '<tr>
			<th colspan="2">' . get_text('ISK-enableHaptics','Api') . '</th>
			<td colspan="3"><input type="checkbox" name="enableHaptics" onclick="manageButtons()" '. ($QrCode['enableHaptics'] ? 'checked="checked"' : '') .'"></td>
			</tr>';
		$Content.= '<tr>
			<th colspan="2">' . get_text('ISK-askSignature','Api') . '</th>
			<td colspan="3"><select name="askSignature" onchange="manageButtons()">
			<option value="0">'.get_text('AskSignature-0','Api').'</option>
			<option value="1" '. ($QrCode['askSignature']==1 ? 'selected="selected"' : '') .'>'.get_text('AskSignature-1','Api').'</option>
			<option value="2" '. ($QrCode['askSignature']==2 ? 'selected="selected"' : '') .'>'.get_text('AskSignature-2','Api').'</option>
			</select></td>
			</tr>';
        $Content.= '<tr>
			<th colspan="2">' . get_text('ISK-SettingsPIN','Api') . '</th>
			<td colspan="3"><input type="text" maxlength="4" pattern="[0-9]{4}" name="settingsPinCode" onchange="manageButtons()" value="'. $QrCode['settingsPinCode'] .'"></td>
			</tr>';
		break;
}

$PAGE_TITLE=get_text('MenuLM_QrCodes');
// $ONLOAD =' onload="showWifiPart();showGPSPart();"';
$IncludeFA=true;
$IncludeJquery = true;
$JS_SCRIPT=array(
		phpVars2js(array(
			'WifiSSID' => get_text('ISK-WifiSSID','Api'),
			'WifiPWD' => get_text('ISK-WifiPWD','Api'),
            'WifiTargetRange' => get_text('ISK-WifiTargetRange','Api'),
		)),
		'<script type="text/javascript" src="./QRcodes.js"></script>',
		'<link href="isk.css" rel="stylesheet" type="text/css">',
);

include('Common/Templates/head.php');

echo '<table class="Tabella" style="width:auto;margin:auto;" id="QrSettings">';
echo '<tr><th class="Title" colspan="5">' . get_text('ISK-Configuration') . '</th></tr>';
echo $Content;
echo '<tr>
	<th colspan="5">
		<input type="button" class="mx-3" id="reset" onclick="reset()" value="'.get_text('CmdCancel').'">
		<input type="button" class="mx-3'.($CanPrint ? '' : ' MustSave').'" id="save" onclick="save()" value="'.get_text('CmdSave').'">
		<input type="button" class="mx-3" '.($CanPrint ? '' : ' disabled="disabled"').' id="print" onclick="print()" value="'.get_text('ISK-AppQrCode','Api').'">
		</th>
	</tr>';
echo '</table>';

include('Common/Templates/tail.php');
