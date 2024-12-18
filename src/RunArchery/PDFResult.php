<?php
require_once(dirname(__DIR__) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');

if($isOris=($_REQUEST['oris']??0)) {
	require_once('Common/pdf/OrisPDF.inc.php');
} else {
	require_once('Common/pdf/ResultPDF.inc.php');
}

if (!isset($_SESSION['TourId']) && isset($_REQUEST['TourId'])) {
	CreateTourSession($_REQUEST['TourId']);
}

checkACL(AclQualification, AclReadOnly);

$Events=[];
foreach(($_REQUEST['events']??[]) as $v) {
	if($v=='.') {
		// all events, so break here
		$Events=[];
		break;
	}
	$Events[]=$v;
}

if($isTeam=($_REQUEST['team']??0)) {
	$PdfData=getRankingRunTeams($Events, !empty($_REQUEST['oris']), !empty($_REQUEST['detailed']));
} else {
	$PdfData=getRankingRunIndividual($Events, !empty($_REQUEST['oris']), !empty($_REQUEST['detailed']));
}

if(!isset($isCompleteResultBook)) {
	if($isOris) {
		$pdf = new OrisPDF('', $PdfData->Description);
	} else {
		$pdf = new ResultPDF($PdfData->Description);
	}
}

$Chunk=($isTeam ? 'RankTeam.inc.php' : 'RankIndividual.inc.php');

if($isOris) {
	// $Chunk='Oris'.$Chunk;
}
require_once(PdfChunkLoader($Chunk));

if (isset($_REQUEST['TourId'])) {
	EraseTourSession();
}

if(isset($__ExportPDF)) {
	$__ExportPDF = $pdf->Output('','S');
} elseif(!isset($isCompleteResultBook)) {
	if(isset($_REQUEST['ToFitarco'])) {
		$Dest='D';
		if (isset($_REQUEST['Dest'])) {
			$Dest=$_REQUEST['Dest'];
		}

		if ($Dest=='S') {
			print $pdf->Output($_REQUEST['ToFitarco'],$Dest);
		} else {
			$pdf->Output($_REQUEST['ToFitarco'],$Dest);
		}
	} else {
		$pdf->Output();
	}
}
