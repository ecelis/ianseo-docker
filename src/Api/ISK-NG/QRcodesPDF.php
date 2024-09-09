<?php
require_once(dirname(__FILE__).'/config.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Lib/CommonLib.php');
require_once(__DIR__.'/Lib.php');

CheckTourSession(true);
checkACL(AclISKServer, AclReadWrite);

if(!($QrCode=getSetupGlobalQrCode($_SESSION['TourId']))) {
	// no QR Code go and set things!
	CD_redirect('./QRcodes.php');
}

// Include the main TCPDF library (search for installation path).
require_once('Common/pdf/ResultPDF.inc.php');

// create new PDF document
$pdf = new ResultPDF('QrCode');//TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set style for barcode
$style = array(
		'border' => 2,
		'vpadding' => 'auto',
		'hpadding' => 'auto',
		'fgcolor' => array(0,0,0),
		'bgcolor' => false, //array(255,255,255)
		'module_width' => 1, // width of a single module in points
		'module_height' => 1 // height of a single module in points
);

$Code=json_encode($QrCode);

$Y=35;
$VBlock=($pdf->getPageHeight()-$Y-30);
$Size=min(110, $VBlock-12);
$Size = $Size + 4/2;
$Size = $Size - ($Size % 4);
$X=($pdf->getPageWidth()-$Size)/2;

$ActY=$Y ;
$pdf->SetFontSize(12);

$pdf->SetY($ActY-6);
$pdf->SetFont('', 'B', 20);
$pdf->Cell(0, 0, get_text('ISK-SETUP-'.$_SESSION['UseApi'],'Api'), 0, 1, 'C');
$pdf->SetFont('', '', 10);
$pdf->write2DBarcode($Code, 'QRCODE,L', $X, $ActY+12, $Size, $Size, $style, 'N');
$ActY+= $VBlock;
$pdf->Ln(10);

if($_SESSION['UseApi']==ISK_NG_LIVE_CODE) {
	$pdf->Cell(0, 0, get_text('ISK-SocketIP','Api') . ": " . $QrCode['socketIP'], 0, 1, 'L');
	$pdf->Cell(0, 0, get_text('ISK-SocketPort','Api') . ": " . $QrCode['socketPort'], 0, 1, 'L');
	if($QrCode['enableWIFIManagement']) {
		$pdf->Cell(0, 0, get_text('ISK-WifiReconnectTO','Api') . ": " . $QrCode['WifiReconnectTO'], 0, 1, 'L');
		$pdf->Cell(0, 0, get_text('ISK-WifiSearch','Api') . ": " . $QrCode['WifiSearch'], 0, 1, 'L');
		$pdf->Cell(0, 0, get_text('ISK-WifiResetCounter','Api') . ": " . $QrCode['WifiResetCounter'], 0, 1, 'L');
		$pdf->Cell(0, 0, get_text('ISK-enableWIFIManagement','Api'), 0, 1, 'L');
		for($i=0; $i<count($QrCode['WifiSSID']); $i++) {
			$pdf->setX($pdf->getX()+5);
			$pdf->Cell(0, 0, get_text('ISK-WifiSSID','Api') . ": " . $QrCode['WifiSSID'][$i] . (empty($QrCode['WifiTgtT'][$i]) ? '' : ' ('.get_text('ISK-WifiTargetRange','Api'). ' ' .$QrCode['WifiTgtF'][$i].'-'.$QrCode['WifiTgtT'][$i].')'), 0, 1, 'L');
		}
		if($QrCode['WifiDELETE']) {
			$pdf->setX($pdf->getX()+5);
			$pdf->Cell(0, 0, get_text('ISK-WifiDELETE','Api'), 0, 1, 'L');
		}
	}
	if($QrCode['enableGPS']) {
	    $pdf->Cell(0, 0, get_text('ISK-enableGPS', 'Api'), 0, 1, 'L');
	    $pdf->setX($pdf->getX()+5);
	    $pdf->Cell(0, 0, get_text('ISK-gpsFrequency','Api') . ": " . $QrCode['gpsFrequency'], 0, 1, 'L');

	}
	if($QrCode['spottingMode']) {
	    $pdf->Cell(0, 0, get_text('ISK-spottingMode','Api'), 0, 1, 'L');
	}
    if($QrCode['hideTotals']) {
        $pdf->Cell(0, 0, get_text('ISK-hideTotals','Api'), 0, 1, 'L');
    }
	if($QrCode['askTotals']) {
	    $pdf->Cell(0, 0, get_text('ISK-askTotals','Api'), 0, 1, 'L');
	}
    if($QrCode['enableHaptics']) {
        $pdf->Cell(0, 0, get_text('ISK-enableHaptics','Api'), 0, 1, 'L');
    }
	if($QrCode['askSignature']) {
	    $pdf->Cell(0, 0, get_text('ISK-askSignature','Api') . ': ' . get_text('AskSignature-'.$QrCode['askSignature'],'Api'), 0, 1, 'L');
	}
    if($QrCode['settingsPinCode']) {
        $pdf->Cell(0, 0, get_text('ISK-SettingsPIN','Api') . ': ' . $QrCode['settingsPinCode'], 0, 1, 'L');
    }
} else {
	$pdf->Cell(0, 0, get_text('ISK-ServerUrl','Api') . ": " . $QrCode['u'], 0, 1, 'L');
	$pdf->Cell(0, 0, get_text('TourCode','Tournament') . ": " . $_SESSION['TourCode'], 0, 1, 'L');
}



// -------------------------------------------------------------------

//Close and output PDF document
$pdf->Output('QrCode.pdf', 'I');
