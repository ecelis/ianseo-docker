<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclParticipants, AclReadOnly);
require_once('Common/pdf/ResultPDF.inc.php');

if(!isset($isCompleteResultBook)) {
    $pdf = new ResultPDF((get_text('StatClasses', 'Tournament')), false);
}

$SesArray=array();
$tmpCntSession = array();
$Sql = "SELECT DISTINCT QuSession, IFNULL(SesName,'') as SessionName
	FROM Qualifications 
	INNER JOIN Entries ON QuId = EnId AND EnTournament = " . StrSafe_DB($_SESSION['TourId']) . " 
	LEFT JOIN Session on SesTournament=EnTournament and SesOrder=QuSession and SesType='Q'
	ORDER BY QuSession";
$q = safe_r_sql($Sql);
while($r = safe_fetch($q)) {
    $SesArray[$r->QuSession] = $r->SessionName;
    $tmpCntSession[$r->QuSession] = 0;
}
safe_free_result($q);

$data=array();
$divTotals=array();
$sesTotals=array();

$Sql = "SELECT EnDivision, QuSession, EnClass, count(*) as numArchers 
    FROM Entries 
    INNER JOIN Qualifications on EnId=QuId
    LEFT JOIN Divisions ON EnDivision=DivId AND DivTournament=EnTournament
    LEFT JOIN Classes ON EnClass=ClId AND ClTournament=EnTournament
    WHERE EnTournament=" . StrSafe_DB($_SESSION['TourId']) ." 
    GROUP BY DivViewOrder, ClViewOrder, QuSession, EnDivision, EnClass 
    ORDER BY DivViewOrder, ClViewOrder, QuSession, EnDivision, EnClass";
$q=safe_r_SQL($Sql);
while ($r = safe_fetch($q)) {
    if(!array_key_exists($r->EnClass, $data)) {
        $data[$r->EnClass] = array();
    }
    if(!array_key_exists($r->EnDivision, $divTotals)) {
        $divTotals[$r->EnDivision] = $tmpCntSession;
    }
    if(!array_key_exists($r->EnDivision, $data[$r->EnClass])) {
        $data[$r->EnClass][$r->EnDivision] = array();
    }
    $data[$r->EnClass][$r->EnDivision][$r->QuSession] = $r->numArchers;
    $divTotals[$r->EnDivision][$r->QuSession] += $r->numArchers;
}
safe_free_result($q);

//Dettaglio per Classe/Divisione/Turno
$WCode=15;
$tmpCnt=0;
foreach($divTotals as $k) {
    foreach($k as $v) {
        if($v) {
            $tmpCnt++;
        }
    }
}
$WCell=min(20,(($pdf->getPageWidth()-20-$WCode)/($tmpCnt+2)));
$pdf->SetFont($pdf->FontStd,'B',10);
$pdf->SetXY($pdf->GetX()+$WCode,$pdf->GetY()+5);
$pdf->Cell($WCell*($tmpCnt+2), 6,  (get_text('StatClasses','Tournament')), 1, 1, 'C', 1);
$pdf->SetX($pdf->GetX()+$WCode);
foreach($divTotals as $kDiv=>$tDiv) {
    $YORG = $pdf->GetY();
    $XORG = $pdf->GetX();
    $tmpCnt=0;
    foreach($tDiv as $v) {
        if($v) {
            $tmpCnt++;
        }
    }
    $pdf->SetFont($pdf->FontStd,'B',10);
    $pdf->Cell($tmpCnt*$WCell, 6, $kDiv, 1, 1, 'C', 1);
    $pdf->SetFont($pdf->FontStd,'B',8);
    $pdf->setX($XORG);
    $sesValid[$kDiv]=array();
    foreach($tDiv as $k=>$v) {
        if($v) {
            $pdf->Cell($WCell, 4, $k, 1, 0, 'C', 1);
        }
    }
    $pdf->setXY($pdf->GetX(),$YORG);
}
$pdf->SetFont($pdf->FontStd,'B',10);
$pdf->Cell(2*$WCell, 10, get_text('TotalShort','Tournament'), 1, 1, 'C', 1);

