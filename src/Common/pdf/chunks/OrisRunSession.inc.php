<?php

$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase($PdfData->Phase);
$pdf->setDocUpdate($PdfData->LastUpdate ?? $PdfData->Timestamp ?? '');

$OldEvent='';
$OldTarget='';
$First=true;

if(!empty($PdfData->Data['Items'])) {
	foreach($PdfData->Data['Items'] as $Header => $MyRows) {
		foreach($MyRows as $MyRow) {

			if($OldEvent!=$Header) {
				if(!$OldEvent) {
					$Version='';
					if($MyRow->DocVersion) {
						$Version=trim('Vers. '.$MyRow->DocVersion . " ($MyRow->DocVersionDate) $MyRow->DocNotes");
					}
					$pdf->setComment($Version);
					$pdf->AddPage();

					$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
					if($First and (empty($pdf->CompleteBookTitle) or $pdf->CompleteBookTitle!=$PdfData->IndexName)) {
						$pdf->Bookmark($PdfData->IndexName, 0);
						$pdf->CompleteBookTitle=$PdfData->IndexName;
					}
				} else {
					$pdf->lastY+=2;
				}
				// Each Event starts on a new page
				// $pdf->setEvent($Header);
				$First=false;
				$pdf->Bookmark($Header, 1);
				$OldTarget='';
				$OldEvent=$Header;
				$firstRow=true;
			}

			// $TgNo=$MyRow->Bib;
			// $Col1=$MyRow->Bib . "  #";
			// if($OldTarget!=$TgNo) {
			// 	// separates the new target
			// 	$pdf->SamePage($MyRow->SesAth4Target + 2, 3.5, $pdf->lastY); // because we must take into account the last previous row AND the separator
			// 	$pdf->lastY += 3.5;
			// 	$OldTarget=$TgNo;
			// }

			$tmp=[				$MyRow->RarStartlist,
				$MyRow->Bib,
				$MyRow->Entry,
				$MyRow->NocCode,
				$MyRow->NocName,
				$PdfData->IsRanked ? ($MyRow->Ranking ? $MyRow->Ranking : '-').'    #' : '',
				$MyRow->DOB,
				$MyRow->EvEventName,
			];

			if(!$MyRow->IsSingle and !$firstRow and $pdf->samePage(2, 3.5, $pdf->lastY, false)) {
				$tmp[0]='';
			}

			$pdf->printDataRow($tmp);

			$firstRow=false;

			if(!isset($PdfData->HTML)) continue;

			$PdfData->HTML['sessions'][$MyRow->Session]['Description']=($MyRow->SesName ? $MyRow->SesName : $PdfData->Data['Fields']['Session'] . ' ' . $MyRow->Session);
			// may go for several events...
			if(empty($PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo])) {
				$PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo]=array(
					(!empty($PdfData->BisTarget) && (intval(substr($MyRow->TargetNo,1)) > $PdfData->NumEnd) ? 'bis ' . (substr($MyRow->TargetNo,0,-1)-$PdfData->NumEnd) . substr($MyRow->TargetNo,-1,1)  : $MyRow->TargetNo),
					$MyRow->Athlete,
					$MyRow->NationCode,
					$MyRow->Nation,
					$MyRow->DivDescription . ' ' . $MyRow->ClDescription,
					$MyRow->EvEventName,
					);
			} elseif(!empty($PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo][5])) {
				$PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo][4]=$PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo][5].', '.$MyRow->EvEventName;
				unset($PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo][5]);
			} else {
				$PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$TgNo][$MyRow->TargetNo][4].=', '.$MyRow->EvEventName;
			}
		}
	}
}

