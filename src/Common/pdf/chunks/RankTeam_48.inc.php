<?php

$PdfData->LastUpdate=$PdfData->rankData['meta']['lastUpdate'];

$pdf->setDocUpdate($PdfData->LastUpdate);

$FirstPage=true;
$Fields=$PdfData->rankData['fields'];

$CellW=($pdf->getPageWidth()-$pdf->getSideMargin()*2-94)/4;
foreach($PdfData->rankData['sections'] as $section) {
	foreach($section as $Event) {
		foreach($Event['phases'] as $Phase) {
			if(count($Phase['items'])) {

				if(!$FirstPage) $pdf->AddPage();
				$FirstPage=false;
				$NeedTitle=true;

				foreach($Phase['items'] as $item) {
					if(!$pdf->SamePage(4)) {
						$NeedTitle=true;
					}

					//Valuto Se è necessario il titolo
					if($NeedTitle) {
						// title of the event and phase
						$pdf->ln(5);
				        $pdf->SetFont($pdf->FontStd,'B',10);
						$pdf->Cell(0, 0, $Event['meta']['event'].' - '.$Event['meta']['descr'], 'LTR', 1, 'C', 1);
				        $pdf->SetFont($pdf->FontStd,'B',8);
						$pdf->Cell(0, 0, $Phase['name'], 'LBR', 1, 'C', 1);

						// Header vero e proprio
					    $pdf->SetFont($pdf->FontStd,'B',7);
						$pdf->Cell(8, 5, $Fields['rank'], 1, 0, 'C', 1);
						$pdf->Cell(46, 5, $Fields['countryName'], 1, 0, 'C', 1);
						$pdf->Cell(40, 5, $Fields['athlete'], 1, 0, 'C', 1);
						$pdf->Cell($CellW, 5, $Fields['FinalTime'], 1, 0, 'C', 1);
						$pdf->Cell($CellW, 5, $Fields['RunningTime'], 1, 0, 'C', 1);
						$pdf->Cell($CellW, 5, $Fields['PenaltyTime'], 1, 0, 'C', 1);
						$pdf->Cell($CellW, 5, $Fields['AdjustedTime'], 1, 0, 'C', 1);
						$pdf->ln();
						$NeedTitle=false;
					}

					$TeamComponents=array_values($Event['teams']['id-'.$item['id'].'-'.$item['subteam']]);
					$pdf->ln(2);
				    $pdf->SetFont($pdf->FontStd,'B',8);
					$pdf->Cell(8, 4, ($item['rank'] ? $item['rank'] : ''), 0, 0, 'C', 0);
				    $pdf->SetFont($pdf->FontStd,'',8);
					$pdf->Cell(10, 4, $item['countryCode'], 0, 0, 'C', 0);
					$pdf->Cell(36, 4, $item['countryName'], 0, 0, 'L', 0);
					$pdf->Cell(40, 4, $TeamComponents[0]['athlete'], 0, 0, 'L', 0);
					$pdf->SetFont($pdf->FontStd,'B',8);
					$pdf->Cell($CellW, 4, $item['FinalTime'], 0, 0, 'R');
					$pdf->SetFont($pdf->FontStd,'',8);
					$pdf->Cell($CellW, 4, $item['RunningTime'], 0, 0, 'R');
					$pdf->Cell($CellW, 4, $item['PenaltyTime'], 0, 0, 'R');
					$pdf->Cell($CellW, 4, $item['AdjustedTime'], 0, 0, 'R');
					$pdf->ln();
					foreach($TeamComponents as $k=>$v) {
						if($k) {
							$pdf->Cell(54,4,'');
							$pdf->Cell(40,4,$v['athlete']);
							$pdf->ln();
						}
					}
				}
			}
		}
	}
}
