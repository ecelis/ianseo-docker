<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/pdf/ResultPDF.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Lib/ScorecardsLib.php');
checkACL(AclTeams, AclReadOnly);

$pdf = new ResultPDF((get_text('TeamFinal')),false);
$pdf->setBarcodeHeader(70);

$Score3D = false;
//$MyQuery = "SELECT (TtElabTeam=2) as is3D FROM Tournament INNER JOIN Tournament*Type AS tt ON ToType=TtId WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$MyQuery = "SELECT (ToCategory=8) as is3D FROM Tournament WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$Rs=safe_r_sql($MyQuery);
if(safe_num_rows($Rs)==1) {
    $r=safe_fetch($Rs);
    $Score3D=$r->is3D;
}

//error_reporting(E_ALL);

$FillWithArrows=false;
if((isset($_REQUEST["ScoreFilled"]) AND $_REQUEST["ScoreFilled"]==1)) {
    $FillWithArrows = true;
}

$ShowTeamComponents=false;
if((isset($_REQUEST["TeamComponents"]) AND $_REQUEST["TeamComponents"]==1)) {
    $ShowTeamComponents=true;
}


$pdf->PrintFlags=(!empty($_REQUEST["ScoreFlags"]));

$MyQuery="";
$athData = array();
if (isset($_REQUEST['Blank'])) {
    $_REQUEST["IncEmpty"]=true;
    $rows= empty($_REQUEST['Rows'])?5:intval($_REQUEST['Rows']);
    $cols= empty($_REQUEST['Cols'])?3:intval($_REQUEST['Cols']);
    $sots= empty($_REQUEST['SO'])?1:intval($_REQUEST['SO']);
    $MyQuery = "(SELECT DISTINCT "
        . " '' AS Event, '' AS EventDescr, '' AS EvFinalFirstPhase, 0 as EvMixedTeam, -1 AS Phase, "
        . " '' AS TfTarget, 0 AS MatchNo, 0 as Team, 0 as SubTeam, 0 as OppTeam, 0 as OppSubTeam, "
        . " '' AS CountryCode, '' AS CountryName, '' AS QualRank, '' AS GridPosition, '' AS Target, '' AS OppCountryCode, '' AS OppCountryName, '' AS OppQualRank, '' AS OppGridPosition, '' AS OppTarget, "
        . " '' AS Arrowstring, '' AS OppArrowstring, '' AS TfTieBreak, EvMatchMode, IF(EvMatchArrowsNo=0,0,1) AS EvMatchArrowsNo, 0 as Score, 0 as Tie, 0 as OppTie"
        . " , '' as EvCheckGolds, '' as EvCheckXNines, '' as EvGoldsChars, '' as EvXNineChars"
        . " , '$rows' CalcEnds "
        . " , '$cols' CalcArrows "
        . " , '$sots' CalcSO "
        . "FROM Events "
        . "WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1) "
        . "ORDER BY  EvMatchMode, EvMatchArrowsNo, MatchNo";
} else {
    $options=array('dist'=>0);
    $family='GridTeam';
    $options['tournament']=$_SESSION['TourId'];

    if (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']!=-1) {
        $options['schedule']=substr($_REQUEST['x_Session'], 1,10) . ' ' . substr($_REQUEST['x_Session'], 11,8);
        $OrderBy=true;
    } else {
        $OrderBy=false;
        $Events=array();
        if (!empty($_REQUEST['Event'])) {
            if(is_array($_REQUEST['Event'])) {
                foreach($_REQUEST['Event'] as $Ev) {
                    if(isset($_REQUEST['Phase'])) {
                        if(is_array($_REQUEST['Phase'])) {
                            foreach($_REQUEST['Phase'] as $Ph) {
                                $Ph=intval($Ph);
                                if ($Ph==24) {
                                    $Ph=32;
                                } elseif ($Ph==48) {
                                    $Ph=64;
                                }
                                $Events[]="$Ev@".$Ph;
                            }
                        } else {
                            $Ph=intval($_REQUEST['Phase']);
                            if ($Ph==24) {
                                $Ph=32;
                            } elseif ($Ph==48) {
                                $Ph=64;
                            }
                            $Events[]="$Ev@".$Ph;
                        }
                    } else {
                        $Events[]="$Ev";
                    }
                }
            } else {
                $Ev=$_REQUEST['Event'];
                if(isset($_REQUEST['Phase'])) {
                    if(is_array($_REQUEST['Phase'])) {
                        foreach($_REQUEST['Phase'] as $Ph) {
                            $Ph=intval($Ph);
                            if ($Ph==24) {
                                $Ph=32;
                            } elseif ($Ph==48) {
                                $Ph=64;
                            }
                            $Events[]="$Ev@".$Ph;
                        }
                    } else {
                        $Ph=intval($_REQUEST['Phase']);
                        if ($Ph==24) {
                            $Ph=32;
                        } elseif ($Ph==48) {
                            $Ph=64;
                        }
                        $Events[]="$Ev@".$Ph;
                    }
                } else {
                    $Events[]="$Ev";
                }
            }
        } elseif (isset($_REQUEST['Phase']) && preg_match("/^[0-9]{1,2}$/i",$_REQUEST["Phase"])) {
            $Events='@'.intval($_REQUEST['Phase']);
        }
        if($Events) $options['events']=$Events;
    }
    $rank=Obj_RankFactory::create($family,$options);

    $MyQuery = $rank->getQuery($OrderBy);
    $rank->read();
    $athData=$rank->getData();
 }

