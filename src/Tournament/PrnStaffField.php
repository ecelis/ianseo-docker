<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
checkACL(AclCompetition, AclReadOnly);
define("HideCols", GetParameter("IntEvent"));

$CatJudge=isset($_REQUEST['judge']);
$CatDos=isset($_REQUEST['dos']);
$CatJury=isset($_REQUEST['jury']);
$CatOC=isset($_REQUEST['oc']);

if(!isset($isCompleteResultBook))
	$pdf = new ResultPDF((get_text('StaffOnField','Tournament')),true,'',false);


$Ses=StrSafe_DB($_SESSION['TourId']);

// $Filter=array();
//
// if ($CatJudge)
// 	$Filter[]=" ItJudge<>0 ";
//
// if ($CatDos)
// 	$Filter[]=" ItDoS<>0 ";
//
// if ($CatJury)
// 	$Filter[]=" ItJury<>0 ";
//
// if ($CatOC)
// 	$Filter[]=" ItOC<>0 ";
//
// if (count($Filter)>0)
// 	$Filter="AND (" . implode(" OR ",$Filter) . ") ";
// else
	$Filter="";

$Select="
	SELECT ti.*, it.*, CoCode, ucase(TiName) as TiUpperName
	FROM TournamentInvolved AS ti 
    LEFT JOIN Countries on TiCountry=CoId and TiTournament=CoTournament
    LEFT JOIN InvolvedType AS it ON ti.TiType=it.ItId
	WHERE ti.TiTournament={$Ses} AND it.ItId IS NOT NULL {$Filter}
	ORDER BY ItJudge=0, ItJudge, ItDoS=0, ItDos, ItJury=0, ItJury, ItOC=0, ItOC, ti.TiName ASC
";
//print $Select;Exit;
$Rs=safe_r_sql($Select);

$OldCategory='';
while ($MyRow=safe_fetch($Rs)) {
	if ($OldCategory!=$MyRow->ItDescription) {
		$pdf->Ln(10);
		$Function=get_text($MyRow->ItDescription,'Tournament').':';
	}
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(45, 6, $Function);
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(0, 6,  $MyRow->TiUpperName . ' ' . $MyRow->TiGivenName . ' (' .  $MyRow->CoCode . ')', 0, 1);
	$OldCategory=$MyRow->ItDescription;
	$Function='';
}

if(!isset($isCompleteResultBook))
{
	if(isset($_REQUEST['ToFitarco']))
	{
		$Dest='D';
		if (isset($_REQUEST['Dest']))
			$Dest=$_REQUEST['Dest'];
		$pdf->Output($_REQUEST['ToFitarco'],$Dest);
	}
	else
		$pdf->Output();
}
