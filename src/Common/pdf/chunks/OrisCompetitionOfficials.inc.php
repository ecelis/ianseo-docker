<?php

$pdf->SetDataHeader($PdfData->Header, $PdfData->HeaderWidth);
$pdf->setPhase('');
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


foreach($PdfData->Data['Items'] as $Group=>$Names) {
	$pdf->SamePage(count($Names), 3.5, $pdf->lastY);
	$pdf->lastY += 3.5;
    $first=true;
	foreach($Names as $name) {
        $tmp=array(
            ($first ? "~".$name->ItDescription : ''),
            mb_strtoupper($name->TiName, 'UTF-8') . ' ' . $name->TiGivenName,
            mb_strtoupper($name->CoCode, 'UTF-8'), $name->CoNameComplete,
            "§".($name->TiGender==0 ? 'M' : 'F')
        );
        $first=false;
        $pdf->printDataRow($tmp);
	}
}
