<?php
require_once(dirname(dirname(__DIR__)) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/pdf/PdfChunkLoader.php');

checkACL(array(AclQualification,AclRobin), AclReadOnly);

$Event=($_REQUEST['Events']??'');
$Team=($_REQUEST['Team']??0);
$Level=($_REQUEST['Level']??0);

if(!$Event) {
	require_once('Common/Templates/head.php');
	echo get_text('ErrGenericError','Errors');
	require_once('Common/Templates/tail.php');
	die();
}

if (!isset($_SESSION['TourId']) AND isset($_REQUEST['TourId'])) {
	CreateTourSession($_REQUEST['TourId']);
}

if($Level==0) {
	// Qualification Shootoff
	$_REQUEST['Events']=$Event."|".$Team;
	require_once('Qualification/PrnShootoff.php');
	// ret+='<a href="' +ROOT_DIR+ '?Events=' +EvCode+ '|'+$('#TeamSelector').val()+'" target="PrintOut"><img src="' + ROOT_DIR + 'Common/Images/pdf_small.gif" alt="' +EvCode+ '"></a>';
	die();
}

$options=[
	'events' => $Event,
	'levels'=>$Level,
	'team'=>$Team,
	];
$isCompleteResultBook = true;
$pdf = new ResultPDF(get_text('ResultsRobin','Tournament'));
$PdfData = getRobin($options);
$rankData = $PdfData->rankData;
require_once(PdfChunkLoader('RobinShootoff.inc.php'));

if (isset($_REQUEST['TourId'])) {
	EraseTourSession();
}

$pdf->Output();