$Rs=safe_r_sql($MyQuery);
// Se il Recordset è valido e contiene almeno una riga
if (safe_num_rows($Rs)>0) {
    $ScoreWidth=($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2;
//    $defGoldW  = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(1/17);
//    $defTotalW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(2/17);
//    $defArrowTotW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(10/17);

    $WhereStartX=array($pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2);
    $WhereStartY=array(55,55);
    $WhereX=NULL;
    $WhereY=NULL;
    $AtlheteName=NULL;
    $FollowingRows=false;
//DrawScore
    while($MyRow=safe_fetch($Rs)) {
        $MyRow->EnId=[];
        $MyRow->OppEnId=[];
        $pdf->ScoreCellHeight=9;
        if(empty($_REQUEST["Blank"]) and empty($_REQUEST["IncEmpty"]) and (!$MyRow->CountryCode or !$MyRow->OppCountryCode)) {
            // se è vuoto uno dei due arcieri e non è selezionata l'inclusione
            $EnIds=array();
            if(!empty($_REQUEST["IncAllNames"])) {
                // 3D and Field rounds, get the opponent(s) of previous matches
                if(empty($MyRow->CountryCode)) {
                    $EnIds=get_winner($MyRow->MatchNo, $MyRow->Event);
                    $MyRow->EnId=$EnIds;
                }
                if(empty($MyRow->OppCountryCode)) {
                    $EnIds=get_winner($MyRow->OppMatchNo, $MyRow->Event);
                    $MyRow->OppEnId=$EnIds;
                }
            }
            // salta al prossimo record
            if(empty($EnIds)) {
                continue;
            }
            if(is_array($MyRow->EnId) and is_array($MyRow->OppEnId)) {

                while(count($MyRow->EnId)<count($MyRow->OppEnId)) {
                    $MyRow->EnId[]=' ';
                }
                while(count($MyRow->OppEnId)<count($MyRow->EnId)) {
                    $MyRow->OppEnId[]=' ';
                }
            }
            $pdf->ScoreCellHeight=min(9, ($pdf->getPageHeight()-105-4.5*max(is_array($MyRow->EnId??'') ? count($MyRow->EnId) : 1, is_array($MyRow->OppEnId??'') ? count($MyRow->OppEnId) : 1))/(4+($MyRow->FinElimChooser ? $MyRow->EvElimEnds : $MyRow->EvFinEnds)));
        }
        $NumUnits=15;
        if($MyRow->EvCheckGolds) {
            $NumUnits++;
        }
        if($MyRow->EvCheckXNines) {
            $NumUnits++;
        }
        $defGoldW  = $ScoreWidth*(1/$NumUnits);
        $defTotalW = $ScoreWidth*(3/$NumUnits);
        $defArrowTotW = $ScoreWidth*(6/$NumUnits);

        $AthL = array();
        $AthR = array();
        if($ShowTeamComponents) {
            $AthL = $athData['sections'][$MyRow->Event]['athletes'][$MyRow->Team][$MyRow->SubTeam] ?? array();
            $AthR = $athData['sections'][$MyRow->Event]['athletes'][$MyRow->OppTeam][$MyRow->OppSubTeam] ?? array();
        }

        // disegna lo score di sinistra
        DrawScore($pdf, $MyRow, 'L', $AthL);

        // Disegna lo score di destra
        DrawScore($pdf, $MyRow, 'R', $AthR);

        //Judge Signatures, Timestamp & Annotations
        $pdf->SetLeftMargin($WhereStartX[0]);
        $pdf->Ln(5);

        $pdf->Cell($pdf->GetPageWidth()-(2*$pdf->getSideMargin())-90,8,(get_text('TargetJudgeSignature','Tournament')),'B',0,'L',0);
        $pdf->Cell(90,8,(get_text('TimeStampSignature','Tournament')),1,1,'L',0);
        $pdf->Ln(6);
        $pdf->Cell(0,4,(get_text('JudgeNotes')),'B',1,'L',0);

        // print barcode if any
        if(!empty($_REQUEST['Barcode'])) {
            $pdf->setxy($pdf->BarcodeHeaderX, 10);
            $pdf->SetFont('barcode','',25);
            $pdf->SetFillColor(255);
            $pdf->Cell($pdf->BarcodeHeader, 10, '*' . mb_convert_encoding($MyRow->MatchNo.'-1-'.$MyRow->Event, "UTF-8","cp1252") . "*",0,1,'C',1);
            $pdf->SetDefaultColor();
            $pdf->SetFont($pdf->FontStd,'',10);
            $pdf->setxy($pdf->BarcodeHeaderX, 20);
            $pdf->Cell($pdf->BarcodeHeader, 4, mb_convert_encoding($MyRow->MatchNo.'-1-'.$MyRow->Event, "UTF-8","cp1252"),0,1,'C',0);
        } else {
            $pdf->setBarcodeHeader(10);
        }

        $QrcodeX=$pdf->BarcodeHeaderX-25;
        if(!empty($_REQUEST['QRCode'])) {
            foreach($_REQUEST['QRCode'] as $k => $Api) {
                require_once('Api/'.$Api.'/DrawQRCode.php');
                $Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
                $Function($pdf, $QrcodeX, 5, $MyRow->Event, $MyRow->MatchNo, $MyRow->Phase, 0, "MT");
                $QrcodeX-=25;
            }
        }

        if($_REQUEST['ScoreQrPersonal']??'') {
            DrawScoreQrPersonal($pdf, intval($MyRow->Target), $QrcodeX, 5);
        }
    }
//END OF DrawScore
    safe_free_result($Rs);
}

$pdf->Output();

function DrawScore(&$pdf, $MyRow, $Side='L', $Athletes=array()) {
	global $CFG, $ScoreWidth, $defTotalW, $defGoldW, $defArrowTotW, $FollowingRows, $WhereStartX, $WhereStartY,  $FillWithArrows;
	if(isset($_REQUEST['Blank'])) {
		$tmp=new stdClass();
		$tmp->ends=empty($_REQUEST['Rows'])?5:intval($_REQUEST['Rows']);
		$tmp->arrows = empty($_REQUEST['Cols'])?3:intval($_REQUEST['Cols']);
		$tmp->so = empty($_REQUEST['SO'])?1:intval($_REQUEST['SO']);
	} else {
		$tmp=getEventArrowsParams($MyRow->Event, $MyRow->Phase, 1);
	}
	$NumRow=$tmp->ends;
	$NumCol=$tmp->arrows;
	$ArrowW = $defArrowTotW/($NumCol);
	$TotalW=$defTotalW;
	$GoldW=$defGoldW;
    $margins = $pdf->getMargins();
    $ScoreCellHeight= min(($pdf->GetPageHeight()-$margins['bottom']-100)/($NumRow+6), $pdf->ScoreCellHeight);

	$Prefix='Opp';
	$Opponent='';
	$ScorePrefix='';
	if($MyRow->EvMatchMode) {
		$ScorePrefix='Set';
	}

//		echo $MyRow->EvMatchArrowsNo . "." . $MyRow->GrPhase ."." . ($MyRow->EvMatchArrowsNo & ($MyRow->GrPhase>0 ? $MyRow->GrPhase*2:1)) . "/" . $NumRow . "--<br>";
	if($Side=='L') {
		if($FollowingRows) $pdf->AddPage();
		$Prefix='';
		$Opponent='Opp';
	}

	$FollowingRows=true;
	$WhichScore=($Side=='R');
	$WhereX=$WhereStartX;
	$WhereY=$WhereStartY;
//Intestazione Atleta
    $pdf->SetDefaultColor();
	$pdf->SetLeftMargin($WhereStartX[$WhichScore]);
	$pdf->SetY(35);
// Flag of Country/Club
	if($pdf->PrintFlags) {
		if(is_file($file= $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$MyRow->{$Prefix.'CountryCode'}.'.jpg')) {
			$H=12;
			$W=18;
			$OrgY=$pdf->gety();
			$OrgX=$ScoreWidth-2*$GoldW-18;
			$pdf->Image($file, $pdf->getx()+$OrgX, $OrgY, $W, $H, 'JPG', '', '', true, 300, '', false, false, 1, true);
			$FlagOffset=$W+1;
		} else {
            $pdf->Cell($ScoreWidth, 6,'', 'T', 0, 'L', 0);
            $pdf->setx($pdf->getx()-$ScoreWidth);
        }
	}

	$AthCell=6;
	$AthHeight=6;
	$RankHeight=6;
	if(($MyRow->Team and is_array($MyRow->Team)) or ($MyRow->OppTeam and is_array($MyRow->OppTeam))) {
		$AthCell=4.5;
		if($MyRow->Team and is_array($MyRow->Team)) {
			$AthHeight=$AthCell*count($MyRow->Team);
		} else {
			$AthHeight=$AthCell*count($MyRow->OppTeam);
		}
		$WhereY[$WhichScore]=$WhereY[$WhichScore]+$AthHeight-6;
		$RankHeight=6+$AthHeight;
	}
    if(($MyRow->EnId and is_array($MyRow->EnId)) or ($MyRow->OppEnId and is_array($MyRow->OppEnId))) {
        $AthCell=4.5;
        if($MyRow->EnId and is_array($MyRow->EnId)) {
            $AthHeight=$AthCell*count($MyRow->EnId);
        } else {
            $AthHeight=$AthCell*count($MyRow->OppEnId);
        }
        $WhereY[$WhichScore]=$WhereY[$WhichScore]+$AthHeight-6;
        $RankHeight=$AthHeight;
    }

	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(20, $AthHeight,(get_text('Team')) . ': ', 'LT', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
    if($MyRow->{$Prefix.'CountryName'}) {
        $pdf->Cell($ScoreWidth-20-2*$GoldW-($pdf->PrintFlags?18:0), $RankHeight, (($MyRow->{$Prefix.'CountryName'}??'') . (strlen($MyRow->{$Prefix.'CountryCode'}??'')>0 ?  ' (' . $MyRow->{$Prefix.'CountryCode'}  . ')' : '')), 'T', 1, 'L', 0);
    } elseif($MyRow->{$Prefix.'EnId'} and is_array($MyRow->{$Prefix.'EnId'})) {
        $OrgX=$pdf->getX();
        foreach($MyRow->{$Prefix.'EnId'} as $k=>$Athlete) {
            $pdf->setX($OrgX);
            $pdf->Cell($ScoreWidth-20-2*$GoldW-($pdf->PrintFlags?18:0),$AthCell,$Athlete, $k ? '' : 'T', 1, 'L', 0);
        }
    } else {
        $pdf->Cell($ScoreWidth-20-2*$GoldW-($pdf->PrintFlags?18:0), 6, '', 'T', 1, 'L', 0);
    }

    if(count($Athletes)) {
        $first = true;
        foreach ($Athletes as $kAth=>$ath) {
            $pdf->SetFont($pdf->FontStd, '', 10);
            $pdf->Cell(15, (count($Athletes)==1 ? 6:4), ($first ? (get_text('Athletes')) . ': ' : ''), 'L', 0, 'L', 0);
            $pdf->SetFont($pdf->FontStd, '', 8);
            $pdf->Cell(5, (count($Athletes)==1 ? 6:4), TCPDF_FONTS::unichr($kAth+65).')', 0, 0, 'R', 0);
            $pdf->SetFont($pdf->FontStd, 'B', 8);
            $pdf->Cell($NumCol * $ArrowW + 2 * $TotalW + $GoldW - 20, (count($Athletes)==1 ? 6:4), $ath['athlete'], 0, 1, 'L', 0);
            $first = false;
        }
    } else {
        $pdf->Cell($ScoreWidth-$TotalW-($pdf->PrintFlags?18:0), 6, '', 'L', 1, 'L', 0);
    }

    $pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(20,6,(get_text('DivisionClass')) . ': ', 'LB', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($ScoreWidth-20-$TotalW-2*$GoldW,6, get_text($MyRow->EventDescr,'','',true), 'B', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($TotalW,6, (get_text('Target')) . ' ' . ltrim($MyRow->{$Prefix.'Target'}??'','0'), '1', 1, 'C', 1);

	// Rank number
	$pdf->SetXY($ScoreWidth-2*$GoldW+$WhereStartX[$WhichScore], 35);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(2*$GoldW,6, (get_text('Rank')),'TLR',1,'C',1);
	$pdf->SetXY($ScoreWidth-2*$GoldW+$WhereStartX[$WhichScore],$pdf->GetY());
	$pdf->SetFont($pdf->FontStd,'B',25);
	$pdf->Cell(2*$GoldW,$RankHeight+(count($Athletes)<=1 ? 6:4)*max(count($Athletes),1), ($MyRow->{$Prefix.'QualRank'} ?? ''),'BLR',1,'C',1);

    //Readjust start of score where needed
    if($WhereY[0] <= $pdf->getY()+5) {
        $WhereY = array($pdf->getY()+5, $pdf->getY()+5);
    }

//Header
	$PhaseName='';
	if($MyRow->{'Phase'}>=0) {
		$PhaseName=get_text(namePhase($MyRow->EvFinalFirstPhase, $MyRow->Phase). '_Phase');
	}
	if(!empty($MyRow->GameNumber)) {
		$PhaseName.=' - '.get_text('GameNumber', 'Tournament', $MyRow->GameNumber);
	}
   	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell($GoldW,$ScoreCellHeight,'',0,0,'C',0);
	$pdf->Cell($ScoreWidth-$GoldW,$ScoreCellHeight, $PhaseName,1,0,'C',1);
//Winner Checkbox
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell(2*$GoldW,$ScoreCellHeight,'',0,0,'C',0);
	//$pdf->Rect($WhereX[$WhichScore]+$GoldW+2,$WhereY[$WhichScore]+2,$GoldW-4,$ScoreCellHeight-4,'DF',array(),array(255,255,255));
	$pdf->Rect($WhereX[$WhichScore]+$GoldW+2,$WhereY[$WhichScore]+2,$ScoreCellHeight-4,$ScoreCellHeight-4,'DF',array(),array(255,255,255));
	if($FillWithArrows && ($MyRow->{$Prefix.$ScorePrefix.'Score'} > $MyRow->{$Opponent.$ScorePrefix.'Score'} || ($MyRow->{$Prefix.$ScorePrefix.'Score'} == $MyRow->{$Opponent.$ScorePrefix.'Score'} && $MyRow->{$Prefix.'Tie'} > $MyRow->{$Opponent.'Tie'} ))) {
		$tmpWidth=$pdf->GetLineWidth();
		$pdf->SetLineWidth($tmpWidth*5);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+1,$WhereY[$WhichScore]+1,$WhereX[$WhichScore]+$GoldW+$ScoreCellHeight-1,$WhereY[$WhichScore]+$ScoreCellHeight-1);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+1,$WhereY[$WhichScore]+$ScoreCellHeight-1,$WhereX[$WhichScore]+$GoldW+$ScoreCellHeight-1,$WhereY[$WhichScore]+1);
		$pdf->SetLineWidth($tmpWidth);
	}
	$pdf->SetDefaultColor();
	$pdf->Cell($GoldW+2*$TotalW+$NumCol*$ArrowW,$ScoreCellHeight, get_text('Winner'),0,1,'L',0);
	$WhereY[$WhichScore]=$pdf->GetY();

// Row 2: Arrow numbers, totale, points, etc
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
    $pdf->Cell($GoldW,$ScoreCellHeight,'',0,0,'C',0);
    for($j=0; $j<$NumCol; $j++) {
        $pdf->Cell($ArrowW,$ScoreCellHeight, ($j+1), 1, 0, 'C', 1);
	}
	$pdf->Cell($TotalW, $ScoreCellHeight, get_text(($MyRow->{'EvMatchMode'}==0 ? 'EndTotal':'SetTotal'),'Tournament'),1,0,'C',1);

	if($MyRow->{'EvMatchMode'}==0) {
		$pdf->Cell($TotalW+2*$GoldW, $ScoreCellHeight, get_text('RunningTotal','Tournament'),1,0,'C',1);
	} else {
		$pdf->Cell(2*$GoldW,$ScoreCellHeight,get_text('SetPoints', 'Tournament'),1,0,'C',1);
		$pdf->Cell($TotalW,$ScoreCellHeight,get_text('TotalPoints','Tournament'),1,0,'C',1);
	}
    if($MyRow->EvCheckGolds) {
        $pdf->Cell($GoldW,$ScoreCellHeight,$MyRow->EvGolds,1,0,'C',1);
    }
    if($MyRow->EvCheckXNines) {
        $pdf->Cell($GoldW,$ScoreCellHeight, $MyRow->EvXNine,1,0,'C',1);
    }
    $pdf->ln();
	$WhereY[$WhichScore]=$pdf->GetY();
//Righe
	$ScoreTotal = 0;
    $GoldsTotal = 0;
    $XNineTotal = 0;
    $SetTotal = 0;

    for($i=1; $i<=$NumRow; $i++) {
        $a=0;
        $b=0;
        $c=0;
        $ScoreEndTotal = 0;
	   	$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
		$pdf->Cell($GoldW,$ScoreCellHeight,$i,1,0,'C',1);
		$pdf->SetFont($pdf->FontStd,'',10);
        list($a,$b,$c)=ValutaArrowStringGX(substr($MyRow->{$Prefix . 'Arrowstring'}, ($i - 1) * $NumCol, $NumCol), $MyRow->EvGoldsChars,  $MyRow->EvXNineChars);
        for($j=0; $j<$NumCol; $j++) {
            $pdf->SetFont($pdf->FontStd,'',10);
            if(count($Athletes) AND !$FillWithArrows) {
                $pdf->Cell($ArrowW-3, $ScoreCellHeight, '', 'LTB', 0, 'C', 0);
                $posTemp = array($pdf->getX(), $pdf->getY());
                $tmpScoreCellHeight = $ScoreCellHeight/count($Athletes);
                $tmpPadding = $pdf->getCellPaddings();
                $pdf->setCellPadding(0);
                $pdf->SetFont($pdf->FontStd,'',6);
                for($ath=0; $ath<count($Athletes); $ath++) {
                    $pdf->setXY($posTemp[0],$posTemp[1]+$tmpScoreCellHeight*$ath);
                    $pdf->Cell(3, $tmpScoreCellHeight, TCPDF_FONTS::unichr($ath+65), '1', 0, 'C', 0);
                }
                $pdf->setXY($posTemp[0]+3,$posTemp[1]);
                $pdf->setCellPaddings($tmpPadding['L'],$tmpPadding['T'],$tmpPadding['R'],$tmpPadding['B']);
            } else {
                $pdf->Cell($ArrowW, $ScoreCellHeight, ($FillWithArrows ? DecodeFromLetter(substr($MyRow->{$Prefix . 'Arrowstring'}, ($i - 1) * $NumCol + $j, 1)) : ''), 1, 0, 'C', 0);
            }
		}
        $IsEndScore= trim(substr($MyRow->{$Prefix.'Arrowstring'}, ($i-1)*$NumCol, $NumCol));
        $ScoreEndTotal = ValutaArrowstring(substr($MyRow->{$Prefix.'Arrowstring'},($i-1)*$NumCol,$NumCol));
        $ScoreTotal += $ScoreEndTotal;
		$pdf->SetFont($pdf->FontStd,'', ($MyRow->EvMatchMode==0 ? 10 : 12));
		$pdf->Cell($TotalW,$ScoreCellHeight,($FillWithArrows && $IsEndScore ? $ScoreEndTotal : ''),1,0,'C',0);

		if($MyRow->EvMatchMode==0) {
			$pdf->SetFont($pdf->FontStd,'', 12);
			$pdf->Cell($TotalW+2*$GoldW, $ScoreCellHeight, ($FillWithArrows && $IsEndScore ? $ScoreTotal : ''),1,0,'C',0);
		} else {
			$SetTotSx = '';
			if($IsEndScore && $FillWithArrows) {
				$SetPointSx= ValutaArrowstring(substr($MyRow->{$Prefix.'Arrowstring'}, ($i-1)*$NumCol, $NumCol));
				$SetPointDx= ValutaArrowstring(substr($MyRow->{$Opponent.'Arrowstring'}, ($i-1)*$NumCol, $NumCol));

				if($SetPointSx > $SetPointDx) {
					$SetTotSx= 2;
				} elseif($SetPointSx < $SetPointDx) {
					$SetTotSx= 0;
				} else {
					$SetTotSx= 1;
				}
				$SetTotal = $SetTotal + $SetTotSx;
			}

			$pdf->SetFont($pdf->FontStd,'B',11);
			if($SetTotSx==2 && $FillWithArrows) {
				$pdf->Circle($pdf->GetX()+$GoldW/3,$pdf->GetY()+$ScoreCellHeight/2, $GoldW/4, 0, 360, 'FD');
			}
			$pdf->Cell($GoldW*2/3,$ScoreCellHeight,'2',1, 0,'C',0);
			if($SetTotSx==1 && $FillWithArrows) {
				$pdf->Circle($pdf->GetX()+$GoldW/3,$pdf->GetY()+$ScoreCellHeight/2, $GoldW/4, 0, 360, 'FD');
			}
			$pdf->Cell($GoldW*2/3,$ScoreCellHeight,'1',1, 0,'C',0);
			if($SetTotSx==0 && $IsEndScore && $FillWithArrows) {
				$pdf->Circle($pdf->GetX()+$GoldW/3,$pdf->GetY()+$ScoreCellHeight/2, $GoldW/4, 0, 360, 'FD');
			}
			$pdf->Cell($GoldW*2/3,$ScoreCellHeight,'0',1, 0,'C',0);
			$pdf->Cell( $TotalW,$ScoreCellHeight,($IsEndScore && $FillWithArrows ? $SetTotal : ''),1, 0,'C',0);
		}
        if($MyRow->EvCheckGolds) {
            $pdf->Cell($GoldW,$ScoreCellHeight,$FillWithArrows && $IsEndScore ? ($b??'') : '', 1, 0,'C',0);
            $GoldsTotal += $b;
        }
        if($MyRow->EvCheckXNines) {
            $pdf->Cell($GoldW,$ScoreCellHeight, $FillWithArrows && $IsEndScore ? ($c??'') : '',1,0,'C',0);
            $XNineTotal += $c;
        }
        $pdf->ln();
		$WhereY[$WhichScore]=$pdf->GetY();
	}

//Shoot Off
	$closeToCenter=false;
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]+($ScoreCellHeight/4));
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell($GoldW, $ScoreCellHeight * 3.5 +1, (get_text('TB')),1,0,'C',1);
    $ShootOffW = min(15, $tmp->so<=$NumCol ? $ArrowW : ($ArrowW*$NumCol)/$tmp->so);
    $ShootTotalW = min(20, ($tmp->so<$NumCol ? min(20,$NumCol-$tmp->so)*$ArrowW : $TotalW));
    $pdf->SetFont($pdf->FontStd,'',10);
	$StartX=$pdf->getx();
    for($i=0; $i<3; $i++) {
        $pdf->SetX($StartX);
        for($j=0; $j<$tmp->so; $j++) {
            $pdf->SetFont($pdf->FontStd,($tmp->so==1 ? 'B' : ''),10);
            if(count($Athletes) AND !$FillWithArrows) {
                $pdf->Cell($ShootOffW-3, $ScoreCellHeight, '', 'LTB', 0, 'C', 0);
                $posTemp = array($pdf->getX(), $pdf->getY());
                $tmpScoreCellHeight = $ScoreCellHeight/count($Athletes);
                $tmpPadding = $pdf->getCellPaddings();
                $pdf->setCellPadding(0);
                $pdf->SetFont($pdf->FontStd,'',6);
                for($ath=0; $ath<count($Athletes); $ath++) {
                    $pdf->setXY($posTemp[0],$posTemp[1]+$tmpScoreCellHeight*$ath);
                    $pdf->Cell(3, $tmpScoreCellHeight, TCPDF_FONTS::unichr($ath+65), '1', 0, 'C', 0);
                }
                $pdf->setXY($posTemp[0]+3,$posTemp[1]);
                $pdf->setCellPaddings($tmpPadding['L'],$tmpPadding['T'],$tmpPadding['R'],$tmpPadding['B']);
            } else {
                $pdf->Cell($ShootOffW,$ScoreCellHeight,($FillWithArrows ? DecodeFromLetter(substr($MyRow->{$Prefix.'TieBreak'}, $i*$tmp->so + $j ,1)) : ''),1,0,'C',0);
            }


            if($FillWithArrows AND $MyRow->{$Prefix.'TieClosest'} != 0) {
                $closeToCenter=true;
            }
        }
        if($tmp->so>1) {
            $pdf->SetX($pdf->GetX()+2);
            $pdf->SetFont($pdf->FontStd,'B',10);
            $pdf->Cell($ShootTotalW, $ScoreCellHeight, (($FillWithArrows AND trim(substr($MyRow->{$Prefix.'TieBreak'}??'',$i*$tmp->so,$tmp->so))) ? ValutaArrowstring(substr($MyRow->{$Prefix.'TieBreak'},$i*$tmp->so,$tmp->so)) : ''), 1, 0, 'C', 0);
        }
        $pdf->ln();
    }
    if($MyRow->{$Prefix.'Tie'}==1) $SetTotal++;
	$SOY=$pdf->GetY();

//Totale
	$Errore=($FillWithArrows and (strlen($MyRow->{$Prefix.'Arrowstring'}) and ($MyRow->{'EvMatchMode'} ? $MyRow->{$Prefix.'SetScore'}!=$SetTotal : $MyRow->{$Prefix.$ScorePrefix.'Score'}!=$ScoreTotal)));
    $pdf->SetXY($TopX=$StartX+$ArrowW*$NumCol,$WhereY[$WhichScore]);
	$pdf->SetFont($pdf->FontStd,'B',10);
	if($MyRow->EvMatchMode==0) {
		$pdf->Cell($TotalW,$ScoreCellHeight,get_text('Total'),0,0,'R',0);
		$pdf->SetFont($pdf->FontStd,'B',11);
		$pdf->Cell($TotalW+2*$GoldW,$ScoreCellHeight,($FillWithArrows ? $ScoreTotal : ''),1,0,'C',0);
		if($Errore) {
			$pdf->Line($x1 = $TopX+$TotalW, $y1=$pdf->gety()+$ScoreCellHeight, $x1+$TotalW, $y1-$ScoreCellHeight);
		}
		$pdf->SetFont($pdf->FontStd,'',10);
	} else {
		$pdf->Cell($TotalW,$ScoreCellHeight,'',0,0,'R',0);
		$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->Cell(2*$GoldW,$ScoreCellHeight,get_text('Total'),0,0,'R',0);
		$pdf->SetFont($pdf->FontStd,'B',14);
		$pdf->Cell($TotalW,$ScoreCellHeight,($FillWithArrows ? $MyRow->{$Prefix.'SetScore'} : ''),1,0,'C',0);
		if($Errore) {
			$pdf->Line($x1 = $TopX+2*$GoldW + $TotalW * 8/5, $y1=$pdf->gety()+$ScoreCellHeight, $x1 + 2/5*$TotalW, $y1-$ScoreCellHeight);
		}
	}

    if($MyRow->EvCheckGolds) {
        $pdf->Cell($GoldW,$ScoreCellHeight,$FillWithArrows ? ($GoldsTotal??'') : '', 1, 0,'C',0);
    }
    if($MyRow->EvCheckXNines) {
        $pdf->Cell($GoldW,$ScoreCellHeight, $FillWithArrows ? ($XNineTotal??'') : '',1,0,'C',0);
    }
    $pdf->ln();

    $WhereY[$WhichScore]=$pdf->GetY();

	if($Errore) {
		$pdf->SetX($TopX);
		$pdf->SetFont($pdf->FontStd,'B',11);
		$pdf->Cell($MyRow->EvMatchMode ? 2*$GoldW + $TotalW * 8/5 : $TotalW, $ScoreCellHeight, (get_text('SignedTotal', 'Tournament') . " "), 0,0,'R',0);
		$pdf->Cell($MyRow->EvMatchMode ? 2/5*$TotalW : $TotalW, $ScoreCellHeight, $MyRow->{$Prefix.$ScorePrefix.'Score'}, 1, 0, 'C', 0);
		$pdf->ln();
	}

//Closet to the center
	$pdf->SetFont($pdf->FontStd,'',9);
	$pdf->SetXY($WhereX[$WhichScore]+$GoldW+($ShootOffW/2), $SOY + 1);
	$pdf->Cell($ShootOffW/2,$ScoreCellHeight/2,'',1,0,'R',0);
	if($closeToCenter) {
		$tmpWidth=$pdf->GetLineWidth();
		$pdf->SetLineWidth($tmpWidth*5);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+($ShootOffW/2),$SOY + 1, $WhereX[$WhichScore]+$GoldW + $ShootOffW, $SOY + 1 + $ScoreCellHeight/2);
		$pdf->Line($WhereX[$WhichScore]+ $GoldW + ($ShootOffW/2),$SOY + 1 +$ScoreCellHeight/2, $WhereX[$WhichScore]+$GoldW + $ShootOffW, $SOY+1);
		$pdf->SetLineWidth($tmpWidth);
	}
	$pdf->Cell($ArrowW*($NumCol-1),$ScoreCellHeight*2/4,get_text('Close2Center','Tournament'),0,0,'L',0);
	$WhereY[$WhichScore]=$pdf->GetY()+10;
