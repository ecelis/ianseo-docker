<?php
require_once(dirname(dirname(__DIR__)) . '/config.php');
CheckTourSession(true);
// require_once('Common/Fun_FormatText.inc.php');
checkACL(array(AclRobin), AclReadOnly);

require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/pdf/PdfChunkLoader.php');

$Team=($_REQUEST['team']??0);
$HasRank=($_REQUEST['IncRankings']??0);
$HasMatches=($_REQUEST['IncBrackets']??0);

$options=[
	'team'=>($_REQUEST['team']??0),
	'includeTeamRank' => $_SESSION['TourLocSubRule']=='SetFRD12023',
	];
if(!empty($_REQUEST['Event'])) {
	$options['events']=$_REQUEST['Event'];
}
$pdf = new ResultPDF(get_text('ResultsRobin','Tournament'));
$PdfData = getRobin($options);
$rankData = $PdfData->rankData;

$pdf->ShowTargetNo=($_REQUEST['ShowTargetNo']??0);
$pdf->ShowSchedule=($_REQUEST['ShowSchedule']??0);
$pdf->ShowSetArrows=($_REQUEST['ShowSetArrows']??0);

$pdf->ShowMatches=(empty($_REQUEST['IncRankings']) or !empty($_REQUEST['IncBrackets']));
$pdf->ShowRank=(empty($_REQUEST['IncBrackets']) or !empty($_REQUEST['IncRankings']));

if(!empty($_REQUEST['ShowOris'])) {
	// ORIS
	require_once(PdfChunkLoader('RobinResults.inc.php'));
} else {
	// Standard
	require_once(PdfChunkLoader('RobinResults.inc.php'));
}

$pdf->Output();
