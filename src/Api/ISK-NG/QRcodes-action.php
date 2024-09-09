<?php
require_once(dirname(__FILE__).'/config.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Lib/CommonLib.php');

$JSON=['error'=>1, 'msg'=>get_text('ErrGenericError', 'Errors')];

if(!CheckTourSession() or !hasACL(AclISKServer, AclReadWrite)) {
	JsonOut($JSON);
}

$Setup=[];
switch($_REQUEST['action']??'') {
	case 'update':
		switch($_SESSION['UseApi']??'') {
			case ISK_NG_LITE_CODE:
			case ISK_NG_PRO_CODE:
				break;
			case ISK_NG_LIVE_CODE:
				$Setup['action']='setup';
				$Setup['enableWIFIManagement']=intval($_REQUEST['enableWIFIManagement']??0);
				if($Setup['enableWIFIManagement']) {
					$Setup['WifiReconnectTO']=intval($_REQUEST['WifiReconnectTO']??10);
					$Setup['WifiSearch']=intval($_REQUEST['WifiSearch']??60);
					$Setup['WifiResetCounter']=intval($_REQUEST['WifiResetCounter']??5);
					$Setup['WifiDELETE']=intval($_REQUEST['WifiDELETE']??0);
					$Setup['WifiSSID']=[];
					$Setup['WifiPWD']=[];
					$Setup['WifiTgtF']=[];
					$Setup['WifiTgtT']=[];
					foreach($_REQUEST['WifiSSID']??[] as $k=>$v) {
						if($v=trim($v) and $pwd=trim($_REQUEST['WifiPWD'][$k]??'')) {
							$Setup['WifiSSID'][]=$v;
							$Setup['WifiPWD'][]=base64_encode($pwd);
							$Setup['WifiTgtF'][]=intval($_REQUEST['WifiTgtF'][$k]??0);
							$Setup['WifiTgtT'][]=intval($_REQUEST['WifiTgtT'][$k]??0);
						}
					}
				}

				$Setup['enableGPS']=intval($_REQUEST['enableGPS']??0);
				if($Setup['enableGPS']) {
					$Setup['gpsFrequency']=intval($_REQUEST['gpsFrequency']??60);
				}
                $Setup['spottingMode']=intval($_REQUEST['spottingMode']??0);
				$Setup['hideTotals']=intval($_REQUEST['hideTotals']??0);
				$Setup['askTotals']=intval($_REQUEST['askTotals']??0);
                $Setup['enableHaptics']=intval($_REQUEST['enableHaptics']??0);
				$Setup['askSignature']=intval($_REQUEST['askSignature']??0);
                if(preg_match("/^[0-9]{4}$/",$_REQUEST['settingsPinCode']??'')) {
                    $Setup['settingsPinCode']=$_REQUEST['settingsPinCode'];
                } else {
                    $Setup['settingsPinCode']='';
                }
				break;
			default:
				JsonOut($JSON);
		}
		setModuleParameter('ISK-NG', 'QRCode-Setup', $Setup);
		$JSON['error']=0;
		break;
	default:
		JsonOut($JSON);
}
$JSON['error']=0;
JsonOut($JSON);
