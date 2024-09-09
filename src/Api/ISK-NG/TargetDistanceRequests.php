<?php

require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/CommonLib.php');

CheckTourSession(true);
if(!($_SESSION["UseApi"] == ISK_NG_LITE_CODE)) {
    CD_redirect($CFG->ROOT_DIR);
}
checkACL(AclISKServer, AclReadWrite);

$PAGE_TITLE=get_text('TargetScoring-Printout', 'Api');

if($_REQUEST['Sessions']??'') {
    require_once('Common/pdf/ScorePDF.inc.php');
    require_once('DrawQRCode.php');

    $PageWidth=floatval($_REQUEST['PageWidth']);
    $PageHeight=floatval($_REQUEST['PageHeight']);
    $QrCodeBlock=max(25, $PageWidth/min(intval($_REQUEST["NumDistances"]),4));

    $pdf=new TCPDF('P', 'mm', [$PageWidth, $PageHeight]);

    error_reporting(E_ALL);
    $pdf->setFontSize(min(10, $QrCodeBlock/3));
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->setMargins(0,0,0, true);
    $pdf->setFooterMargin(0);
    $pdf->setAutoPageBreak(false);
    $pdf->setCellPadding(0);

    $XCount=intval($pdf->getPageWidth()/($QrCodeBlock));
    $XGutter=($pdf->getPageWidth()-($QrCodeBlock*$XCount))/($XCount*2);

    // adding space to print the label
    $CellHeight=$pdf->getCellHeight($pdf->getFontSize(), false)+1;
    $YCount=intval($pdf->getPageHeight()/(35+$CellHeight));
    $YGutter=($pdf->getPageHeight()-((35+$CellHeight)*$YCount))/($YCount*2);


    $SessionQrCodes=[];
    foreach($_REQUEST['Sessions'] as $Session => $dummy) {
        if(empty(trim($_REQUEST['Targets'][$Session]))) {
            continue;
        }
        if(empty($_REQUEST['Distances'][$Session])) {
            continue;
        }
        $SessionQrCodes[$Session] = array();

        $Ranges=explode(',', trim($_REQUEST['Targets'][$Session]));
        foreach($Ranges as $Range) {
            $Targets=explode('-', trim($Range));
            if(count($Targets)==1) {
                foreach($_REQUEST['Distances'][$Session] as $d => $dummy) {
                    $SessionQrCodes[$Session][] = [
                        's' => $Session,
                        't' => intval($Targets[0]),
                        'd' => intval($d),
                    ];
                }
            } else {
                if(empty($Targets[1])) {
                    // up to the end!
                    $Targets[1]=$_REQUEST['MaxTarget'][$Session];
                }
                foreach(range($Targets[0], $Targets[1]) as $Target) {
                    foreach($_REQUEST['Distances'][$Session] as $d => $dummy) {
                        $SessionQrCodes[$Session][] = [
                            's' => $Session,
                            't' => intval($Target),
                            'd' => intval($d),
                        ];
                    }
                }
            }
        }
    }

    foreach ($SessionQrCodes as $Session => $QrCodes) {
        $X = 0;
        $Y = 0;
        foreach ($QrCodes as $QrCode) {
            if ($X == 0 and $Y == 0) {
                $pdf->AddPage();
                // draw the "cut" lines
                for ($i = 1; $i < $XCount; $i++) {
                    $LineX = ($QrCodeBlock + 2 * $XGutter) * $i;
                    $pdf->Line($LineX, 0, $LineX, $PageHeight, ['dash' => "2", 'color' => [200]]);
                }
                for ($i = 1; $i < $YCount; $i++) {
                    $LineY = (35 + $CellHeight + 2 * $YGutter) * $i;
                    $pdf->Line(0, $LineY, $PageWidth, $LineY, ['dash' => "2", 'color' => [200]]);
                }
                $pdf->setLineStyle(['dash' => "0", 'color' => [0]]);
            }

            $DrawX = ($QrCodeBlock-25)/2 + $XGutter + (2 * $XGutter + $QrCodeBlock) * $X;
            $DrawY = $YGutter + (2 * $YGutter + 35 + $CellHeight) * $Y;
            DrawQRCode_ISK_Lite($pdf, $DrawX, $DrawY+5, $QrCode['s'], $QrCode['d'], $QrCode['t']);
            $pdf->setXY($DrawX-($QrCodeBlock-25)/2, $DrawY + 32);
            $pdf->Cell($QrCodeBlock, $CellHeight,  get_text('Session') . ' ' . $QrCode['s'] . ' - ' . get_text('IskTargetTitle', 'Api', $QrCode['t']) . ', ' . get_text('DistanceNum', 'Api', $QrCode['d']), 0, 0, 'C', '', '', 1);
            $X++;
            if ($X == $XCount) {
                $X = 0;
                $Y++;
                if ($Y == $YCount) {
                    $Y = 0;
                }
            }
        }
    }
    $pdf->Output();
    die();
}

