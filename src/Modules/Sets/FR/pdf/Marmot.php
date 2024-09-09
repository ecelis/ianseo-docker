<?php

require_once(dirname(dirname(dirname(__DIR__))) . '/config.php');
require_once('Common/pdf/ScorePDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Lib/ScorecardsLib.php');
checkACL(AclQualification, AclReadOnly);

$pdf=new ScorePDF('P');

$pdf->AddPage();

$CX=$pdf->getPageWidth()/2;
$pdf->setFillColor(0);
$pdf->setDrawColor(0);

// PAY ATTENTION: path is drawn on the middle of the border, so we need to add half border width!
$pdf->Rect($CX-78, $CX-78, 156,156, '',['all' => ['width'=>0.2]]);
$pdf->Circle($CX, $CX, 66.5,0, 360,'', ['width'=>8]);
$pdf->Circle($CX, $CX, 50.5,0, 360,'', ['width'=>1]);
$pdf->Circle($CX, $CX, 40.5,0, 360,'', ['width'=>1]);
$pdf->Circle($CX, $CX, 28,0, 360,'F', '', [0]);

$pdf->PolyLine([$CX, $CX-25, $CX+5, $CX, $CX-5, $CX, $CX, $CX-25, $CX, $CX], '', ['all'=>['width'=>0.2,'color'=>[255]]]);
$pdf->PolyLine([$CX, $CX+25, $CX+5, $CX, $CX-5, $CX, $CX, $CX+25, $CX, $CX], '', ['all'=>['width'=>0.2,'color'=>[255]]]);
$pdf->PolyLine([$CX-25, $CX, $CX, $CX+5, $CX, $CX-5, $CX-25, $CX, $CX, $CX], '', ['all'=>['width'=>0.2,'color'=>[255]]]);
$pdf->PolyLine([$CX+25, $CX, $CX, $CX+5, $CX, $CX-5, $CX+25, $CX, $CX, $CX], '', ['all'=>['width'=>0.2,'color'=>[255]]]);
$pdf->StartTransform();
$pdf->Rotate(45, $CX, $CX);
$pdf->PolyLine([$CX, $CX-25, $CX+5, $CX, $CX-5, $CX, $CX, $CX-25, $CX, $CX], '', ['all'=>['width'=>0.2,'color'=>[255]]]);
$pdf->PolyLine([$CX, $CX+25, $CX+5, $CX, $CX-5, $CX, $CX, $CX+25, $CX, $CX], '', ['all'=>['width'=>0.2,'color'=>[255]]]);
$pdf->PolyLine([$CX-25, $CX, $CX, $CX+5, $CX, $CX-5, $CX-25, $CX, $CX, $CX], '', ['all'=>['width'=>0.2,'color'=>[255]]]);
$pdf->PolyLine([$CX+25, $CX, $CX, $CX+5, $CX, $CX-5, $CX+25, $CX, $CX, $CX], '', ['all'=>['width'=>0.2,'color'=>[255]]]);
$pdf->Line($CX+25, $CX, $CX+110, $CX, ['width'=>0.2,'color'=>[0]]);
$pdf->Line($CX-25, $CX, $CX-110, $CX, ['width'=>0.2,'color'=>[0]]);
$pdf->Line($CX, $CX+25, $CX, $CX+110, ['width'=>0.2,'color'=>[0]]);
$pdf->Line($CX, $CX-25, $CX, $CX-110, ['width'=>0.2,'color'=>[0]]);
$pdf->setFontSize(18);
$pdf->setXY($CX-4,$CX-104);
$pdf->setFillColor(255);
$pdf->Cell(8,8,'A',0,1,'C',1);

$pdf->setXY($CX+96,$CX-4);
$pdf->Cell(8,8,'C',0,0,'C',1);
$pdf->setXY($CX-4,$CX+96);
$pdf->Cell(8,8,'B',0,0,'C',1);
$pdf->setXY($CX-104,$CX-4);
$pdf->Cell(8,8,'D',0,0,'C',1);

// Bouquet Provincial
$pdf->setFontSize(14);
$pdf->setXY($CX-15,$CX-90);
$pdf->Cell(30,0,'Bouquet',0,1,'C',1);
$pdf->setXY($CX-15,$CX-84);
$pdf->Cell(30,0,'Provincial',0,1,'C',1);

// Place and year
$q=safe_r_sql("select ToVenue, year(ToWhenFrom) as ToYear from Tournament where ToId={$_SESSION['TourId']}");
$r=safe_fetch($q);
$pdf->setFontSize(14);
$pdf->setXY($CX-15,$CX+78);
$pdf->Cell(30,0,$r->ToVenue,0,1,'C',1);
$pdf->setXY($CX-15,$CX+84);
$pdf->Cell(30,0,$r->ToYear,0,1,'C',1);
$pdf->StopTransform();

$pdf->Circle($CX, $CX, 10,0, 360,'B', ['width'=>0.2,'color'=>[255]], [0]);
$pdf->Circle($CX, $CX, 5,0, 360,'F', '', [255]);

$pdf->Line($CX+25, $CX, $CX+75, $CX, ['width'=>0.2,'color'=>[0]]);
$pdf->Line($CX-25, $CX, $CX-75, $CX, ['width'=>0.2,'color'=>[0]]);
$pdf->Line($CX, $CX+25, $CX, $CX+75, ['width'=>0.2,'color'=>[0]]);
$pdf->Line($CX, $CX-25, $CX, $CX-75, ['width'=>0.2,'color'=>[0]]);

$pdf->output();
