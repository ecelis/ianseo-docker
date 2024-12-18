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
	checkACL(AclIndividuals, AclReadOnly);

    $Options=[];
    if(!empty($_REQUEST['Margins'])) {
        $Options=[
            'PrintLogo'=>false,
            'print_header'=>false,
            'print_footer'=>false,
        ];
    }
	$pdf = new ResultPDF((get_text('IndFinal')),false, '', true, $Options);
	$pdf->setBarcodeHeader(empty($_REQUEST['Barcode']) ? '10' : '70');

	$pdf->ScoreCellHeight=9;

	//error_reporting(E_ALL);

	$FillWithArrows=false;
	if((isset($_REQUEST["ScoreFilled"]) && $_REQUEST["ScoreFilled"]==1))
		$FillWithArrows=true;

	$pdf->PrintFlags=(!empty($_REQUEST["ScoreFlags"]));

	$MyQuery="";
	if (isset($_REQUEST['Blank'])) {
		$MyQuery = "SELECT DISTINCT
		 	'' AS Event, '' as CountryName, '' as Athlete, '' as CountryCode, '' as EventDescr, '' as Target, '' as Position, -1 as Phase, EvMatchMode as EvMatchMode, EvElimType, '' as ArrowString, '' as Tie,
		 	'' as EnId, '' as OppEnId, '' as OppAthlete, '' as OppCountryCode, '' as OppCountryName, '' as OppTarget, '' as OppPosition, '' as OppArrowString, '' as OppTie, '' as QualRank, '' as OppQualRank, 
		 	'' as EvCheckGolds, '' as EvCheckXNines, '' as EvGoldsChars, '' as EvXNineChars
			FROM Events
			WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0";
	} else {
		$options=array('dist'=>0);
		$family='GridInd';
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
	 }
	$Rs=safe_r_sql($MyQuery);
