<?php

$JSON=array('error'=>1, 'msg'=> 'error');
define('DIRNAME', dirname($_SERVER['SCRIPT_FILENAME']));
require_once(dirname(DIRNAME) . '/config.php');
checkACL(AclRoot, AclReadWrite);

if(!empty($_SESSION['AUTH_ENABLE']) AND empty($_SESSION['AUTH_ROOT'])) {
    JsonOut($JSON);
}

// THIS IS MANDATORY TO AVOID HAVING PHP WAITING UNTIL ANOTHER PAGE FROM THE SAME SESSION IS PROCESSED!!!
session_write_close();


if(empty($_REQUEST['act'])) {
    JsonOut($JSON);
}

$file=dirname(DIRNAME).'/TV/Photos/updating.json';

switch($_REQUEST['act']) {
    case 'getFile':
        // check if a previous operation has finished...
        if(file_exists($file) and empty($_REQUEST['force'])) {
            if($f=file_get_contents($file) and $data=@json_decode($f)) {
                // we have a situation file...
                if(!$data->finished) {
                    // do nothing and halts
                    $JSON['msg']=get_text('UpdateInProgress', 'Install').' (since '.$data->start.')';
                    JsonOut($JSON);
                }
            }
        }

        // check the directory is writeable
        if(!is_writable(dirname($file))) {
            $JSON['msg']=get_text('DirectoryNotWriteable', 'Errors', dirname($file));
            JsonOut($JSON);
        }

        $JSON['error']=0;
        $JSON['start']=date('Y-m-d m:i:s');
        $JSON['status']='';
        $JSON['finished']=0;
	    writeStatusFile($file, $JSON);
        JsonOut($JSON);
        break;
    case 'getInfo':
        $JSON['msg']='An error has occurred!';
        if(file_exists($file)) {
            $JSON['msg']='File exists but not readable!';
            if($f=file_get_contents($file) and $data=@json_decode($f,true)) {
                // we have a situation file...
                JsonOut($data);
            }
        }
        JsonOut($JSON);
        break;
    case 'doUpdate':
        $IN_PHP=true;
        require_once('./UpdateIanseo.php');
        break;
    case 'updateInnoDb':
        $JSON['msg']=UpdateToInnoDb(true);
        $JSON['error']=($JSON['msg']?1:0);
        break;
}

JsonOut($JSON);

function writeStatusFile($file, $data) {
	file_put_contents($file.'.tmp', json_encode($data));
	rename($file.'.tmp', $file);
}