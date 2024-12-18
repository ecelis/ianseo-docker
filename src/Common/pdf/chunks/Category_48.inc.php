<?php

$pdf->HideCols = $PdfData->HideCols;
$pdf->setDocUpdate($PdfData->Timestamp ?? $PdfData->LastUpdate ?? '');

$ShowStatusLegend = false;
$OldHeader='';

$pdf->SetFont($pdf->FontStd,'B',15);
$pdf->Cell(0,0,$PdfData->Description,0,1,'C');
$pdf->ln(5);

// events will be ordered by time...

foreach($PdfData->Data['Items'] as $ItemHead => $Phases) {
	$NeedSubtitle=count($Phases)>1;
	foreach($Phases as $Phase=>$MyRows) {
		if(!$pdf->SamePage(16+4*count($MyRows))) {
			$pdf->AddPage();
		}

		$OldGroup=0;
		$OldPool=0;

		foreach($MyRows as $MyRow) {
			if ($OldHeader!=$ItemHead.$Phase or !$pdf->SamePage(4)) {
				$OldTime=$MyRow->RarStartlist;
				// print new event header
				if(!$pdf->SamePage(20)) {
					$pdf->AddPage();
				} elseif($OldHeader) {
					$pdf->dy(6);
				}

				if($OldHeader!=$ItemHead.$Phase) {
					$OldGroup=0;
					$OldPool=0;
				}

				if($TmpSegue= !$pdf->SamePage(4)) {
					$pdf->AddPage();
				}

				$pdf->SetFont($pdf->FontStd,'B',10);
				$pdf->Cell(0, 6,  $MyRow->EvenTitle, 1, 1, 'C', 1);
				if($TmpSegue) {
					$pdf->SetXY(170,$pdf->GetY()-6);
					$pdf->SetFont($pdf->FontStd,'I',6);
					$pdf->Cell(30, 6,  $PdfData->Continue, 0, 1, 'R', 0);
				}

				$pdf->SetFont($pdf->FontStd,'B',7);
				$pdf->Cell(15, 0, $PdfData->Data['Fields']['Bib'], 1, 0, 'C', 1);
				$pdf->Cell(10, 0, $PdfData->Data['Fields']['TgtGrp'], 1, 0, 'C', 1);
				$pdf->Cell(10, 0, $PdfData->Data['Fields']['Target'], 1, 0, 'C', 1);
				$pdf->Cell(50, 0, $PdfData->Data['Fields']['Athlete'], 1, 0, 'L', 1);
				$pdf->Cell(10, 0, $PdfData->Data['Fields']['AgeClass'], 1, 0, 'L', 1);
				$pdf->Cell(15, 0, $PdfData->Data['Fields']['NationCode'], 1, 0, 'L', 1);
				$pdf->Cell(40, 0, $PdfData->Data['Fields']['Nation'], 1, 0, 'L', 1);
				$pdf->Cell(0, 0, $PdfData->Data['Fields']['StartTime'], 1, 0, 'C', 1);
				$pdf->ln(4);

				$OldHeader=$ItemHead.$Phase;
			}
			if($Phase and $OldPool!=$MyRow->RarPool) {
				$pdf->SetFont($pdf->FontStd,'B',7);
				if($OldPool) {
					$pdf->ln(2);
				} else {
					$pdf->ln(-0.5);
				}
				$pdf->Cell(0, 0, $MyRow->PoolName, 1, 1, 'C', 1);
			}
			$OldPool=$MyRow->RarPool;

			if((!$MyRow->IsSingle and $OldTime!=$MyRow->RarStartlist) or ($MyRow->IsSingle and $OldGroup and $Phase==0 and $OldGroup!=$MyRow->TgtGrp)) {
				$pdf->dy(2);
			}

			$pdf->SetFont($pdf->FontStd,'B',8);
			$pdf->Cell(15, 0, $MyRow->Bib, 0, 0, 'C');
			$pdf->SetFont($pdf->FontStd,'',8);
			$pdf->Cell(10, 0, $MyRow->TgtGrp, 0, 0, 'C');
			$pdf->Cell(10, 0, $MyRow->Target, 0, 0, 'C');
			$pdf->Cell(50, 0, $MyRow->Entry);
			$pdf->Cell(10, 0, $MyRow->AgeClass, 0, 0, 'C');
			$pdf->Cell(15, 0, $MyRow->NocCode, 0, 0, 'C');
			$pdf->Cell(40, 0, $MyRow->NocName. ($MyRow->RarSubTeam==0 ? "" : " (" . ($MyRow->RarSubTeam+1) . ")"));
			$pdf->Cell(0, 0, $MyRow->IsSingle ? $MyRow->RarStartlist : substr($MyRow->RarStartlist,0,-3));
			$pdf->ln(4);
			$OldTime=$MyRow->RarStartlist;
			$OldGroup=$MyRow->TgtGrp;
		}
	}
}

