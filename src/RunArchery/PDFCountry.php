<?php
require_once(dirname(__DIR__) . '/config.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/OrisFunctions.php');
require_once('Common/pdf/PdfChunkLoader.php');

CheckTourSession(true);
checkACL(AclParticipants, AclReadOnly);

$PdfData=getRunEntries('','','Country');

if(!isset($isCompleteResultBook)) {
    $pdf = new ResultPDF($PdfData->Description, true, '', false);
}

require_once(PdfChunkLoader('Country_48.inc.php'));

if(!isset($isCompleteResultBook)) {
	if(isset($_REQUEST['ToFitarco'])) {
		$Dest='D';
		if (isset($_REQUEST['Dest']))
			$Dest=$_REQUEST['Dest'];
		$pdf->Output($_REQUEST['ToFitarco'],$Dest);
	} else {
        $pdf->Output();
	}
}
