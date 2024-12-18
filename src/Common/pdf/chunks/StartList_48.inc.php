<?php

$pdf->HideCols = $PdfData->HideCols;
$pdf->setDocUpdate($PdfData->Timestamp ?? $PdfData->LastUpdate ?? '');

$ShowStatusLegend = false;
$OldHeader='';
$OldTime='';


$pdf->SetFont($pdf->FontStd,'B',15);
$pdf->Cell(0,0,$PdfData->Description,0,1,'C');
$pdf->ln(5);

// // we need to re-arrange the whole thing by time!
// $OrderedData=[];
// foreach($PdfData->Data['Items'] as $ItemHead => $MyRows) {
// 	foreach ($MyRows as $MyRow) {
// 		$Date=substr($MyRow->RarStartlist, 0, 10);
// 		$Time=substr($MyRow->RarStartlist, 11);
// 		if(empty($OrderedData[$Date])) {
// 			$OrderedData[$Date]
// 		}
// 	}
// }

$OldDate='';
$TimeWidth=18;
$EvWidth=$pdf->getPageWidth()-2*$pdf->getSideMargin()-140-$TimeWidth;
$OldPhase=0;
foreach($PdfData->Data['Items'] as $ItemHead => $Phases) {
	foreach($Phases as $Phase => $MyRows) {
		if(!$pdf->SamePage(16+4*count($MyRows))) {
			$pdf->AddPage();
		}
		$OldGroup=0;
		$OldPool=0;
		foreach($MyRows as $MyRow) {
			if ($OldHeader!=$ItemHead or !$pdf->SamePage(4) or $OldPhase!=$MyRow->RarPhase) {
				// print new event header
				if(!$pdf->SamePage(20)) {
					$pdf->AddPage();
				} elseif($OldHeader) {
					$pdf->dy(6);
				}
				$TmpSegue = !$pdf->SamePage(4);
				if($TmpSegue or $OldPhase!=$MyRow->RarPhase) {
					$pdf->AddPage();
				}

                $OldPhase=$MyRow->RarPhase;
                if($OldPhase and $OldPhase==$MyRow->RarPhase) {
                    $OldDate=0;
                }
				list($Date, $Time) = explode(' ', $MyRow->RarStartlist);

				if($MyRow->IsSingle and $Phase==0) {
					$LastRow=end($MyRows);
					list($d, $LastTime)=explode(' ', $LastRow->RarStartlist);
					if($Time!=$LastTime) {
						$Time.=' => '.$LastTime;
					}
				}

				if($OldHeader!=$ItemHead) {
					$OldGroup=0;
				}

				$pdf->SetFont($pdf->FontStd,'B',10);

				if($OldDate!=$Date) {
					if($OldDate) {
						$pdf->AddPage();
					}
					$pdf->Cell(0, 8, $Date, 1, 1, 'C', 1);
				}
				$OldDate=$Date;

				$pdf->Cell(0, 6, $Time, 1, 1, 'C', 1);
				if($TmpSegue) {
					$pdf->SetXY(170,$pdf->GetY()-6);
					$pdf->SetFont($pdf->FontStd,'I',6);
					$pdf->Cell(30, 6,  $PdfData->Continue, 0, 1, 'R', 0);
				}

				$pdf->SetFont($pdf->FontStd,'B',7);
				$pdf->Cell(10, 0, $PdfData->Data['Fields']['Bib'], 1, 0, 'C', 1);
				$pdf->Cell(10, 0, $PdfData->Data['Fields']['TgtGrp'], 1, 0, 'C', 1);
				$pdf->Cell(10, 0, $PdfData->Data['Fields']['Target'], 1, 0, 'C', 1);
				$pdf->Cell(50, 0, $PdfData->Data['Fields']['Athlete'], 1, 0, 'L', 1);
				$pdf->Cell(10, 0, $PdfData->Data['Fields']['AgeClass'], 1, 0, 'L', 1);
				$pdf->Cell(15, 0, $PdfData->Data['Fields']['NationCode'], 1, 0, 'L', 1);
				$pdf->Cell(35, 0, $PdfData->Data['Fields']['Nation'], 1, 0, 'L', 1);

				if($MyRow->IsSingle and $Phase==0) {
					$pdf->Cell($EvWidth, 0, $PdfData->Data['Fields']['EventName'], 1, 0, 'L', 1);
					$pdf->Cell($TimeWidth, 0, $PdfData->Data['Fields']['StartTime'], 1, 0, 'C', 1);
				} else {
					$pdf->Cell(0, 0, $PdfData->Data['Fields']['EventName'], 1, 0, 'L', 1);
				}
				$pdf->ln();

				$OldHeader=$ItemHead;
			}

			if((!$MyRow->IsSingle and $OldTime!=$MyRow->RarStartlist) or ($MyRow->IsSingle and $MyRow->RarPhase==0 and $OldGroup and $OldGroup!=$MyRow->TgtGrp)) {
				$pdf->dy(2);
			}

			$pdf->SetFont($pdf->FontStd,'B',8);
			$pdf->Cell(10, 0, $MyRow->Bib, 0, 0, 'C');
			$pdf->SetFont($pdf->FontStd,'',8);
			$pdf->Cell(10, 0, $MyRow->TgtGrp,0,0,'C');
			$pdf->Cell(10, 0, $MyRow->Target,0,0,'C');
			$pdf->Cell(50, 0, $MyRow->Entry);
			$pdf->Cell(10, 0, $MyRow->AgeClass, 0, 0, 'C');
			$pdf->Cell(15, 0, $MyRow->NocCode, 0, 0, 'C');
			$pdf->Cell(35, 0, $MyRow->NocName. ($MyRow->RarSubTeam==0 ? "" : " (" . ($MyRow->RarSubTeam+1) . ")"));
			if($MyRow->IsSingle and $MyRow->RarPhase==0) {
				$pdf->Cell($EvWidth, 0, $MyRow->EvCode.' - '.$MyRow->EvEventName);
				$pdf->Cell($TimeWidth, 0, substr($MyRow->RarStartlist,11));
			} else {
				$pdf->Cell(0, 0, $MyRow->EvenFullName);
			}
			$pdf->ln();
			$OldTime=$MyRow->RarStartlist;
			$OldGroup=$MyRow->TgtGrp;
		}
	}
}