foreach($data as $kCl=>$vCl) {
    $rowTot = 0;
    $pdf->SetFont($pdf->FontStd,'B',10);
    $pdf->Cell($WCode, 5, $kCl, 1, 0, 'C', 1);
    $pdf->SetFont($pdf->FontStd,'',8);
    foreach($divTotals as $kDiv=>$tDiv) {
        foreach($tDiv as $k=>$v) {
            if(array_key_exists($kDiv,$vCl) AND array_key_exists($k,$vCl[$kDiv]) AND $v!=0) {
                $pdf->Cell($WCell, 5, $vCl[$kDiv][$k], 1, 0, 'R', 0);
                $rowTot += $vCl[$kDiv][$k];
            } elseif($divTotals[$kDiv][$k]) {
                $pdf->Cell($WCell, 5, '', 1, 0, 'R', 0);
            }
        }
    }
    $pdf->SetFont($pdf->FontStd,'B',8);
    $pdf->Cell(2*$WCell, 5, $rowTot, 1, 1, 'R', 1);
}
$pdf->SetFont($pdf->FontStd,'B',1);
$pdf->Cell($WCode+$WCell*($tmpCnt+2), 0.5, '', 1, 1, 'C', 0);
$pdf->SetFont($pdf->FontStd,'B',10);
$pdf->Cell($WCode, 5, get_text('Total'), 1, 0, 'L', 1);
$pdf->SetFont($pdf->FontStd,'B',8);
$rowTot = 0;
foreach($divTotals as $kDiv=>$tDiv) {
    foreach($tDiv as $k=>$v) {
        if($v) {
            $pdf->Cell($WCell, 5, array_sum(array_column(array_column($data,$kDiv),$k)), 1, 0, 'R', 1);
            $rowTot += array_sum(array_column(array_column($data,$kDiv),$k));
        }
    }
}
$pdf->SetFont($pdf->FontStd,'B',10);
$pdf->Cell(2*$WCell, 5, $rowTot, 1, 1, 'R', 1);

//Totali per turni
$DivArray=array_keys($divTotals);
$TotArray=array();

$WCategory=70;
$totSize=min(20,($pdf->getPageWidth()-20-$WCode-$WCategory)/(count($DivArray)+1));
$YORG=$pdf->GetY()+5;
if(!$pdf->SamePage(11 + count($SesArray)*5)) {
    $pdf->AddPage();
    $YORG=$pdf->GetY()+5;
}
$pdf->SetFont($pdf->FontStd,'B',10);
$pdf->SetXY($pdf->GetX()+$WCode+$WCategory, $YORG);
foreach($DivArray as $vDiv) {
    $pdf->Cell($totSize, 6,  $vDiv, 1, 0, 'C', 1);
}
$pdf->Cell($totSize, 6, get_text('TotalShort','Tournament'), 1, 1, 'C', 1);
//Totali
$i=0;
foreach ($SesArray as $Ses=>$sName) {
    $pdf->SetFont($pdf->FontStd,'B',10);
    $pdf->Cell($WCode, 5, $Ses==0 ? '--' : $Ses, 1, 0, 'C', 1);
    $pdf->SetFont($pdf->FontStd,'',8);
    $pdf->Cell($WCategory, 5, $sName, 1, 0, 'L', 0);
    $pdf->SetFont($pdf->FontStd,'',8);
    $TmpCounter=0;
    foreach($DivArray as $vDiv) {
        $pdf->Cell($totSize, 5, $divTotals[$vDiv][$Ses] ?:'-', 1, 0, 'R', 0);
        $TmpCounter += $divTotals[$vDiv][$Ses];
    }
    $pdf->SetFont($pdf->FontStd,'B',8);
    $pdf->Cell($totSize, 5,  $TmpCounter, 1, 1, 'R', 0);
    $i++;
}
$pdf->SetFont($pdf->FontStd,'B',1);
$pdf->Cell($WCode+$WCategory+$totSize*(count($DivArray)+1), 0.5, '', 1, 1, 'C', 0);
$pdf->SetFont($pdf->FontStd,'B',10);
$pdf->Cell($WCode+$WCategory, 5, (get_text('Total')), 1, 0, 'L', 1);
$GrandTotal=0;
foreach($DivArray as $vDiv) {
    $pdf->SetFont($pdf->FontStd,'B',8);
    $TmpCounter = array_sum($divTotals[$vDiv]);
    $pdf->Cell($totSize, 5,  $TmpCounter, 1, 0, 'R', 1);
    $GrandTotal+=$TmpCounter;
}
$pdf->SetFont($pdf->FontStd,'B',10);
$pdf->Cell($totSize, 5,  $GrandTotal, 1, 1, 'R', 1);