//Firme Athletes/agents
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
   	$pdf->SetFont($pdf->FontStd,'I',7);
	$pdf->Cell($ScoreWidth,4,(get_text('ArcherSignature','Tournament')),'B',1,'L',0);

}

function get_winner($MatchNo, $Event) {
    $ret=array();
    $Bronze=($MatchNo==2 or $MatchNo==3);
    if($MatchNo<2) {
        $MatchNo+=2;
    }
    $q=safe_r_sql("select concat('(#', TeRank, ') ', CoName, if(TfSubTeam>0, TfSubTeam, ''), ' (', CoCode, if(TfSubTeam>0, TfSubTeam, ''),')') as Athlete, CoCode, TfMatchNo, TfWinLose 
		from TeamFinals
	    left join (
	        select TeCoId, TeSubTeam, TeTournament, CoCode, CoName, TeRank
            from Teams
            inner join Countries on CoId=TeCoId
            where TeTournament={$_SESSION['TourId']} and TeEvent='$Event'
	    ) Countries on TeCoId=TfTeam and TeSubTeam=TfSubTeam
		where TfMatchNo in (".($MatchNo*2).", ".($MatchNo*2 + 1).") and TfEvent='$Event' and TfTournament={$_SESSION['TourId']} order by TfWinLose desc");
    while($r=safe_fetch($q)) {
        if($r->TfWinLose) {
            $ret[]=$r->Athlete;
            return $ret;
        }
        $ret=array_merge($ret, get_winner($r->TfMatchNo, $Event));
        if($r->CoCode) {
            $ret[]=$r->Athlete;
        }
    }
    $ret=array_unique($ret);
    if($Bronze and count($ret)==1) {
        // if the semifinals are with only one contender, then no bronze are done!
        $ret=[' '];
    }
    return $ret;
}


