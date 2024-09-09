<?php

require_once(dirname(__DIR__) . '/config.php');
require_once('Common/Lib/CommonLib.php');
CheckTourSession(true);
checkACL(AclAccreditation, AclReadWrite);

$CompCodes=getModuleParameter('AccSync', 'CompCodes', array());
$NumRows=0;
$Executed=false;

if(!empty($_REQUEST['PrintAccs'])) {
	require_once('Common/pdf/OrisPDF.inc.php');

    $pdf=new OrisPDF('', get_text('PreviousParticipations', 'Tournament'));
    $pdf->SetMargins(10,40);
    $pdf->AddPage();

    if(!$CompCodes) {
        $pdf->output('DeliveredAccreditation.pdf', 'I');
        die();
    }

	$q=safe_r_sql("select *
 		from Entries
		inner join Tournament on ToId=EnTournament
		inner join Countries on CoId=EnCountry 
		left join (
			select distinct EnCode OldCode, EnDivision OldDivision, ToName OldToName, EnBadgePrinted as OldBadgePrinted, CoCode as OldCountry
			from Entries 
			inner join Countries on CoId=EnCountry ".(!empty($_REQUEST['PrintAccsFilter']) ? " and CoCode like ".StrSafe_DB($_REQUEST['PrintAccsFilter']) : "")."
			inner join Tournament on ToId=EnTournament 
			where ToCode in (".implode(',', StrSafe_DB($CompCodes)).") and EnBadgePrinted>0
			order by ToWhenFrom
			) Oldies on OldCode=EnCode and OldDivision=EnDivision and OldCountry=CoCode
		where EnTournament={$_SESSION['TourId']} and (
		    CoCode = ".StrSafe_DB($_REQUEST['PrintAccs']) ."
		    or EnCode = ".StrSafe_DB($_REQUEST['PrintAccs']) ."
		    or EnFirstName like ".StrSafe_DB('%'.$_REQUEST['PrintAccs'].'%') ."
		)
		order by EnCountry, OldCode is null, EnFirstName, EnName");
	$OldCOuntry='';


	while($r=safe_fetch($q)) {
		if($OldCOuntry!=$r->CoCode) {
			if($OldCOuntry) {
				if($pdf->samePage(35, 1)) {
					$pdf->dy(10);
				} else {
					//$pdf->AddPage();
				}
			}
			$pdf->SetFont('', 'b', '15');
			$pdf->Cell(0,0, $r->CoCode.' '.$r->CoName,'', 1,'L');
			$pdf->SetFont('', '', '10');
			$pdf->dy(5);
		}

		if(!$pdf->samePage(5,1)) {
			$pdf->SetFont('', 'bi', '11');
			$pdf->Cell(0,0, $r->CoCode.' '.$r->CoName  . ' ('.get_text('Continue').')','', 1,'L');
			$pdf->SetFont('', '', '10');
			$pdf->dy(2);
		}

		$pdf->Cell(15, 0, $r->EnCode, '', 0,'R');
		$pdf->Cell(30, 0, $r->EnFirstName);
		$pdf->Cell(30, 0, $r->EnName);
		$pdf->Cell(10,0,$r->EnDivision);
		$pdf->Cell(0, 0, $r->OldToName ?: '-','', 1);

		$OldCOuntry=$r->CoCode;
	}

	$pdf->output('DeliveredAccreditation.pdf', 'I');
	die();
}

$PAGE_TITLE=get_text('MenuLMPrintedBadgesFlag');
$JS_SCRIPT[]='<script src="./AccreditationSync.js"></script>';
$JS_SCRIPT[]=phpVars2js([
    'txtCancel' => get_text('CmdCancel'),
    'txtAdd' => get_text('CmdAdd', 'Tournament'),
]);
$IncludeFA=true;
$IncludeJquery=true;
include('Common/Templates/head.php');

echo '<table class="Tabella freeWidth">
    <tr><th class="Main" colspan="6">'.$PAGE_TITLE.'</th></tr>
    <tr><th class="Title" colspan="6">'.get_text('CurrentCompCode', 'Tournament', $_SESSION['TourCode']).'</th></tr>
    <tr>
        <th class="Title"><i class="fa fa-lg fa-plus-square" onclick="addCode()"></i></th>
        <th class="Title">Competitions</th>
        <th class="Title">Entries</th>
        <th class="Title">Printed</th>
        <th class="Title">Photo taken</th>
        <th class="Title">Photo to retake</th>
    </tr>
    <tbody id="AccreditationBody"></tbody>
    <tr><td colspan="6" class="Center"><div class="Button" onclick="syncComp()">'.get_text('CmdSync').'</div></td></tr>
    <tr><td colspan="6" class="Center"><input type="text" id="PrintAccsFilter"><div class="Button mx-2" onclick="printPrevious()">'.get_text('PrintedAccreditation', 'Tournament').'</div></td></tr>
    </table>';

include('Common/Templates/tail.php');