//Totali per categorie e per turni
$Categories = [];
$q=safe_r_sql("select QuSession, concat(EnDivision, EnClass) as CatCode, concat_ws(' ', DivDescription, ClDescription) as Description, sum(EnWChair+EnDoubleSpace) as WheelChairs, count(*) as archers
    from Entries
    inner join Qualifications on QuId=EnId
    left join Divisions on DivId=EnDivision and DivTournament=EnTournament 
    left join Classes on ClId=EnClass and ClTournament=EnTournament 
    where EnTournament={$_SESSION['TourId']}
    group by QuSession, DivViewOrder, ClViewOrder, EnDivision, EnClass
    order by DivViewOrder, ClViewOrder, QuSession
    ");
while($r=safe_fetch($q)) {
    if(empty($Categories[$r->CatCode])) {
        $Categories[$r->CatCode]=[
            'desc'=>$r->Description,
            'nums'=>[]
        ];
    }
    $Categories[$r->CatCode]['nums'][$r->QuSession]=[$r->archers,$r->WheelChairs];
}
if($Categories) {
    $TotCats=0;
    foreach($Categories as $k=>$v) {
        $TotCats+=count($v);
    }
    $Wsession=min(20,($pdf->getPageWidth()-20-$WCode-$WCategory)/(count($SesArray)+1));

    $YORG=$pdf->GetY()+5;
    if(!$pdf->SamePage(4*count($Categories))+6) {
        // cannot print all categories in the bottom right square... go to a new page
        // 3 columns to put the data
        $pdf->AddPage();
        $YORG=$pdf->GetY()+5;
    }
    $pdf->SetY($YORG);

    $SesTot=[];
    $SesWheels=[];
    $pdf->SetFont($pdf->FontStd,'B',10);
    $pdf->Cell($WCode, 6, '', 0, 0, 'L', 0);
    $pdf->Cell($WCategory, 6,  get_text('Description'), 1, 0, 'L', 1);
    foreach($SesArray as $Ses=>$sName) {
        $SesTot[(int) $Ses]=0;
        $SesWheels[(int) $Ses]=0;
        $pdf->Cell($Wsession, 6, $Ses, 1, 0, 'C', 1);
    }
    $pdf->SetFont($pdf->FontStd,'B',10);
    $pdf->Cell($Wsession, 6, get_text('Total'), 1, 0, 'C', 1);
    $pdf->ln();
    foreach($Categories as $CatCode => $CatDetails) {
        $tot=[0,0];
        $pdf->SetFont($pdf->FontStd,'',8);
        $pdf->Cell($WCode, 5,  $CatCode, 1, 0, 'C', 1);
        $pdf->Cell($WCategory, 5,  $CatDetails['desc'], 1, 0, 'L', 0);
        $pdf->SetFont($pdf->FontStd,'',8);
        foreach(array_keys($SesArray) as $Ses) {
            $Ses=(int)$Ses;
            if($n=$CatDetails['nums'][$Ses]?? '') {
                $pdf->Cell($Wsession - 8, 5, $n[0]?: '', 'TLB', 0, 'R', 0);
                $pdf->Cell(8, 5, $n[1] ? '('.$n[1].')' : '- ', 'TRB', 0, 'R', 0);
                $SesTot[$Ses]+=$n[0];
                $SesWheels[$Ses]+=$n[1];
                $tot[0]+=$n[0];
                $tot[1]+=$n[1];
            } else {
                $pdf->Cell($Wsession, 5, '', 1, 0, 'R', 0);
            }
        }
        $pdf->Cell($Wsession - 8, 5, $tot[0]?: '', 'TLB', 0, 'R', 0);
        $pdf->Cell(8, 5, $tot[1] ? '('.$tot[1].')' : '- ', 'TRB', 0, 'R', 0);
        $pdf->ln();
    }

    $tot=[0,0];
    $pdf->SetFont($pdf->FontStd,'B',1);
    $pdf->Cell($WCode+$WCategory+$Wsession*(count($SesArray)+1), 0.5, '', 1, 1, 'C', 0);
    $pdf->SetFont($pdf->FontStd,'B',10);
    $pdf->Cell($WCode+$WCategory, 5,  get_text('Total'), 1, 0, 'L', 1);
    $pdf->SetFont($pdf->FontStd,'B',8);
    foreach(array_keys($SesArray) as $Ses) {
        $Ses=(int)$Ses;
        $pdf->Cell($Wsession-8, 5, $SesTot[$Ses]?: '', 'TLB', 0, 'R', 1);
        $pdf->Cell(8, 5, $SesWheels[$Ses] ? '('.$SesWheels[$Ses].')' : '- ', 'TRB', 0, 'R', 1);
        $tot[0]+=$SesTot[$Ses];
        $tot[1]+=$SesWheels[$Ses];
    }
    $pdf->SetFont($pdf->FontStd,'B',10);
    $pdf->Cell($Wsession - 8, 5, $tot[0]?: '', 'TLB', 0, 'R', 1);
    $pdf->Cell(8, 5,  $tot[1] ? '('.$tot[1].')' : '- ', 'TRB', 0, 'R', 1);
}

if(!isset($isCompleteResultBook)) {
    $pdf->Output();
}