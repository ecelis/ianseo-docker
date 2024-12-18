<?php

$pdf->HideCols = $PdfData->HideCols;
$pdf->setDocUpdate($PdfData->Timestamp ?? $PdfData->LastUpdate ?? '');

$ShowStatusLegend = false;
$CurSession=-1;
$OldTarget='';
$Components=array('ok'=>false,'players'=>array(),'header'=>array());
$TargetFace=(isset($_REQUEST['tf']) && $_REQUEST['tf']==1);
$key='';
foreach($PdfData->Data['Items'] as $MyRows) {
	foreach($MyRows as $MyRow) {
		if($key==$MyRow->TargetNo.$MyRow->Bib.$MyRow->DivCode.$MyRow->ClassCode) {
			continue;
		}
		$key=$MyRow->TargetNo.$MyRow->Bib.$MyRow->DivCode.$MyRow->ClassCode;

		if ($CurSession != $MyRow->Session || !$pdf->SamePage(4) || (substr($MyRow->TargetNo,-1,1)=='A' && !$pdf->SamePage(4*$MyRow->SesAth4Target))) {
			$TmpSegue = !$pdf->SamePage(4);	//Segue se non ci stanno 4 pixel
			if(substr($MyRow->TargetNo,-1,1)=='A' && !$pdf->SamePage(4*$MyRow->SesAth4Target))	//Segue se non ci sta il paglione intero
			{
				$TmpSegue=true;
				$pdf->AddPage();
			}

			if($CurSession != $MyRow->Session) {
				// print the last component of previous session
				if($Components['players']) $pdf->PrintComponents($OldTarget, $Components, empty($_REQUEST['Filled']), $TargetFace); // new function
					$Components=array('ok'=>false,'players'=>array(),'header'=>array());

				//Add a page if the list doesn't fit a target + the header
				if(!$pdf->SamePage(12+4*$MyRow->SesAth4Target))
					$pdf->AddPage();
				elseif($CurSession != -1)
					$pdf->SetY($pdf->GetY()+2);
			}

			$CurSession = $MyRow->Session;

			$pdf->SetFont($pdf->FontStd,'B',10);
			$txt='';
			if ($MyRow->SesName!='') {
                $txt = $MyRow->SesName . ' (' . $PdfData->Data['Fields']['Session'] . ' ' . $CurSession . ')';
            } else {
                $txt = $PdfData->Data['Fields']['Session'] . ' ' . $CurSession;
            }

			$pdf->Cell(190, 6,  $txt, 1, 1, 'C', 1);
			if($TmpSegue) {
				$pdf->SetXY(170,$pdf->GetY()-6);
			    $pdf->SetFont($pdf->FontStd,'I',6);
				$pdf->Cell(30, 6,  $PdfData->Continue, 0, 1, 'R', 0);
			}
		    $pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(11, 4, $PdfData->Data['Fields']['TargetNo'], 1, 0, 'C', 1);
			$pdf->Cell(10, 4, $PdfData->Data['Fields']['Bib'], 1, 0, 'C', 1);
			$pdf->Cell(44, 4, $PdfData->Data['Fields']['Athlete'], 1, 0, 'L', 1);
			$pdf->Cell(56, 4, $PdfData->Data['Fields']['NationCode'], 1, 0, 'L', 1);
			if(!$pdf->HideCols && !$TargetFace) {
				$pdf->Cell(12, 4, $PdfData->Data['Fields']['AgeClass'], 1, 0, 'C', 1);
				$pdf->Cell(9, 4, $PdfData->Data['Fields']['SubClass'], 1, 0, 'C', 1);
			}
			$pdf->Cell(12 + ($pdf->HideCols ? ($TargetFace ? 12 : 23) : 0), 4, $PdfData->Data['Fields']['DivCode'], 1, 0, 'C', 1);
			$pdf->Cell(12 + ($pdf->HideCols ? ($TargetFace ? 12 : 22) : 0), 4, $PdfData->Data['Fields']['ClassCode'], 1, 0, 'C', 1);

			if ($TargetFace) {
                $pdf->Cell(3, 4, $PdfData->Data['Fields']['Wheelchair'], 1, 0, 'C', 1);
                $pdf->Cell(18, 4, $PdfData->Data['Fields']['TargetFace'], 1, 0, 'C', 1);
			}


			//Disegna i Pallini
			if(!$pdf->HideCols) {
				$pdf->DrawParticipantHeader();
			    $pdf->SetFont($pdf->FontStd,'B',7);
				$pdf->Cell(10, 4, $PdfData->Data['Fields']['Status'], 1, 0, 'C', 1);
			}
			$pdf->Cell(1, 4,  '', 0, 1, 'C', 0);
			$OldTeam='';
			$FirstTime=false;
		}
		//$pdf->SetFont($pdf->FontStd,'B',8);
		$temprow=array();
		if($OldTarget != substr($MyRow->TargetNo,0,-1)) {
			if($Components['players']) $pdf->PrintComponents($OldTarget, $Components, empty($_REQUEST['Filled']),$TargetFace); // new function
			$Components=array('ok'=>false,'players'=>array(),'header'=>array());
			$OldTarget = substr($MyRow->TargetNo,0,-1);
			$Targetno = intval($OldTarget);
			$BisValue='';
			if($PdfData->BisTarget && ($Targetno > $PdfData->NumEnd)) {
				$Targetno -= $PdfData->NumEnd;
				$BisValue='bis';

				if($Targetno > $PdfData->NumEnd) {
					$Targetno -= $PdfData->NumEnd;
					$BisValue='ter';
				}

			}

			$Components['header'][]=$BisValue;
			$Components['header'][]=$Targetno;
		}
		$Components['ok']=($Components['ok'] or $MyRow->Athlete);
		$temprow[]=substr($MyRow->TargetNo,-1,1);
		$temprow[]= ($MyRow->Bib ?? '');
		$temprow[]= ($MyRow->Athlete ?? '');
		$temprow[]= ($MyRow->NationCode ?? '');
		$temprow[]= $MyRow->Nation . ($MyRow->EnSubTeam==0 ? "" : " (" . $MyRow->EnSubTeam . ")");
		$temprow[]= ($MyRow->AgeClass ?? '');
		$temprow[]= ($MyRow->SubClass ?? '');
		$temprow[]= ($pdf->HideCols ? $MyRow->DivDescription : $MyRow->DivCode) ?? '';
		$temprow[]= ($pdf->HideCols ? $MyRow->ClDescription : $MyRow->ClassCode) ?? '';
		$temprow[]= array($MyRow->IC, $MyRow->IF, $MyRow->TC, $MyRow->TF, $MyRow->TM);
		$temprow[]= $MyRow->Status==0 ? '' : ($MyRow->Status ?? '');
		$temprow[]= ($MyRow->NationCode2 ?? '');
		$temprow[]= ($MyRow->Nation2 ?? '');
		$temprow[]= ($MyRow->TfName ?? '');
		$temprow[]= ($MyRow->NationCode3 ?? '');
		$temprow[]= ($MyRow->Nation3 ?? '');
        $temprow[]= ($MyRow->Wheelchair ?? '');

		$ShowStatusLegend = ($ShowStatusLegend || ($MyRow->Status!=0));
		$Components['players'][]=$temprow;

		if(isset($PdfData->HTML) && $MyRow->Athlete) {
			$PdfData->HTML['sessions'][$MyRow->Session]['Description']=($MyRow->SesName ? $MyRow->SesName : $PdfData->Data['Fields']['Session'] . ' ' . $MyRow->Session);
			$PdfData->HTML['sessions'][$MyRow->Session]['Targets'][$OldTarget][]=array(
				(!empty($PdfData->BisTarget) && (intval(substr($MyRow->TargetNo,1)) > $PdfData->NumEnd) ? 'bis ' . (substr($MyRow->TargetNo,0,-1)-$PdfData->NumEnd) . substr($MyRow->TargetNo,-1,1)  : $MyRow->TargetNo),
				$MyRow->Athlete,
				$MyRow->NationCode,
				$MyRow->Nation,
		//			($MyRow->EvCode ? $MyRow->EventName : $MyRow->DivDescription . ' ' . $MyRow->ClDescription),
				$MyRow->DivDescription . ' ' . $MyRow->ClDescription,
				);
		}
	}
}

if($Components['players']) $pdf->PrintComponents($OldTarget, $Components, empty($_REQUEST['Filled']),$TargetFace); // check for the last entry

if(!$pdf->HideCols)
{
//Legenda per la partecipazione alle varie fasi
	$pdf->DrawPartecipantLegend();
//Legenda per lo stato di ammisisone alle gare
	if($ShowStatusLegend)
		$pdf->DrawStatusLegend();
}

?>