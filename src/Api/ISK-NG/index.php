<?php
$SKIP_AUTH=true;
require_once(__DIR__ . '/config.php');
const MAX_API_VERSION = 1;

$Json=array('error'=>true);

$data = json_decode(file_get_contents("php://input"));
if(isset($data->requests)) {
    $data = $data->requests;
} else if(is_null($data)) {
    if(!(isset($_REQUEST['requests']) AND !is_null($data = json_decode($_REQUEST['requests'])))) {
        $Json['errorMsg'] = 'Missing REQUESTS payload';
        JsonOut($Json);
    }
} else {
    $Json['errorMsg'] = 'Missing PAYLOAD and REQUESTS';
    JsonOut($Json);
}

// Run every Request separately
$Json['error'] = false;
$Json['responses'] = [];

// This is in case we have several responses to manage as the result of a single call!
$MultipleRes=[];

if(NG_DEBUG_LOG) {
    error_log(json_encode(['time'=>date('Y-m-d H:i:s'), 'mode'=>'GET', 'data' => $data])."\n", 3, NG_DEBUG_LOGFILE);
    error_log(json_encode(['time'=>date('Y-m-d H:i:s'), 'mode'=>'HEADERS', 'data' => apache_request_headers()])."\n", 3, NG_DEBUG_LOGFILE);
    chmod(NG_DEBUG_LOGFILE, 0666);
}

try {
	foreach (is_array($data) ? $data : array($data) as $req) {
		if(($req->tournament??'') and strpos($req->tournament,'|')!==false) {
			$tmp=explode('|', $req->tournament, 2);
			$req->tournament=$tmp[0];
			// we need to check the PIN is correct!
			$ToId=getIdFromCode($req->tournament);
			if($tmp[1] != getModuleParameter('ISK-NG', 'ServerUrlPin', 'Invalid PIN', $ToId)) {
				$Json['error'] = true;
				$Json['errorMsg'] = 'Validation failed';
				continue;
			}
		}
		if(($req->tocode??'') and strpos($req->tocode,'|')!==false) {
			$tmp=explode('|', $req->tocode, 2);
			$req->tocode=$tmp[0];
			// we need to check the PIN is correct!
			$ToId=getIdFromCode($req->tocode);
			if($tmp[1] != getModuleParameter('ISK-NG', 'ServerUrlPin', 'Invalid PIN', $ToId)) {
				$Json['error'] = true;
				$Json['errorMsg'] = 'Validation failed';
				continue;
			}
		}

		// Verify the basic input parameters
		if (!((isset($req->device) OR isset($req->uuid)) AND isset($req->action) AND isset($req->apiVersion))) {
			$Json['error'] = true;
			$Json['errorMsg'] = 'Incorrect transaction format: '.$req->action;
			continue;
		}
		// Initialize empty response
		$res = null;

		if($req->apiVersion <= MAX_API_VERSION) {
			$transactionScriptFile = sprintf('./v%s/%s.php',$req->apiVersion, ($req->action ?? 'index'));
			if (file_exists($transactionScriptFile)) {
				// always update the timestamp
				include($transactionScriptFile);
				if(($dev=$req->device??'') OR ($dev=$req->uuid??'')) {
					safe_w_sql("update IskDevices set IskDvLastSeen='" . date('Y-m-d H:i:s') . "' where IskDvDevice=" . StrSafe_DB($dev));
				}
			} else {
				$Json['error'] = true;
				$Json['errorMsg'] = 'Incorrect action or API version: '.$req->action.', '.$req->apiVersion;
			}
		} else {
			$Json['errorMsg'] = 'Incorrect API version';
		}

		$Json['error'] = ($Json['error'] OR (!isset($res['error']) ? false : $res['error']==1));
		if ($res) {
			$Json['responses'][] = $res;
		} elseif($Json['error']) {
			$Json['responses'][] =array(
				'action'=>'reset',
				'device'=>($req->device ?? $req->uuid ?? ''),
				'resetMessage' => $Json['errorMsg'], 'resetSubMsg' => '', 'resetTarget' => '');
		}
    }
    foreach($MultipleRes as $altRes) {
        $Json['responses'][] = $altRes;
    }
} catch(Throwable $exception) {
	if(NG_DEBUG_LOG) {
        error_log(json_encode(['time'=>date('Y-m-d H:i:s'), 'mode'=>'ERROR', 'data' => $exception->getMessage()])."\n", 3, NG_DEBUG_LOGFILE);
	}

	$Json['error'] = true;
	$Json['errorMsg'] = 'Throwable error!';
} catch(Error $exception) {
	if(NG_DEBUG_LOG) {
        error_log(json_encode(['time'=>date('Y-m-d H:i:s'), 'mode'=>'ERROR', 'data' => $exception->getMessage()])."\n", 3, NG_DEBUG_LOGFILE);
	}

	$Json['error'] = true;
	$Json['errorMsg'] = 'Fatal error!';
}

if(NG_DEBUG_LOG) {
    error_log(json_encode(['time'=>date('Y-m-d H:i:s'), 'mode'=>'SEND', 'data' => $Json])."\n", 3, NG_DEBUG_LOGFILE);
}

safe_close();
JsonOut($Json);

