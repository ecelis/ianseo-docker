<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/pdf/OrisPDF.inc.php');
require_once('Common/pdf/OrisBracketPDF.inc.php');
checkACL(AclCompetition, AclReadOnly);

$isCompleteResultBook = true;

if(isset($_REQUEST["IncBrackets"]) AND $_REQUEST["IncBrackets"]==1 AND ($_REQUEST['OrisCE']??'')=='E') {
	// scorecard!
	require_once('Common/OrisFunctions.php');
	require_once('Common/pdf/PdfChunkLoader.php');
	$PdfData = getBracketsTeams($_REQUEST['Event']??'', true, false, false, true, false, null, true);

	//$pdf->setOrisCode('', '', true);
	$pdf = new OrisPDF('C73E', $PdfData->Description);
	$pdf->SetAutoPageBreak(true,(OrisPDF::bottomMargin+$pdf->extraBottomMargin));

	include(PdfChunkLoader('OrisScoreTeam.inc.php'));
} else {
	$pdf = new OrisBracketPDF('C75C', 'Result Brackets');

	if(isset($_REQUEST["IncBrackets"]) && $_REQUEST["IncBrackets"]==1) {
		include 'OrisBracket.php';
	}

	$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin);

	if(isset($_REQUEST["IncRankings"]) && $_REQUEST["IncRankings"]==1) {
		include 'OrisRanking.php';
	}
}



if(isset($_REQUEST['ToFitarco']))
{
	$Dest='D';
	if (isset($_REQUEST['Dest']))
		$Dest=$_REQUEST['Dest'];
	$pdf->Output($_REQUEST['ToFitarco'],$Dest);
}
else
	$pdf->Output();
?>
