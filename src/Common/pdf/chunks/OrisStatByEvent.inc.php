<?php

$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase('');
$pdf->setDocUpdate($PdfData->LastUpdate ?? $PdfData->Timestamp ?? '');

$pdf->setOrisCode($PdfData->Code, $PdfData->Description);
$pdf->AddPage();
$pdf->Bookmark($PdfData->IndexName, 0);

foreach($PdfData->Data as $EvCode => $MyRow) {
	$tmp=array(
		$MyRow['Name'],
		number_format($MyRow['Number'],0,'','.').'#',
        number_format($MyRow['Countries'],0,'','.').'#',
        number_format($MyRow['Teams'],0,'','.').'#'
		);
	$pdf->printDataRow($tmp);
}
