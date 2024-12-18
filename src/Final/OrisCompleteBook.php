<?php

/*
 * Since 2020 ORIS book hase the following sections
 *
 * - C24 Records
 * - C30 Number of Entries by NOC
 * - C32E Entries by NOC
 * - C35A Competition Officials (TODO:)
 * - C65B/C/D Scorecards
 * - C67 Official Communication (TODO:)
 * - C68 Sport Communication (TODO:)
 * - C73A Results RR
 * - C73B Results RR Team
 * - C73C Results RR Mixed Teams
 * - C75A/B Brackets Individual
 * - C75C Brackets (Team)
 * - C76A Final Rank Individual
 * - C76B Final Ranking Team
 * - C92A/B-C93 Medallists
 * - C95 Medal Standing
 *
 * */

require_once(dirname(dirname(__FILE__)) . '/config.php');
include_once('Common/pdf/OrisPDF.inc.php');
include_once('Common/pdf/OrisBracketPDF.inc.php');
include_once('Common/Fun_FormatText.inc.php');
include_once('Common/pdf/PdfChunkLoader.php');
$cbIndFinal = false;
$cbIndElim = false;
$cbTeamFinal = false;

$Rs=safe_r_sql('SELECT EvCode FROM Events WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=0 AND EvShootOff=1');
$cbIndFinal = (safe_num_rows($Rs)>0);
$Rs=safe_r_sql('SELECT EvCode FROM Events WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=0 AND (EvE1ShootOff=1 OR EvE2ShootOff=1)');
$cbIndElim = (safe_num_rows($Rs)>0);
$Rs=safe_r_sql('SELECT EvCode FROM Events WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=1 AND EvShootOff=1');
$cbTeamFinal = (safe_num_rows($Rs)>0);

checkACL(array(AclIndividuals, AclTeams), AclReadOnly);
$isCompleteResultBook = true;

$pdf = new OrisBracketPDF('', 'Complete Result Book');
$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin+$pdf->extraBottomMargin);

$LastUpdate='';

/********
 * ORIS ORDER of documents
 *
Archery cover page (provided by the OC)
Version History
Competition Format and Rules (N02A)
Medallists by Event (C93)
Medal Standings (C95)
Number of Entries by NOC (C30)
Entries by NOC (C32E) (men, followed by women, followed by Mixed Team)
For each event (in WA presentation order according to the "References" section):
* Individual
  - Medallists (Individual) (C92A)
  - Final Ranking (Individual) (C76A)
  - Brackets - Final Rounds (Individual) (C75B)
  - Brackets - Elimination Rounds (Individual) (C75A)
  - Results (Individual - Ranking Round) (C73A)
  - Match Results (Individual) (C73D) - for each match (ordered by match number)
  - Individual Statistics (C85A)
* Team
  - Medallists (Teams) (C92B)
  - Final Ranking (Team) (C76B)
  - Brackets (Team) (C75C)
  - Results (Team - Ranking Round) (C73B)
  - Match Results (Team) (C73E) - for each match (ordered by match number)
  - Team Statistics (C85B)
* Mixed Team
  - Medallists (Mixed Team) (C92B)
  - Final Ranking (Mixed Team) (C76B)
  - Brackets (Mixed Team) (C75C)
  - Results (Mixed Team - Ranking Round) (C73C)
  - Match Results (Team) (C73E) - for each match (ordered by match number)
  - Mixed Team Statistics (C85C)
Records (C24) (updated with the records achieved during the Games)
Records Broken (C81)
Competition Officials (C35A)
Official Communication (C67) - in chronological order
Sport Communication (C68) - selected by World Archery

 * */


//Medaglieri
if($cbIndFinal || $cbTeamFinal) {
	include 'OrisMedalList.php';
    $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
	$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin+$pdf->extraBottomMargin);
	include 'OrisMedalStanding.php';
    $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
    $pdf->endPage();
}

include 'Partecipants/OrisCountry.php';
$LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
$pdf->endPage();

if($cbIndFinal || $cbIndElim) {
    include 'Final/Individual/OrisRanking.php';
    $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
    $pdf->endPage();
}

if($cbIndFinal) {
	include 'Final/Individual/OrisBracket.php';
    $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
	$BracketsInd = clone $PdfData;
	$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin+$pdf->extraBottomMargin);
    $pdf->endPage();
}

