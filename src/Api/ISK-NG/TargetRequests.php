<?php

require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/CommonLib.php');

CheckTourSession(true);
if(!($_SESSION["UseApi"] == ISK_NG_PRO_CODE or ($_SESSION["UseApi"] == ISK_NG_LIVE_CODE AND module_exists('ISK-NG_Live')))) {
    CD_redirect($CFG->ROOT_DIR);
}
checkACL(AclISKServer, AclReadWrite);

$PAGE_TITLE=get_text('TargetRequests-Printout', 'Api');

// check the max target range based on the info
$SQL=[];
$SQL[]="select max(AtTarget) as MaxTarget from AvailableTarget where AtTournament={$_SESSION['TourId']}";
$SQL[]="select max(FSTarget) as MaxTarget  from FinSchedule where FsTournament={$_SESSION['TourId']}";
$SQL[]="select max(ElTargetNo) as MaxTarget  from Eliminations where ElTournament={$_SESSION['TourId']}";
$SQL[]="select max(RrMatchTarget) as MaxTarget  from RoundRobinMatches where RrMatchTournament={$_SESSION['TourId']}";

$MaxTarget=25;
$q=safe_r_sql("(".implode(") UNION (", $SQL).") order by MaxTarget desc");
if ($r=safe_fetch($q)) {
    $MaxTarget=$r->MaxTarget;
}

if($_REQUEST['Groups']??'') {
    $PageWidth=floatval($_REQUEST['PageWidth']);
    $PageHeight=floatval($_REQUEST['PageHeight']);
    $QrCodeBlock=min(floatval($_REQUEST['QrCodeWidth']), $PageWidth, $PageHeight);

    require_once('Common/Lib/ScorecardsLib.php');
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
    $YCount=intval($pdf->getPageHeight()/($QrCodeBlock+$CellHeight));
    $YGutter=($pdf->getPageHeight()-(($QrCodeBlock+$CellHeight)*$YCount))/($YCount*2);


    $QrCodes=[];
    foreach($_REQUEST['Groups'] as $Group => $dummy) {
        if(empty(trim($_REQUEST['Targets'][$Group]))) {
            continue;
        }

        $Ranges=explode(',', trim($_REQUEST['Targets'][$Group]));
        foreach($Ranges as $Range) {
            $Targets=explode('-', trim($Range));
            if(count($Targets)==1) {
                $QrCodes[]=[
                    'g'=>$Group,
                    't'=>intval($Targets[0])
                ];
            } else {
                if(empty($Targets[1])) {
                    // up to the end!
                    $Targets[1]=$MaxTarget;
                }
                foreach(range($Targets[0], $Targets[1]) as $Target) {
                    $QrCodes[]=[
                        'g'=>$Group,
                        't'=>intval($Target)
                    ];
                }
            }
        }
    }

    $X=0;
    $Y=0;
    foreach($QrCodes as $QrCode) {
        if($X==0 and $Y==0) {
            $pdf->AddPage();
            // draw the "cut" lines
            for($i=1; $i<$XCount; $i++) {
                $LineX=($QrCodeBlock+2*$XGutter)*$i;
                $pdf->Line($LineX, 0, $LineX, $PageHeight, ['dash'=>"2", 'color'=>[200]]);
            }
            for($i=1; $i<$YCount; $i++) {
                $LineY=($QrCodeBlock+$CellHeight+2*$YGutter)*$i;
                $pdf->Line(0, $LineY, $PageWidth, $LineY, ['dash'=>"2", 'color'=>[200]]);
            }
            $pdf->setLineStyle( ['dash'=>"0", 'color'=>[0]]);
        }

        $DrawX=$XGutter+(2*$XGutter+$QrCodeBlock)*$X;
        $DrawY=$YGutter+(2*$YGutter+$QrCodeBlock+$CellHeight)*$Y;
        DrawScoreQrPersonal($pdf, $QrCode['t'], $DrawX, $DrawY, $QrCode['g'], $QrCodeBlock, 0);
        $pdf->setXY($DrawX, $DrawY+$QrCodeBlock);
        $pdf->Cell($QrCodeBlock, $CellHeight, get_text('Group#', 'Tournament', $QrCode['g']).' - '.get_text('IskTargetTitle', 'Api', $QrCode['t']), 0,0,'C', '', '', 1);
        $X++;
        if($X==$XCount) {
            $X=0;
            $Y++;
            if($Y==$YCount) {
                $Y=0;
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
echo '<tr><th class="Title" colspan="2">' . $PAGE_TITLE . '</th></tr>';
echo '<tr>
    <th>'.get_text('StickersPageWidth', 'Tournament').'</th>
    <td><input type="number" class="w-100" name="PageWidth" value="'.$PageWidth.'"></td>
    </tr>';
echo '<tr>
    <th>'.get_text('StickersPageHeight', 'Tournament').'</th>
    <td><input type="number" class="w-100" name="PageHeight" value="'.$PageHeight.'"></td>
    </tr>';
echo '<tr>
    <th>'.get_text('QrCodeWidth', 'Api').'</th>
    <td><input type="number" class="w-100" name="QrCodeWidth" value="'.$QrCodeWidth.'"></td>
    </tr>';
echo '<tr>
    <th>'.get_text('API-Group', 'Api').'</th>
    <th>'.get_text('API-Targets', 'Api').'</th>
    </tr>';
foreach(range(0,25) as $i) {
    echo '<tr>
        <td class="Center">
        <input type="checkbox" name="Groups['.$i.']">'.chr(65+$i).'</td>
        <td><input type="text" class="w-100" name="Targets['.$i.']" value="1-'.$MaxTarget.'"></td>
        </tr>';
}
echo '<tr><td class="Center" colspan="2"><input type="submit"></td></tr>';
echo '<tr><th class="Header" colspan="2">' . get_text('TargetRequests-Printout', 'Help') . '</th></tr>';
echo '</table>';
echo '</form>';
include('Common/Templates/tail.php');