// Se il Recordset è valido e contiene almeno una riga
	if (safe_num_rows($Rs)>0) {
//		$defGoldW  = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(1/15);
//		$defTotalW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(3/15);
//		$defArrowTotW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(6/15);
        $ScoreWidth=($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2;

		$WhereStartX=array($pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2);
		$WhereStartY=array(60,60);
		$WhereX=NULL;
		$WhereY=NULL;
		$AtlheteName=NULL;
		$FollowingRows=false;

        $BarCodeX=$pdf->BarcodeHeaderX;
        $BarCodeY=10;

        if(!empty($_REQUEST['Margins'])) {
            $BarCodeX=intval($_REQUEST['LeftMargin']??$BarCodeX);
            $BarCodeY=intval($_REQUEST['TopMargin']??$BarCodeY);
        }

//DrawScore
		while($MyRow=safe_fetch($Rs)) {
			set_time_limit(30);
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

            if(empty($_REQUEST["Blank"]) AND  empty($_REQUEST["IncEmpty"]) && (empty($MyRow->EnId) || empty($MyRow->OppEnId))) {
                // if at least one of the archer is missing and the include all names is set and not a blank scorecard and not include also empty scorecards
                $EnIds=array();
                if(!empty($_REQUEST["IncAllNames"])) {
                    if(empty($MyRow->EnId)) {
                        $EnIds=get_winner($MyRow->MatchNo, $MyRow->Event);
                        $MyRow->EnId=$EnIds;
                    }
                    if(empty($MyRow->OppEnId)) {
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
            }
            if(!empty($_REQUEST["Blank"]) and !empty($_REQUEST['Rows'])) {
                $pdf->ScoreCellHeight = min(9, intval(($pdf->getPageHeight() - 120 - 4.5) / (4 + intval($_REQUEST['Rows']??5))));
            } else {
                $pdf->ScoreCellHeight = min(9, intval(($pdf->getPageHeight() - 120 - 4.5 * (is_array($MyRow->EnId) ? count($MyRow->EnId) : 1)) / (4 + ($MyRow->FinElimChooser ?? 0 ? $MyRow->EvElimEnds ?? 5 : $MyRow->EvFinEnds ?? 5))));
            }

			// disegna lo score di sinistra
			DrawScore($pdf, $MyRow, 'L');

			// Disegna lo score di destra
			DrawScore($pdf, $MyRow, 'R');

			//Judge Signatures, Timestamp & Annotations
			$pdf->SetLeftMargin($WhereStartX[0]);
			$pdf->Ln(5);

			$pdf->Cell($pdf->GetPageWidth()-(2*$pdf->getSideMargin())-90,8,(get_text('TargetJudgeSignature','Tournament')),'B',0,'L',0);
			$pdf->Cell(90,8,(get_text('TimeStampSignature','Tournament')),1,1,'L',0);
			$pdf->Ln(6);
			$pdf->Cell(0,4,(get_text('JudgeNotes')),'B',1,'L',0);

			// print barcode if any
			if(!empty($_REQUEST['Barcode'])) {
				$pdf->setxy($BarCodeX, $BarCodeY);
				$pdf->SetFont('barcode','',25);
				$pdf->SetFillColor(255);
				$pdf->Cell($pdf->BarcodeHeader, 10, '*' . mb_convert_encoding($MyRow->MatchNo.'-0-'.$MyRow->Event, "UTF-8","cp1252") . "*",0,1,'C',1);
				$pdf->SetDefaultColor();
				$pdf->SetFont($pdf->FontStd,'',10);
				$pdf->setxy($BarCodeX, $BarCodeY+10);
				$pdf->Cell($pdf->BarcodeHeader, 4, mb_convert_encoding($MyRow->MatchNo.'-0-'.$MyRow->Event, "UTF-8","cp1252"),0,1,'C',0);
			} else {
				$pdf->setBarcodeHeader(10);
			}

            $QrcodeX=$BarCodeX-25;
			if(!empty($_REQUEST['QRCode'])) {
				foreach($_REQUEST['QRCode'] as $k => $Api) {
					require_once('Api/'.$Api.'/DrawQRCode.php');
					$Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
					$Function($pdf, $QrcodeX, 5, $MyRow->Event, $MyRow->MatchNo, $MyRow->Phase, 0, "MI");
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

function DrawScore(&$pdf, $MyRow, $Side='L') {
	global $CFG, $ScoreWidth, $defTotalW, $defGoldW, $defArrowTotW, $FollowingRows, $TrgOutdoor, $WhereStartX, $WhereStartY, $FillWithArrows;
	if(isset($_REQUEST['Blank'])) {
		$tmp=new stdClass();
		$tmp->ends=empty($_REQUEST['Rows'])?5:intval($_REQUEST['Rows']);
		$tmp->arrows = empty($_REQUEST['Cols'])?3:intval($_REQUEST['Cols']);
		$tmp->so = empty($_REQUEST['SO'])?1:intval($_REQUEST['SO']);
	} else {
		$tmp=getEventArrowsParams($MyRow->Event, $MyRow->Phase, 0);
	}
	$NumRow=$tmp->ends;
	$NumCol=$tmp->arrows;
	$ArrowW = $defArrowTotW/$NumCol;
	$TotalW=$defTotalW;
	$GoldW=$defGoldW;

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
			$OrgX=$ScoreWidth-$TotalW-18;
			$pdf->Image($file, $pdf->getx()+$OrgX, $OrgY, $W, $H, 'JPG', '', '', true, 300, '', false, false, 1, true);
			$FlagOffset=$W+1;
		}
	}

	$AthCell=6;
	$AthHeight=6;
	$RankHeight=12;
	if(($MyRow->EnId and is_array($MyRow->EnId)) or ($MyRow->OppEnId and is_array($MyRow->OppEnId))) {
		$AthCell=4.5;
		if($MyRow->EnId and is_array($MyRow->EnId)) {
			$AthHeight=$AthCell*count($MyRow->EnId);
		} else {
			$AthHeight=$AthCell*count($MyRow->OppEnId);
		}
		$WhereY[$WhichScore]=$WhereY[$WhichScore]+$AthHeight-6;
		$RankHeight=6+$AthHeight;
	}
	//error_reporting(E_ALL);

	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(20,$AthHeight,(get_text('Athlete')) . ': ', 'TL', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	if($MyRow->{$Prefix.'Athlete'}) {
		$pdf->Cell($ScoreWidth-20-$TotalW-($pdf->PrintFlags?18:0),$AthHeight,($MyRow->{$Prefix.'Athlete'}), 'T', 1, 'L', 0);
	} elseif($MyRow->{$Prefix.'EnId'} and is_array($MyRow->{$Prefix.'EnId'})) {
		$OrgX=$pdf->getX();
		foreach($MyRow->{$Prefix.'EnId'} as $k=>$Athlete) {
			$pdf->setX($OrgX);
			$pdf->Cell($ScoreWidth-20-$TotalW-($pdf->PrintFlags?18:0),$AthCell,$Athlete, $k ? '' : 'T', 1, 'L', 0);
		}
	} else {
        $pdf->Cell($ScoreWidth-20-$TotalW-($pdf->PrintFlags?18:0),$AthHeight,'', 'T', 1, 'L', 0);
    }
   	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(20,6,(get_text('Country')) . ': ', 'L', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($ScoreWidth-20-$TotalW,6, ($MyRow->{$Prefix.'CountryCode'} ? ($MyRow->{$Prefix.'CountryName'} . ' (' . $MyRow->{$Prefix.'CountryCode'}  . ')') : ''), 0, 1, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(20,6,(get_text('DivisionClass')) . ': ', 'LB', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($ScoreWidth-20-2*$TotalW,6, get_text($MyRow->EventDescr,'','',true), 'B', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($TotalW,6, (get_text('Target')) . ' ' . ltrim($MyRow->{$Prefix.'Target'}??'','0'), '1', 1, 'C', 1);

	// Rank number
	$pdf->SetXY($ScoreWidth-$TotalW+$WhereStartX[$WhichScore], 35);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($TotalW,6, (get_text('Rank')),'TLR',1,'C',1);
	$pdf->SetXY($ScoreWidth-$TotalW+$WhereStartX[$WhichScore],$pdf->GetY());
	$pdf->SetFont($pdf->FontStd,'B',25);
	$pdf->Cell($TotalW,$RankHeight, ($MyRow->{$Prefix.'QualRank'} ?? ''),'BLR',1,'C',1);

//Header
	$PhaseName='';
	if($MyRow->{'Phase'}>=0) {
		$PhaseName=get_text(namePhase($MyRow->EvFinalFirstPhase, $MyRow->Phase). '_Phase');
		if($MyRow->EvElimType==3 and isset($pdf->PoolMatches[$MyRow->MatchNo])) {
			$PhaseName=$pdf->PoolMatches[$MyRow->MatchNo];
		} elseif($MyRow->EvElimType==4 and isset($pdf->PoolMatchesWA[$MyRow->MatchNo])) {
			$PhaseName=$pdf->PoolMatchesWA[$MyRow->MatchNo];
		}
	}
	if(!empty($MyRow->GameNumber)) {
		$PhaseName.=' - '.get_text('GameNumber', 'Tournament', $MyRow->GameNumber);
	}
   	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell($GoldW,$pdf->ScoreCellHeight,'',0,0,'C',0);
	$pdf->Cell($ScoreWidth-$GoldW,$pdf->ScoreCellHeight, $PhaseName,1,0,'C',1);
//Winner Checkbox
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell($TotalW,$pdf->ScoreCellHeight,'',0,0,'C',0);
	//$pdf->Rect($WhereX[$WhichScore]+$GoldW+2,$WhereY[$WhichScore]+2,$GoldW-4,$pdf->ScoreCellHeight-4,'DF',array(),array(255,255,255));
	$pdf->Rect($WhereX[$WhichScore]+$GoldW+2,$WhereY[$WhichScore]+2,$pdf->ScoreCellHeight-4,$pdf->ScoreCellHeight-4,'DF',array(),array(255,255,255));
	if($FillWithArrows && ($MyRow->{$Prefix.$ScorePrefix.'Score'} > $MyRow->{$Opponent.$ScorePrefix.'Score'} || ($MyRow->{$Prefix.$ScorePrefix.'Score'} == $MyRow->{$Opponent.$ScorePrefix.'Score'} && $MyRow->{$Prefix.'Tie'} > $MyRow->{$Opponent.'Tie'} ))) {
		$tmpWidth=$pdf->GetLineWidth();
		$pdf->SetLineWidth($tmpWidth*5);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+1,$WhereY[$WhichScore]+1,$WhereX[$WhichScore]+$GoldW+$pdf->ScoreCellHeight-1,$WhereY[$WhichScore]+$pdf->ScoreCellHeight-1);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+1,$WhereY[$WhichScore]+$pdf->ScoreCellHeight-1,$WhereX[$WhichScore]+$GoldW+$pdf->ScoreCellHeight-1,$WhereY[$WhichScore]+1);
		$pdf->SetLineWidth($tmpWidth);
	}
	$pdf->SetDefaultColor();
	$pdf->Cell($GoldW+2*$TotalW+$NumCol*$ArrowW,$pdf->ScoreCellHeight, get_text('Winner'),0,1,'L',0);
	$WhereY[$WhichScore]=$pdf->GetY();

// Row 2: Arrow numbers, totale, points, etc
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell($GoldW,$pdf->ScoreCellHeight,'',0,0,'C',0);
	for($j=0; $j<$NumCol; $j++) {
        $pdf->Cell($ArrowW,$pdf->ScoreCellHeight, ($j+1), 1, 0, 'C', 1);
	}
	$pdf->Cell($TotalW, $pdf->ScoreCellHeight, get_text(($MyRow->{'EvMatchMode'}==0 ? 'EndTotal':'SetTotal'),'Tournament'),1,0,'C',1);
	if($MyRow->{'EvMatchMode'}==0) {
		$pdf->Cell($TotalW+2*$GoldW, $pdf->ScoreCellHeight, get_text('RunningTotal','Tournament'),1,0,'C',1);
	} else {
		$pdf->Cell(2*$GoldW,$pdf->ScoreCellHeight,get_text('SetPoints', 'Tournament'),1,0,'C',1);
		$pdf->Cell($TotalW,$pdf->ScoreCellHeight,get_text('TotalPoints','Tournament'),1,0,'C',1);
	}
    if($MyRow->EvCheckGolds) {
		$pdf->Cell($GoldW,$pdf->ScoreCellHeight,$MyRow->EvGolds,1,0,'C',1);
    }
    if($MyRow->EvCheckXNines) {
		$pdf->Cell($GoldW,$pdf->ScoreCellHeight, $MyRow->EvXNine,1,0,'C',1);
    }
    $pdf->ln();
	$WhereY[$WhichScore]=$pdf->GetY();
//Righe
	$ScoreTotal = 0;
	$GoldsTotal = 0;
	$XNineTotal = 0;
	$SetTotal = 0;
	for($i=1; $i<=$NumRow; $i++) {
		$ScoreEndTotal = 0;
	   	$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
		$pdf->Cell($GoldW,$pdf->ScoreCellHeight,$i,1,0,'C',1);
		$pdf->SetFont($pdf->FontStd,'',10);

        $a=0;
        $b=0;
        $c=0;

        for($j=0; $j<$NumCol; $j++) {
            $pdf->Cell($ArrowW, $pdf->ScoreCellHeight, ($FillWithArrows ? DecodeFromLetter(substr($MyRow->{$Prefix . 'ArrowString'}, ($i - 1) * $NumCol + $j, 1)) : ''), 1, 0, 'C', 0);
        }
        list($a,$b,$c)=ValutaArrowStringGX(substr($MyRow->{$Prefix . 'ArrowString'}, ($i - 1) * $NumCol, $NumCol), $MyRow->EvGoldsChars,  $MyRow->EvXNineChars);
        $IsEndScore= trim(substr($MyRow->{$Prefix.'ArrowString'}, ($i-1)*$NumCol, $NumCol));
        $ScoreEndTotal = ValutaArrowString(substr($MyRow->{$Prefix.'ArrowString'},($i-1)*$NumCol,$NumCol));
        $ScoreTotal += $ScoreEndTotal;

		$pdf->SetFont($pdf->FontStd,'', ($MyRow->{'EvMatchMode'}==0 ? 10 : 12));
		$pdf->Cell($TotalW,$pdf->ScoreCellHeight,($FillWithArrows && $IsEndScore ? $ScoreEndTotal : ''),1,0,'C',0);

		if($MyRow->EvMatchMode==0) {
			$pdf->SetFont($pdf->FontStd,'', 12);
			$pdf->Cell($TotalW+2*$GoldW,$pdf->ScoreCellHeight,($FillWithArrows && $IsEndScore ? $ScoreTotal : ''),1,0,'C',0);
		} else {
			$SetTotSx = '';
			if($IsEndScore && $FillWithArrows) {
				$SetPointSx= ValutaArrowString(substr($MyRow->{$Prefix.'ArrowString'}, ($i-1)*$NumCol, $NumCol));
				$SetPointDx= ValutaArrowString(substr($MyRow->{$Opponent.'ArrowString'}, ($i-1)*$NumCol, $NumCol));

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
				$pdf->Circle($pdf->GetX()+$GoldW/3,$pdf->GetY()+$pdf->ScoreCellHeight/2, $GoldW/4, 0, 360, 'FD');
			}
			$pdf->Cell($GoldW*2/3,$pdf->ScoreCellHeight,'2',1, 0,'C',0);
			if($SetTotSx==1 && $FillWithArrows) {
				$pdf->Circle($pdf->GetX()+$GoldW/3,$pdf->GetY()+$pdf->ScoreCellHeight/2, $GoldW/4, 0, 360, 'FD');
			}
			$pdf->Cell($GoldW*2/3,$pdf->ScoreCellHeight,'1',1, 0,'C',0);
			if($SetTotSx==0 && $IsEndScore && $FillWithArrows) {
				$pdf->Circle($pdf->GetX()+$GoldW/3,$pdf->GetY()+$pdf->ScoreCellHeight/2, $GoldW/4, 0, 360, 'FD');
			}
			$pdf->Cell($GoldW*2/3,$pdf->ScoreCellHeight,'0',1, 0,'C',0);
			$pdf->Cell( $TotalW,$pdf->ScoreCellHeight,($IsEndScore && $FillWithArrows ? $SetTotal : ''),1, 0,'C',0);
		}
        if($MyRow->EvCheckGolds) {
            $pdf->Cell($GoldW,$pdf->ScoreCellHeight,$FillWithArrows && $IsEndScore ? ($b??'') : '', 1, 0,'C',0);
            $GoldsTotal += $b;
        }
        if($MyRow->EvCheckXNines) {
            $pdf->Cell($GoldW,$pdf->ScoreCellHeight, $FillWithArrows && $IsEndScore ? ($c??'') : '',1,0,'C',0);
            $XNineTotal += $c;
        }
        $pdf->ln();
		$WhereY[$WhichScore]=$pdf->GetY();
	}

//Shoot Off
	$closeToCenter=false;
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]+($pdf->ScoreCellHeight/4));
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell($GoldW,$pdf->ScoreCellHeight*($MyRow->EvElimType ? 11 : 23)/8,(get_text('TB')),1,0,'C',1);
	$ShootOffW=($tmp->so<=$NumCol ? $ArrowW : ($ArrowW*$NumCol)/$tmp->so);
	$StartX=$pdf->getX();
	for($i=0; $i<($MyRow->EvElimType ? 1 : 3); $i++) {
		$pdf->SetX($StartX);
		for($j=0; $j<$tmp->so; $j++) {
			$pdf->SetFont($pdf->FontStd,($tmp->so==1 ? 'B' : ''),10);
			$pdf->Cell($ShootOffW,$pdf->ScoreCellHeight*3/4,($FillWithArrows ? DecodeFromLetter(substr($MyRow->{$Prefix.'TieBreak'}, $i*$tmp->so + $j ,1)) : ''),1,0,'C',0);
			if($FillWithArrows AND $MyRow->{$Prefix.'TieClosest'} != 0) {
				$closeToCenter=true;
			}
		}
        if($tmp->so>1) {
            $pdf->SetX($pdf->GetX()+2);
            $pdf->SetFont($pdf->FontStd,'B',10);
            $pdf->Cell($TotalW-2, $pdf->ScoreCellHeight*3/4, (($FillWithArrows AND trim(substr($MyRow->{$Prefix.'TieBreak'}??'',$i*$tmp->so,$tmp->so))) ? ValutaArrowstring(substr($MyRow->{$Prefix.'TieBreak'},$i*$tmp->so,$tmp->so)) : ''), 1, 0, 'C', 0);
        }
        $pdf->ln();
	}
	if($MyRow->{$Prefix.'Tie'}==1) {
        $SetTotal++;
    }
	//if($NumCol>$j) {
	//	$pdf->Cell($ArrowW*($NumCol-$j),$pdf->ScoreCellHeight*3/4,'',0,0,'L',0);
	//}

//Totale
	$Errore=($FillWithArrows and (strlen($MyRow->{$Prefix.'ArrowString'}) and ($MyRow->{'EvMatchMode'} ? $MyRow->{$Prefix.'SetScore'}!=$SetTotal : $MyRow->{$Prefix.$ScorePrefix.'Score'}!=$ScoreTotal)));
	$pdf->SetXY($TopX=$StartX+$ArrowW*$NumCol,$WhereY[$WhichScore]);
	$pdf->SetFont($pdf->FontStd,'B',10);
	if($MyRow->EvMatchMode==0) {
		$pdf->Cell($TotalW,$pdf->ScoreCellHeight,get_text('Total'),0,0,'R',0);
		$pdf->SetFont($pdf->FontStd,'B',11);
		$pdf->Cell($TotalW+2*$GoldW,$pdf->ScoreCellHeight,($FillWithArrows ? $ScoreTotal : ''),1,0,'C',0);
		if($Errore) {
			$pdf->Line($x1 = $TopX+$TotalW, $y1=$pdf->gety()+$pdf->ScoreCellHeight, $x1+$TotalW, $y1-$pdf->ScoreCellHeight);
		}
		$pdf->SetFont($pdf->FontStd,'',10);
	} else {
		$pdf->Cell(2*$GoldW,$pdf->ScoreCellHeight,'',0,0,'R',0);
		$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->Cell($TotalW,$pdf->ScoreCellHeight,get_text('Total'),0,0,'R',0);
		$pdf->SetFont($pdf->FontStd,'B',14);
		$pdf->Cell($TotalW,$pdf->ScoreCellHeight,($FillWithArrows ? $MyRow->{$Prefix.'SetScore'} : ''),1,0,'C',0);
		if($Errore) {
			$pdf->Line($x1 = $TopX+$TotalW + $TotalW * 8/5, $y1=$pdf->gety()+$pdf->ScoreCellHeight, $x1 + 2/5*$TotalW, $y1-$pdf->ScoreCellHeight);
		}
	}

    if($MyRow->EvCheckGolds) {
        $pdf->Cell($GoldW,$pdf->ScoreCellHeight,$FillWithArrows ? ($GoldsTotal??'') : '', 1, 0,'C',0);
    }
    if($MyRow->EvCheckXNines) {
        $pdf->Cell($GoldW,$pdf->ScoreCellHeight, $FillWithArrows ? ($XNineTotal??'') : '',1,0,'C',0);
    }
    $pdf->ln();

	$WhereY[$WhichScore]=$pdf->GetY();

	if($Errore) {
		$pdf->SetX($TopX);
		$pdf->SetFont($pdf->FontStd,'B',11);
		$pdf->Cell($MyRow->EvMatchMode ? $TotalW + $TotalW * 8/5 : $TotalW, $pdf->ScoreCellHeight, (get_text('SignedTotal', 'Tournament') . " "), 0,0,'R',0);
		$pdf->Cell($MyRow->EvMatchMode ? 2/5*$TotalW : $TotalW, $pdf->ScoreCellHeight, $MyRow->{$Prefix.$ScorePrefix.'Score'}, 1, 0, 'C', 0);
		$pdf->ln();
	}

//Closet to the center
	$pdf->SetFont($pdf->FontStd,'',9);
	$pdf->SetXY($WhereX[$WhichScore]+$GoldW+$ShootOffW/2, $WhereY[$WhichScore]+$pdf->ScoreCellHeight*($MyRow->EvElimType ? 1 : 13)/8);
	$pdf->Cell($ShootOffW/2,$pdf->ScoreCellHeight/2,'',1,0,'R',0);
	if($closeToCenter) {
		$tmpWidth=$pdf->GetLineWidth();
		$pdf->SetLineWidth($tmpWidth*5);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+$ShootOffW/2-1,$WhereY[$WhichScore]+$pdf->ScoreCellHeight*($MyRow->EvElimType ? 1 : 13)/8-1,$WhereX[$WhichScore]+$GoldW+$ShootOffW+1,$WhereY[$WhichScore]+$pdf->ScoreCellHeight*(($MyRow->EvElimType ? 1 : 13)+4)/8+1);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+$ShootOffW/2-1,$WhereY[$WhichScore]+$pdf->ScoreCellHeight*(($MyRow->EvElimType ? 1 : 13)+4)/8+1,$WhereX[$WhichScore]+$GoldW+$ShootOffW+1,$WhereY[$WhichScore]+$pdf->ScoreCellHeight*($MyRow->EvElimType ? 1 : 13)/8-1);
		$pdf->SetLineWidth($tmpWidth);
	}
	$pdf->Cell($ArrowW*($NumCol-1),$pdf->ScoreCellHeight*2/4,get_text('Close2Center','Tournament'),0,0,'L',0);
	$WhereY[$WhichScore]=$pdf->GetY()+10;
//Firme Athletes/agents
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
   	$pdf->SetFont($pdf->FontStd,'i',8);
	$pdf->Cell($ScoreWidth,4,(get_text('ArcherSignature','Tournament')),'B',1,'L',0);

}

function get_winner($MatchNo, $Event) {
	$ret=array();
    $Bronze=($MatchNo==2 or $MatchNo==3);
    if($MatchNo<2) {
        $MatchNo+=2;
    }
	$q=safe_r_sql("select concat('(#', IndRank, ') ', ucase(EnFirstName), ' ', EnName, ' - ', CoCode) as Athlete, EnId, FinMatchNo, FinWinLose 
		from Finals 
	    left join (select EnId, EnTournament, EnFirstName, EnName, CoCode, IndRank
            from Entries 
            inner join Countries on CoId=EnCountry and CoTournament=EnTournament
            inner join Individuals on IndTournament=EnTournament and IndEvent='$Event' and IndId=EnId
            where EnTournament={$_SESSION['TourId']}
            ) Entries on EnId=FinAthlete and EnTournament=FinTournament
		where FinMatchNo in (".($MatchNo*2).", ".($MatchNo*2 + 1).") and FinEvent='$Event' and FinTournament={$_SESSION['TourId']} order by FinWinLose desc");
	while($r=safe_fetch($q)) {
		if($r->FinWinLose) {
			$ret[]=$r->Athlete;
			return $ret;
		}
		$ret=array_merge($ret, get_winner($r->FinMatchNo, $Event));
		if($r->EnId) {
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

