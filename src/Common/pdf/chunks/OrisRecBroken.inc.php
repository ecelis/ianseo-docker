<?php

$OldStop=$pdf->StopHeader;
$pdf->StopHeader=true;
$pdf->setPhase('As of '.$PdfData->RecordAs);
$pdf->setEvent($PdfData->Description);

$pdf->setDocUpdate($PdfData->LastUpdate ?? $PdfData->Timestamp ?? '');

$Version='';
if($PdfData->DocVersion) {
	$Version=trim('Vers. '.$PdfData->DocVersion . " ($PdfData->DocVersionDate) $PdfData->DocVersionNotes");
}
$pdf->setComment($Version);
$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
$pdf->AddPage();
$pdf->Bookmark($PdfData->IndexName, 0);

$ONLINE=isset($PdfData->HTML);

$AddPage=false;
$first=true;

if(empty($PdfData->Data['Items'])) {
	$pdf->printSectionTitle('No data§', $pdf->GetY()+10);
} else {
	$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
	foreach($PdfData->Data['Items'] as $Team => $Rows) {
		if($AddPage) {
			$pdf->addpage();
			$first=true;
		}
		if(!$pdf->samePage(5, 3.5, $pdf->lastY)) {
			$first=true;
		}
		//$AddPage=true;
		$pdf->SamePage(count($Rows), 3.5, $pdf->lastY);
		$lstPictures = array();
		$lstDoB = array();

		foreach($Rows as $RecType => $MyRows) {
			if(!$pdf->samePage(4+intval($MyRows[0]->RtRecComponents), 3.5, $pdf->lastY)) {
				/// must keep at least the space before header, header and captions and 1 line of record
				$first=true;
			}
			$pdf->printSectionTitle($PdfData->SubSections[$Team][$RecType].'§', $pdf->lastY+($first ? 0 : 5));
			$pdf->ln();
			$pdf->PrintHeader($pdf->GetX(), $pdf->GetY()+1);
			foreach($MyRows as $MyRow) {
				if(!$pdf->samePage(1+intval($MyRow->RtRecComponents), 3.5, $pdf->lastY)) {
					$pdf->printSectionTitleWContinue($PdfData->SubSections[$Team][$RecType].'§', $pdf->lastY);
					$pdf->ln();
					$pdf->PrintHeader($pdf->GetX(), $pdf->GetY()+1);
				}
				$OldRecord='*';
				if($MyRow->RtRecTotal) {
					$OldRecord=$MyRow->RtRecTotal.(($MyRow->CheckXNine and $MyRow->RtRecXNine) ? "/$MyRow->RtRecXNine" : '');
				}
				$tmp=array(
					$MyRow->RtRecDistance,
					'§'. $OldRecord . ' / ' . $MyRow->NewRecord.(($MyRow->CheckXNine) ? "/$MyRow->NewXNine" : ''),
					$MyRow->Athlete,
					'§'.$MyRow->CoCode,
					substr($MyRow->RecordDate,0,10).'#'
					);
				$pdf->printDataRow($tmp);
			}
			$first=false;
		}
	}
}

$pdf->StopHeader=$OldStop;