$IncludeJquery = true;
$IncludeFA = true;

$JS_SCRIPT=array(
    '<link href="./isk.css" rel="stylesheet" type="text/css">',
);

$PageWidth=($_SESSION['ToPaper'] ? '216' : '210');
$PageHeight=($_SESSION['ToPaper'] ? '279' : '297');
// prints at least 12 QrCodes per page with at least 5mm border
$QrCodeWidth=intval(min($PageWidth/3, ($PageHeight-24)/4))-10;

include('Common/Templates/head.php');

echo '<form method="post" target="TargetRequests">';
echo '<table class="Tabella w-50 mb-3">';
echo '<tr><th class="Title" colspan="3">' . $PAGE_TITLE . '</th></tr>';
echo '<tr>
    <th>'.get_text('StickersPageWidth', 'Tournament').'</th>
    <td colspan="2"><input type="number" class="w-100" name="PageWidth" value="'.$PageWidth.'"></td>
    </tr>';
echo '<tr>
    <th>'.get_text('StickersPageHeight', 'Tournament').'</th>
    <td colspan="2"><input type="number" class="w-100" name="PageHeight" value="'.$PageHeight.'"></td>
    </tr>';
echo '<tr>
    <th>'.get_text('Session').'</th>
    <th>'.get_text('API-Targets', 'Api').'</th>
    <th>'.get_text('Distance', 'Tournament').'</th>
    </tr>';

$q = safe_r_SQL("SELECT SesOrder, SesFirstTarget, SesTar4Session, SesName, ToNumDist FROM `Session` INNER JOIN Tournament ON SesTournament=ToId WHERE SesTournament={$_SESSION['TourId']} AND SesType='Q' ORDER BY SesOrder ASC");
while($r = safe_fetch($q)) {
    echo '<tr>
        <td class="Center">
        <input type="hidden" name="MaxTarget['.$r->SesOrder.']" value="'.($r->SesFirstTarget + $r->SesTar4Session-1).'">
        <input type="hidden" name="NumDistances" value="'.($r->ToNumDist).'">
        <input type="checkbox" name="Sessions['.$r->SesOrder.']">'.$r->SesOrder . (!empty($r->SesName) ?? ' - ' . $r->SesName).'</td>
        <td><input type="text" class="w-100" name="Targets['.$r->SesOrder.']" value="'.$r->SesFirstTarget . '-' . ($r->SesFirstTarget + $r->SesTar4Session-1).'"></td>
        <td>';
    for($i=1;$i<=$r->ToNumDist; $i++) {
        echo '<input type="checkbox" name="Distances['.$r->SesOrder.']['.$i.']">'.$i;
    }
    echo '</td></tr>';
}
echo '<tr><td class="Center" colspan="3"><input type="submit"></td></tr>';
echo '<tr><th class="Header" colspan="3">' . get_text('TargetScoring-Printout', 'Help') . '</th></tr>';
echo '</table>';
echo '</form>';
include('Common/Templates/tail.php');
