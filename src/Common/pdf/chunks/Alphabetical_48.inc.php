<?php

$pdf->HideCols = $PdfData->HideCols;
$pdf->setDocUpdate($PdfData->Timestamp ?? $PdfData->LastUpdate ?? '');

$ShowStatusLegend = false;
$OldHeader='';
$OldTime='';
$EventsLegend=[];

$pdf->SetFont($pdf->FontStd,'B',15);
$pdf->Cell(0,0,$PdfData->Description,0,1,'C');
$pdf->ln(5);

foreach($PdfData->Data['Items'] as $ItemHead => $MyRows) {
	foreach($MyRows as $MyRow) {
		if ($OldHeader!=$ItemHead or !$pdf->SamePage(5)) {
			if(!$pdf->SamePage(20)) {
				$pdf->AddPage();
			} elseif($OldHeader) {
				$pdf->dy(6);
			}
			$TmpSegue = !$pdf->SamePage(5);
			if($TmpSegue) {
				$pdf->AddPage();
			}

			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell(0, 6,  $ItemHead, 1, 1, 'C', 1);
			if($TmpSegue) {
				$pdf->SetXY(170,$pdf->GetY()-6);
				$pdf->SetFont($pdf->FontStd,'I',6);
				$pdf->Cell(30, 6,  $PdfData->Continue, 0, 1, 'R', 0);
			}

			$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(15, 0, $PdfData->Data['Fields']['WaId'], 1, 0, 'C', 1);
			$pdf->Cell(10, 0, $PdfData->Data['Fields']['BibShort'], 1, 0, 'C', 1);
			$pdf->Cell(40, 0, $PdfData->Data['Fields']['Athlete'], 1, 0, 'L', 1);
			$pdf->Cell(30, 0, $PdfData->Data['Fields']['Category'], 1, 0, 'L', 1);
			$pdf->Cell(0, 0, $PdfData->Data['Fields']['EventName'], 1, 0, 'L', 1);
			$pdf->ln();

			$OldHeader=$ItemHead;
		}

		// if($OldTime!=$MyRow->RarStartlist) {
		// 	$pdf->dy(1);
		// }
		// $pdf->SetFont($pdf->FontStd,'B',8);
		// $pdf->Cell(25, 0, $MyRow->RarStartlist);
		$pdf->SetFont($pdf->FontStd,'',8);
		$pdf->Cell(15, 0, $MyRow->EnCode, 0,0,'R');
		$pdf->Cell(10, 0, $MyRow->Bib, 0,0,'C');
		$pdf->Cell(40, 0, $MyRow->Entry);
		$pdf->Cell(30, 0, $MyRow->Category);
		$pdf->Cell(0, 0, $MyRow->EventsWithBib);
		$pdf->ln();
		// $OldTime=$MyRow->RarStartlist;
		if($MyRow->Events) {
			$EventsLegend=array_merge($EventsLegend, explode(', ', $MyRow->Events));
		}
	}
}

if($PdfData->Legend) {
	// store current object
	$pdf->startTransaction();
	// store starting values
	$start_y = $pdf->GetY();
	$start_page = $pdf->getPage();
	// call your printing functions with your parameters
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	$pdf->MultiCell(0,0,implode('; ', $PdfData->Legend), 1, 'C', '', 1, '', '', '', '', '1');
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	// get the new Y
	$end_y = $pdf->GetY();
	$end_page = $pdf->getPage();
	// calculate height
	$height = 0;
	if ($end_page == $start_page) {
		$height = $end_y - $start_y;
	} else {
		for ($page=$start_page; $page <= $end_page; ++$page) {
			$this->setPage($page);
			if ($page == $start_page) {
				// first page
				$height += $this->h - $start_y - $this->bMargin;
			} elseif ($page == $end_page) {
				// last page
				$height += $end_y - $this->tMargin;
			} else {
				$height += $this->h - $this->tMargin - $this->bMargin;
			}
		}
	}
	// restore previous object
	$pdf = $pdf->rollbackTransaction();

	if($pdf->samePage($height)) {
		$pdf->setY($pdf->getPageHeight()-$pdf->getBreakMargin()-$height-$pdf->getFooterMargin()-2);
	} else {
		$pdf->ln();
	}
	$pdf->MultiCell(0,0,implode('; ', $PdfData->Legend), 1, 'C', '', 0, '', '', '', '', '1');
}
