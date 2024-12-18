<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_Modules.php');

$ISKMode=getModuleParameter('ISK', 'Mode', '');

$JSON=array('html' => '');

switch($_REQUEST['api']) {
    case 'ng-lite':
    case 'ng-pro':
    case 'ng-live':
        $JSON['html'].=getConfigString($CFG->DOCUMENT_PATH.'Api/ISK-NG/ApiConfig.php');
        break;
}

if($JSON['html']) {
	$JSON['html']='<table class="TextInput">'.$JSON['html'].'</table>';
}

JsonOut($JSON);


function getConfigString($file) {
	global $CFG;
	$ConfigHtml='';

	include($file);

	return $ConfigHtml;
}
