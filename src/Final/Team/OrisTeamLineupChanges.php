<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/pdf/OrisPDF.inc.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');
checkACL(AclCompetition, AclReadOnly);

$Ev = (!empty($_REQUEST["EvCode"]) ? $_REQUEST["EvCode"] : '');
$Team = (!empty($_REQUEST["CoId"]) ? $_REQUEST["CoId"] : '');

$PdfData=getTeamsComponentsLog($Ev, $Team);

if(!isset($isCompleteResultBook)) {
	$pdf = new OrisPDF($PdfData->Code, $PdfData->Description);
} else {
	$pdf->setOrisCode('', $PdfData->Description);
}

require_once(PdfChunkLoader('OrisComponentLogTeam.inc.php'));

if(!isset($isCompleteResultBook)) {
	if(isset($_REQUEST['ToFitarco'])) {
		$Dest='D';
		if (isset($_REQUEST['Dest']))
			$Dest=$_REQUEST['Dest'];
		$pdf->Output($_REQUEST['ToFitarco'],$Dest);
	} else {
		$pdf->Output();
	}
}