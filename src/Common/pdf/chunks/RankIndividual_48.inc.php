<?php

$PdfData->LastUpdate=$PdfData->rankData['meta']['lastUpdate'];

$pdf->setDocUpdate($PdfData->LastUpdate);

$FirstPage=true;
$Fields=$PdfData->rankData['fields'];

foreach($PdfData->rankData['sections'] as $section) {
	foreach($section as $Event) {
		$OldEvent=true;
		foreach($Event['phases'] as $PhaseNum => $Phase) {
			if(count($Phase['items'])) {
				$HaseQualified=0;
				$CellW=($pdf->getPageWidth()-$pdf->getSideMargin()*2-108)/4;
				if(count($Event['phases'])>1 and $PhaseNum!=1) {
					$CellW=($pdf->getPageWidth()-$pdf->getSideMargin()*2-113)/4;
					$HaseQualified=1;
				}
				if(!$FirstPage and ($OldEvent or !$pdf->SamePage(4*count($Phase['items'])+15))) {
					$pdf->AddPage();
				}
				$FirstPage=false;
				$OldEvent=!$PdfData->rankData['meta']['byClass'];
				$NeedTitle=true;

				$OldPool='';

				foreach($Phase['items'] as $item) {
					if(!$pdf->SamePage(4)) {
						$NeedTitle=true;
					}

					//Valuto Se è necessario il titolo
					if($NeedTitle) {
						// title of the event and phase
						$pdf->ln(5);
				        $pdf->SetFont($pdf->FontStd,'B',10);
						$pdf->Cell(0, 0, $Event['meta']['evCode'].' - '.$Event['meta']['descr'].($Event['meta']['class'] ? ' / '.$Event['meta']['class'].' - '.$Event['meta']['className'] : ''), 'LTR', 1, 'C', 1);
				        $pdf->SetFont($pdf->FontStd,'B',8);
						$pdf->Cell(0, 0, $Phase['name'], 'LBR', 1, 'C', 1);

						// Header vero e proprio
					    $pdf->SetFont($pdf->FontStd,'B',7);
						$pdf->Cell(12, 5, $Fields['rank'], 1, 0, 'C', 1);
						$pdf->Cell(40, 5, $Fields['athlete'], 1, 0, 'C', 1);
						$pdf->Cell(10, 5, $Fields['class'], 1, 0, 'C', 1);
						$pdf->Cell(46, 5, $Fields['countryName'], 1, 0, 'C', 1);
						$pdf->Cell($CellW, 5, $Fields['FinalTime'], 1, 0, 'C', 1);
						if($HaseQualified) {
							$pdf->Cell(5, 5, '', 1, 0, 'C', 1);
						}
						$pdf->Cell($CellW, 5, $Fields['RunningTime'], 1, 0, 'C', 1);
						$pdf->Cell($CellW, 5, $Fields['PenaltyTime'], 1, 0, 'C', 1);
						$pdf->Cell($CellW, 5, $Fields['AdjustedTime'], 1, 0, 'C', 1);
						$pdf->ln();
						$NeedTitle=false;
					}

					if($item['Pool'] and $OldPool!=$item['Pool']) {
						$pdf->ln(2);
						$pdf->SetFont($pdf->FontStd,'B',7);
						$pdf->Cell(0, 5, $Phase['Pools'][$item['Pool']], 1, 1, 'C', 1);
					}

				    $pdf->SetFont($pdf->FontStd,'B',8);
					$pdf->Cell(12, 4, ($item['rank'] ? $item['rank'] : ''), 0, 0, 'C', 0);
				    $pdf->SetFont($pdf->FontStd,'',8);
					$pdf->Cell(40, 4, $item['athlete'], 0, 0, 'L', 0);
					$pdf->Cell(10, 4, $item['ageclass'], 0, 0, 'C', 0);
					$pdf->Cell(10, 4, $item['countryCode'], 0, 0, 'C', 0);
					$pdf->Cell(36, 4, $item['countryName'], 0, 0, 'L', 0);
					$pdf->SetFont($pdf->FontStd,'B',8);
					$pdf->Cell($CellW, 4, $item['FinalTime'], 0, 0, 'R');
					$pdf->SetFont($pdf->FontStd,'',8);
					if($HaseQualified) {
						$pdf->Cell(5, 4, trim($item['Qualified']), 0, 0, 'C');
					}
					$pdf->Cell($CellW, 4, $item['RunningTime'], 0, 0, 'R');
					$pdf->Cell($CellW, 4, $item['PenaltyTime'], 0, 0, 'R');
					$pdf->Cell($CellW, 4, $item['AdjustedTime'], 0, 0, 'R');
					$pdf->ln();
					$OldPool=$item['Pool'];
				}
			}
		}
	}
}