if($cbIndElim) {
    include 'Elimination/OrisIndividual.php';
    $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
    $pdf->endPage();
}

if($cbIndFinal) {
    include 'Qualification/OrisIndividual.php';
    $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
    $pdf->endPage();
}

if($cbTeamFinal) {
	include 'Final/Team/OrisRanking.php';
    $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
	include 'Final/Team/OrisBracket.php';
    $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
	$BracketsTeam = clone $PdfData;
	$pdf->SetAutoPageBreak(true,OrisPDF::bottomMargin+$pdf->extraBottomMargin);
	include 'Qualification/OrisTeam.php';
    $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
	$pdf->endPage();
}

if($cbIndFinal) {
	if(empty($BracketsInd)) {
		$PdfData = getBracketsIndividual('',
			true,
			isset($_REQUEST["ShowTargetNo"]),
			isset($_REQUEST["ShowSchedule"]),
			true,
			true);

        $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
	} else {
		$PdfData = clone $BracketsInd;
	}

	//$pdf->setOrisCode('', '', true);
	$pdf->SetAutoPageBreak(true,(OrisPDF::bottomMargin+$pdf->extraBottomMargin));

	include(PdfChunkLoader('OrisScoreIndividual.inc.php'));
    $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
    $pdf->endPage();
}
if($cbTeamFinal) {
    if(empty($BracketsTeam)) {
        $PdfData = getBracketsTeams('',
            true,
            false,
            false,
            true,
            true,
            null,
            true);
        $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
    } else {
        $PdfData = clone $BracketsTeam;
    }
	//$pdf->setOrisCode('', '', true);
	$pdf->SetAutoPageBreak(true,(OrisPDF::bottomMargin+$pdf->extraBottomMargin));

	include(PdfChunkLoader('OrisScoreTeam.inc.php'));
    $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
    $pdf->endPage();

    $PdfData=getTeamsComponentsLog();
    $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
    include(PdfChunkLoader('OrisComponentLogTeam.inc.php'));
    $pdf->endPage();
}

// Standing and broken records
$q=safe_r_sql("SELECT count(*) as Involved FROM TourRecords WHERE TrTournament={$_SESSION['TourId']}");
if($r=safe_fetch($q) and $r->Involved) {
	include('Partecipants/OrisStatRecStanding.php');
    $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
    $pdf->endPage();
}
$q=safe_r_sql("SELECT count(*) as Involved FROM RecBroken WHERE RecBroTournament={$_SESSION['TourId']}");
if($r=safe_fetch($q) and $r->Involved) {
	include('Partecipants/OrisStatRecBroken.php');
    $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
    $pdf->endPage();
}

// competition officials
$q=safe_r_sql("SELECT count(*) as Involved FROM TournamentInvolved WHERE TiTournament={$_SESSION['TourId']}");
if($r=safe_fetch($q) and $r->Involved) {
	include('Tournament/OrisStaffField.php');
    $LastUpdate = max($LastUpdate, $PdfData->LastUpdate ?? $LastUpdate);
    $pdf->endPage();
}

$pdf->endPage();
$pdf->Records=array();

// add a new page for TOC
$pdf->setPrintPageNo(false);
$pdf->SetDataHeader(array(), array());
$pdf->setEvent('Complete Results Booklet');
$pdf->setComment('');
$pdf->setOrisCode('SUMMARY', 'Complete Results Booklet');
$pdf->setPhase('');
$pdf->setDocUpdate($LastUpdate ?? '');

$pdf->addTOCPage();

// write the TOC title
$pdf->SetFont('times', 'B', 16);

// disable existing columns
$pdf->resetColumns();
// set columns
$pdf->setEqualColumns(2, ($pdf->getPageWidth()-25)/2);

$pdf->SetFont('freesans', '', 9.5);

// add a simple Table Of Content at first page
// (check the example n. 59 for the HTML version)
$pdf->addTOC(1, 'courier', '.', 'INDEX', 'B');

// end of TOC page
$pdf->endTOCPage();


if(isset($_REQUEST['ToFitarco']))
{
	$Dest='D';
	if (isset($_REQUEST['Dest']))
		$Dest=$_REQUEST['Dest'];
	$pdf->Output($_REQUEST['ToFitarco'],$Dest);
}
else
	$pdf->Output();
