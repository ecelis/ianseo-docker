<?php

require_once(dirname(dirname(dirname(__DIR__))) . '/config.php');
require_once('Common/pdf/ScorePDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Lib/ScorecardsLib.php');
checkACL(AclQualification, AclReadOnly);

if($_REQUEST['Marmot']??'') {
    require_once(__DIR__.'/Marmot.php');
    die;
}
// switch to decide which scorecard type to print $_REQUEST['TourField3D']=$_SESSION['TourField3D'];

$Session=intval($_REQUEST['x_Session']);
$_REQUEST['ScoreDist']=array(1);

$pdf=CreateBeursaultScorecard($Session, $_REQUEST['x_From'], $_REQUEST['x_To'], $_REQUEST);
$pdf->output();

function CreateBeursaultScorecard($Session, $FromTgt=1, $ToTgt=999, $Options=array(), $SaveDir='', $File='') {
    if($Session=='ONLINE') {
        $FromTgt=1;
        $ToTgt=999;
        $Options['ScoreDraw'] = "Complete";
        $Options['ScoreHeader'] = "1";
        $Options['ScoreLogos'] = "1";
        $Options['ScoreFlags'] = "1";
        $Options['ScoreBarcode'] = "1";
        $Options['PersonalScore'] = "1";
        $Options['ScoreFilled'] = "1";
        $Options['ScoreDist'] = [1];
    }

    $ScoreDraw=($Options["ScoreDraw"]??'');
    $FillWithArrows = !empty($Options["ScoreFilled"]);

    $pdf = new ScorePDF(true);
    $pdf->setMargins(20, 15, 20, true);
    //error_reporting(E_ALL);
    if(!empty($Options["QRCode"])) {
        $pdf->QRCode=$Options["QRCode"];
        $pdf->BottomImage=false;
    }
    $pdf->FillWithArrows=$FillWithArrows;
    if(empty($Options["ScoreHeader"])) {
        $pdf->HideHeader();
    }
    if(empty($Options["ScoreLogos"])) {
        $pdf->HideLogo();
    }
    if(empty($Options["ScoreFlags"])) {
        $pdf->HideFlags();
    }
    if(!empty($Options["ScoreBarcode"])) {
        $pdf->PrintBarcode=true;
    }

    if($Options['noEmpty']??'') {
        $SQL="select QuD1Hits as Hits, QuD1Score as Score, QuD1Gold as Gold, QuD1Xnine as XNine, QuD1Arrowstring as Arrowstring, QuTarget as Target, QuLetter as Letter, QuSession as Session,
                EnFirstName, EnName, EnCode, DivId, DivDescription, ClId, ClDescription, EvCode, EvEventName, 
                CoCode, CoName,
                ToVenue
            from Qualifications
            inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']}
            inner join Countries on CoId=EnCountry
            inner join Tournament on ToId=EnTournament
            inner join Divisions on DivTournament=EnTournament and DivId=EnDivision
            inner join Classes on ClTournament=EnTournament and ClId=EnClass
            inner join Individuals on IndId=EnId and IndTournament=EnTournament
            inner join Events on EvCode=IndEvent and EvTournament=EnTournament and EvTeamEvent=0
            order by QuSession, QuTargetNo";
    } else {
        $SQL="select coalesce(QuD1Hits,0) as Hits, coalesce(QuD1Score,0) as Score, coalesce(QuD1Gold,0) as Gold, coalesce(QuD1Xnine,0) as XNine, coalesce(QuD1Arrowstring,'') as Arrowstring, AtTarget as Target, AtLetter as Letter, AtSession as Session,
                EnFirstName, EnName, EnCode, DivId, DivDescription, ClId, ClDescription, EvCode, EvEventName, 
                CoCode, CoName,
                ToVenue
            from AvailableTarget
            inner join Tournament on ToId=AtTournament
            left join (
                select QuD1Hits, QuD1Score, QuD1Gold, QuD1Xnine, QuD1Arrowstring, QuTarget, QuLetter, QuSession,
                        EnFirstName, EnName, EnCode, DivId, DivDescription, ClId, ClDescription, EvCode, EvEventName,
                        CoCode, CoName
                    from Qualifications
                    inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']}
                    inner join Countries on CoId=EnCountry
                    inner join Divisions on DivTournament=EnTournament and DivId=EnDivision
                    inner join Classes on ClTournament=EnTournament and ClId=EnClass
                    inner join Individuals on IndId=EnId and IndTournament=EnTournament
                    inner join Events on EvCode=IndEvent and EvTournament=EnTournament and EvTeamEvent=0
                ) Archers on QuTarget=AtTarget and QuLetter=AtLetter and QuSession=AtSession
            where AtTournament= {$_SESSION['TourId']} and AtTarget between $FromTgt and $ToTgt ".($Session=='ONLINE' ? '' : "and AtSession=$Session");
    }

    $q=safe_r_sql($SQL);



    $tmp=$pdf->getMargins();
    $TopX=$tmp['left'];
    $TopY=$tmp['top'];
    $Width=$pdf->getPageWidth()-2*$TopX;
    $Height=$pdf->getPageHeight()-2*$TopY;
    $TmpLeft=0;
    $TmpRight=0;

    if($pdf->BottomImage and file_exists($IM=$pdf->ToPaths['ToBottom'])) {
        $Height-=7.5;
    }

    $LeftColumn=110;
    $LeftOffset=$LeftColumn+10+$TopX;
    $ScoreWidth=$pdf->getPageWidth()-$TopX-$LeftOffset;

    $ArrowEnds = getArrowEnds($Session);
    $MaxScore=($_SESSION['TourLocSubRule']=='SetFrBouquet' ? 3 : 4);
    $ScoreCell=$ScoreWidth/($MaxScore+2);
    $CellHeight=($Height-6-($pdf->PrintBarcode?7:0))/($ArrowEnds[1]['ends']+4+($pdf->PrintBarcode?1:0));
    $LogoHeight=15;

    ini_set('error_reporting', E_ALL);

    $VGap=min(10, ($Height-55+$TopY-142)/10);

    while($r=safe_fetch($q)) {
        if(empty($Options["ScoreFilled"])) {
            $r->Arrowstring='';
            $r->Hits='';
            $r->Score='';
        } else {
            $r->Arrowstring=DecodeFromString($r->Arrowstring, false, true);
            $Misses=array_count_values($r->Arrowstring);
        }

        $pdf->setLeftMargin(20);
        $pdf->AddPage('','',true);

        // print headers
        if($pdf->PrintLogo) {
            if(file_exists($IM=$pdf->ToPaths['ToLeft']) ) {
                $im=getimagesize($IM);
                $pdf->Image($IM, $TopX, $TopY, 0, $LogoHeight);
                $TmpLeft = (1 + ($im[0] * $LogoHeight / $im[1]));
            }
            if(file_exists($IM=$pdf->ToPaths['ToRight']) ) {
                $im=getimagesize($IM);
                $TmpRight = ($im[0] * $LogoHeight / $im[1]);
                $pdf->Image($IM, $TopX+$LeftColumn-$TmpRight, $TopY, 0, $LogoHeight);
                $TmpRight++;
            }
            //IMMAGINE DEGLI SPONSOR
            // Sponsors disabled if QRCodes are to be printed!!!
            if($pdf->BottomImage and file_exists($IM=$pdf->ToPaths['ToBottom'])) {
                $BottomImage=7.5;
                $im=getimagesize($IM);
                $imgW = $Width;
                $imgH = $imgW * $im[1] / $im[0] ;
                if($imgH > $BottomImage) {
                    $imgH = $BottomImage;
                    $imgW = $imgH * $im[0] / $im[1] ;
                }
                $pdf->Image($IM, ($TopX+($Width-$imgW)/2), $pdf->getPageHeight()-10-$imgH, $imgW, $imgH);
            }
        }

        if($pdf->PrintHeader) {
            $pdf->setCellPadding(0);
            $pdf->SetColors(true);
            $pdf->SetFont($pdf->FontStd,'B',12);
            $pdf->SetXY($TopX+$TmpLeft,$TopY);
            $pdf->Cell($LeftColumn-$TmpLeft-$TmpRight, $LogoHeight/3, $pdf->Name, 0, 1, 'L', 0, 0, 1,false,'T','T');
            $pdf->SetFont($pdf->FontStd,'B',10);
            $pdf->SetX($TopX+$TmpLeft);
            $pdf->Cell($LeftColumn-$TmpLeft-$TmpRight, $LogoHeight/3, $pdf->Where, 0, 1,'L', 0, 0, 1);
            $pdf->SetFont($pdf->FontStd,'',10);
            $pdf->SetX($TopX+$TmpLeft);
            $pdf->Cell($LeftColumn-$TmpLeft-$TmpRight, $LogoHeight/3, TournamentDate2String($pdf->WhenF,$pdf->WhenT), 0, 1, 'L', 0, 1, 1, false, 'T', 'B');
        }

        $pdf->setCellPadding(2);

        // Target
        $pdf->setY(55);
        $pdf->setFont('','b','12');
        $pdf->Cell($LeftColumn,5, 'Cible n° '.$r->Target.' '.$r->Letter, '',1,'C');

        // Athlete etc
        $pdf->dY($VGap);
        $pdf->setFont('','b','14');
        $pdf->Cell($LeftColumn,5, $r->EnFirstName ? $r->EnFirstName.' '.$r->EnName : 'Archer: '.str_repeat('_', 33), '',1, 'L', '', '',1);

        // Athlete etc
        $pdf->dY($VGap);
        $pdf->setFont('','b','14');
        $pdf->Cell($LeftColumn,5, $r->CoCode ? $r->CoCode.' '.$r->CoName : 'Compagnie: '.str_repeat('_', 30), '',1, 'L', '', '',1);

        // EnCode etc
        $pdf->setFont('','','10');
        $pdf->dY($VGap);
        $pdf->setCellPadding(0);
        $pdf->Cell(50,4, 'N° Licence '.$r->EnCode, '',0);
        $pdf->Cell(25,4, 'Départ n° '.$r->Session, '',0);
        $pdf->Cell($LeftColumn-75,4, 'Tir n° ', '',1);
        // Category
        $pdf->Cell(50,4, 'Arme: ');
        $pdf->Cell($LeftColumn-50,4, $r->DivDescription??'', '',1);
        $pdf->Cell(50,4, 'Catégorie:');
        $pdf->Cell($LeftColumn-50,4, $r->ClDescription??'', '',1);
        $pdf->Cell(50,4, 'Epreuve:');
        $pdf->Cell($LeftColumn-50,4, $r->EvEventName??'', '',1);
        $pdf->setCellPadding(2);


        $pdf->dY($VGap);
        $pdf->setFont('','b','10');
        $pdf->RoundedRect($pdf->GetX(),$pdf->GetY(),$LeftColumn,10,2);
        $pdf->Cell($LeftColumn,10, 'JEUX D\'ARC DE '.$r->ToVenue, '',1);

        $pdf->dY($VGap);
        $pdf->RoundedRect($pdf->GetX(),$pdf->GetY(),$LeftColumn,10,2);
        $pdf->Cell($LeftColumn,10, 'HEURE DE TIR', '',1);

        $pdf->dY($VGap);
        $pdf->RoundedRect($pdf->GetX(),$pdf->GetY(),$LeftColumn,30,2);
        $pdf->MultiCell($LeftColumn, 30, "1) Cette feuille de marque doit être:\n  a) remplie au stylo à bille\n  b) signée par l'archer et le marqueur\n\n2) Toute rature devra être contresignée par l'arbitre", '',1);

        $pdf->dY($VGap);
        $OrgY=$pdf->GetY();
        $pdf->RoundedRect($pdf->GetX(),$pdf->GetY(),$LeftColumn,30,2);
        $pdf->Cell($LeftColumn, 0, "SIGNATURES", '',1, 'C');
        $pdf->dY(-5);
        $pdf->setFont('','','10');
        $pdf->Cell($LeftColumn/2, 0, "ARCHER:", '',0, 'L');
        $pdf->Cell($LeftColumn/2, 0, "MARQUEUR:", '',0, 'R');

        $pdf->setY($OrgY+40);

        $pdf->setFont('','b','10');
        $pdf->RoundedRect($pdf->GetX(),$pdf->GetY(),$LeftColumn,10,2);
        $pdf->Cell($LeftColumn-15,10, 'HONNEURS:', '',0);
        $pdf->Cell(15,10, ($FillWithArrows and $r->EnFirstName) ? ($r->Hits-($Misses['M']??0)):'', '',1, 'R');

        $pdf->dY($VGap);
        $pdf->RoundedRect($pdf->GetX(),$pdf->GetY(),$LeftColumn,10,2);
        $pdf->Cell($LeftColumn-15,10, 'POINTS:', '',0);
        $pdf->Cell(15,10, $r->EnFirstName?$r->Score:'', '',1, 'R');

        // draw the actual scorecard!
        $pdf->setLeftMargin($LeftOffset);
        $pdf->setXY($LeftOffset, $TopY);
        $pdf->setCellPadding(0.5);

        $pdf->setFont('','b','9');
        $pdf->Cell($ScoreCell, $CellHeight, 'FL', '1', 0, 'C', '1');
        foreach(range(0, $MaxScore) as $ar) {
            $pdf->Cell($ScoreCell, $CellHeight, $ar, '1', 0, 'C', '1');
        }
        $pdf->ln();
        $Hons=[];
        foreach(range(1, $ArrowEnds[1]['ends']) as $end) {
            $pdf->Cell($ScoreCell, $CellHeight, $end, '1', 0, 'C', '1');
            foreach(range(0, $MaxScore) as $ar) {
                $txt='';
                if($FillWithArrows) {
                    $tmp=($r->Arrowstring[$end-1]??'');
                    if($tmp==$ar or ($tmp=='M' and $ar==0)) {
                        $txt=$tmp;
                        $Hons[$ar]=($Hons[$ar]??0)+1;
                    }
                }
                $pdf->Cell($ScoreCell, $CellHeight, $txt, '1', 0, 'C', '');
            }
            $pdf->ln();
        }

        $pdf->dy(2);
        $pdf->Cell($ScoreCell, $CellHeight, 'HON', '1', 0, 'C', '1');
        foreach(range(0, $MaxScore) as $ar) {
            $pdf->Cell($ScoreCell, $CellHeight, $Hons[$ar]??'', '1', 0, 'C', '');
        }
        $pdf->ln();

        $pdf->dy(2);
        $pdf->setFont('','', 8);
        $pdf->Cell($ScoreCell, $CellHeight, '', '1', 0, 'C', '');
        foreach(range(0, $MaxScore) as $ar) {
            $pdf->Cell($ScoreCell, $CellHeight, $ar ? 'x '.$ar : '', '1', 0, 'C', '');
        }
        $pdf->ln();

        $pdf->dy(2);
        $pdf->setFont('','b', 9);
        $pdf->Cell($ScoreCell, $CellHeight, 'Pts.', '1', 0, 'C', '');
        foreach(range(0, $MaxScore) as $ar) {
            $pdf->Cell($ScoreCell, $CellHeight, $ar ? ($Hons[$ar]??'' ? $Hons[$ar]*$ar : '') : '', '1', 0, 'C', '');
        }
        $pdf->ln();
        
        // Barcode ?
        if($pdf->PrintBarcode and !empty($r->EnCode)) {
            $pdf->setCellPadding(0);
            $pdf->SetColors(true);
            $pdf->SetFont('barcode','',22);
            if($r->EnCode[0]=='_') $r->EnCode='UU'.substr($r->EnCode, 1);
            $pdf->Cell(0, $CellHeight+3, mb_convert_encoding('*' . $r->EnCode.'-'.$r->DivId.'-'.$r->ClId . '-1', "UTF-8","cp1252") . "*",0,0,'C',0);
            $pdf->SetFont($pdf->FontStd,'',7);
            $pdf->ln();
            $pdf->Cell(0, 3, mb_convert_encoding($r->EnCode.'-'.$r->DivId.'-'.$r->ClId . '-1', "UTF-8","cp1252"),0,0,'C',0, '', 1, false, 'T', 'T');
            $pdf->SetColors(false);
            $pdf->setCellPadding(2);
        }

    }
    return $pdf;
}
