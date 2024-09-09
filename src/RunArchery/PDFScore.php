<?php
require_once(dirname(__DIR__) . '/config.php');
require_once('Common/pdf/ScorePDF.inc.php');
// require_once('Common/Fun_FormatText.inc.php');
// require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Lib/ScorecardsLib.php');

CheckTourSession(true);
checkACL(AclQualification, AclReadOnly);

$PdfType=$_REQUEST['act']??'';

// get how many people per target in session 1
$q=safe_r_sql("select SesAth4Target, max(EvE1Arrows) as Hits 
	from Session
	inner join Events on EvTournament=SesTournament
	where SesType='Q' and SesOrder=1 and SesTournament={$_SESSION['TourId']}
	group by SesTournament");
$r=safe_fetch($q);


$pdf = new ScorePDF();

if($PdfType!='printLoop' and $PdfType!='printDelays') {
	$Data=[
		'top'=>10,
		'height'=>($pdf->getPageHeight()/2)-20,
		'rows'=>(int)$r->SesAth4Target,
		'targets'=>(int)$r->Hits,
	];
	$pdf->AddPage();

	$pdf->DrawScoreRunArcherySpotter($Data);
	$Data['top']+=$pdf->getPageHeight()/2;
	$pdf->DrawScoreRunArcherySpotter($Data);
}

if($PdfType!='printLoop' and $PdfType!='printSpotter') {
	$Data=[
		'top'=>10,
		'height'=>($pdf->getPageHeight()/2)-20,
		'rows'=>(int)$r->SesAth4Target,
	];
	$pdf->AddPage();

	$pdf->DrawScoreRunArcheryDelays($Data);
	$Data['top']+=$pdf->getPageHeight()/2;
	$pdf->DrawScoreRunArcheryDelays($Data);
}

if($PdfType!='printSpotter' and $PdfType!='printDelays') {
	$Data = [
		'top' => 10,
		'height' => $pdf->getPageHeight() - 20,
		'rows' => 25,
	];
	$pdf->AddPage();

	$pdf->DrawScoreRunArcheryLoop($Data);
}
$pdf->output();
