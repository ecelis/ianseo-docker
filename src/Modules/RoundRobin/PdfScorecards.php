<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
CheckTourSession(true);
checkACL(AclRobin, AclReadOnly);
require_once('Common/pdf/ResultPDF.inc.php');
// require_once('Common/Lib/CommonLib.php');
// require_once('Common/Globals.inc.php');
// require_once('Common/Fun_DB.inc.php');
// require_once('Common/Lib/Fun_Phases.inc.php');
// require_once('Common/Lib/Fun_FormatText.inc.php');
//require_once('HHT/Fun_HHT.local.inc.php');

$Team=intval($_REQUEST['team'] ?? -1);
$Events=($_REQUEST['events']??[]);
$Levels=($_REQUEST['levels']??[]);
$Groups=($_REQUEST['groups']??[]);
$Rounds=($_REQUEST['rounds']??[]);

if($Team==-1) {
	OutputError(get_text('ErrGenericError', 'Errors'));
}

$Options=[];
if($_SESSION['TourLocRule']=='LANC') {
    $Options=[
        'PrintLogo'=>false,
        'print_header'=>false,
        'print_footer'=>false,
    ];
}

$pdf = new ResultPDF(get_text('R-Session','Tournament'),false, '', true, $Options);
$pdf->setBarcodeHeader(empty($_REQUEST['Barcode']) ? '10' : '100');
$pdf->ScoreCellHeight=9;
$pdf->FillWithArrows=($_REQUEST['ScoreFilled']??0);
$pdf->PrintFlags=($_REQUEST['ScoreFlags']??0);

$MyQuery="";
if (isset($_REQUEST['Blank'])) {
	$MyQuery = "SELECT DISTINCT
		 	'' AS Event, '' as CountryName, '' as Athlete, '' as CountryCode, '' as EventDescr, '' as Target, '' as Position, -1 as Phase, EvMatchMode as EvMatchMode, EvElimType, '' as ArrowString, '' as Tie,
		 	'' as EnId, '' as OppEnId, '' as OppAthlete, '' as OppCountryCode, '' as OppCountryName, '' as OppTarget, '' as OppPosition, '' as OppArrowString, '' as OppTie, '' as QualRank, '' as OppQualRank
			FROM Events
			WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=0";
} else {

	$options=array(
		'tournament'=>$_SESSION['TourId']
	);

	if (isset($_REQUEST['schedule']) and $_REQUEST['schedule'] and $_REQUEST['schedule']!=-1) {
		$options['schedule']=$_REQUEST['schedule'];
		$OrderBy=true;
	} else {
		$OrderBy=false;
		$options['team']=$Team;
		if($Events) {
			$options['events']=$Events;
		}
		if($Levels) {
			$options['levels']=$Levels;
		}
		if($Groups) {
			$options['groups']=$Groups;
		}
		if($Rounds) {
			$options['rounds']=$Rounds;
		}
	}
	$rank=Obj_RankFactory::create('Robin', $options);

	$MyQuery = $rank->getQuery($OrderBy);
}
$Rs=safe_r_sql($MyQuery);
// Se il Recordset è valido e contiene almeno una riga
if (safe_num_rows($Rs)>0) {
	$defGoldW  = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(1/15);
	$defTotalW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(3/15);
	$defArrowTotW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(6/15);

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
		if(empty($_REQUEST["Blank"]) && empty($_REQUEST["IncEmpty"]) && (empty($MyRow->M1Athlete) || empty($MyRow->M2Athlete))) {
			// se è vuoto uno dei due arcieri e non è selezionata l'inclusione
			continue;
		}
		if(empty($MyRow->M1Athlete) and empty($MyRow->M2Athlete)) {
			continue;
		}

		if(empty($_REQUEST["IncEmpty"]) and (!$MyRow->M1Athlete or !$MyRow->M2Athlete)) {
			// skip if targets are not set!
			continue;
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
			$BarCode=mb_convert_encoding(implode('-', [$MyRow->M1MatchNo, $MyRow->M1Round, $MyRow->M1Group, $MyRow->M1Level, $MyRow->EvTeamEvent, $MyRow->EvCode]), "UTF-8","cp1252");
			$pdf->setxy($BarCodeX, $BarCodeY);
			$pdf->SetFont('barcode','',25);
			$pdf->SetFillColor(255);
			$pdf->Cell($pdf->BarcodeHeader, 10, '*' . $BarCode . "*",0,1,'C',1);
			$pdf->SetDefaultColor();
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->setxy($BarCodeX, $BarCodeY+10);
			$pdf->Cell($pdf->BarcodeHeader, 4, $BarCode,0,1,'C',0);
		}

		if(!empty($_REQUEST['QRCode'])) {
			foreach($_REQUEST['QRCode'] as $k => $Api) {
				require_once('Api/'.$Api.'/DrawQRCode.php');
				$Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
				$Function($pdf, $BarCodeX -(25 * ($k+1)), 5, $MyRow->EvCode, $MyRow->M1MatchNo, $MyRow->M1Level.'|'.$MyRow->M1Group.'|'.$MyRow->M1Round, 0, "R".($MyRow->M1Team ? 'T' : 'I'));
			}
		}
	}

	//END OF DrawScore
	safe_free_result($Rs);
}

$pdf->Output();

function DrawScore(&$pdf, $MyRow, $Side='L') {
	global $CFG, $defTotalW, $defGoldW, $defArrowTotW, $FollowingRows, $TrgOutdoor, $WhereStartX, $WhereStartY;
	if(isset($_REQUEST['Blank'])) {
		$MyRow->RrLevEnds=empty($_REQUEST['Rows'])?5:intval($_REQUEST['Rows']);
		$MyRow->RrLevArrows = empty($_REQUEST['Cols'])?3:intval($_REQUEST['Cols']);
		$MyRow->RrLevSO = empty($_REQUEST['SO'])?1:intval($_REQUEST['SO']);
	}
	$NumRow=$MyRow->RrLevEnds;
	$NumCol=$MyRow->RrLevArrows;
	$ArrowW = $defArrowTotW/$NumCol;
	$TotalW=$defTotalW;
	$GoldW=$defGoldW;

    $ScorePrefix='';
    if($MyRow->RrLevMatchMode) {
        $ScorePrefix='Set';
    }

    if($MyRow->Swapped) {
        $Prefix='1';
        $Opponent='2';

        if($Side=='L') {
            if($FollowingRows) {
                $pdf->AddPage();
            }
            $Prefix='2';
            $Opponent='1';
        }
    } else {
        $Prefix='2';
        $Opponent='1';

        if($Side=='L') {
            if($FollowingRows) {
                $pdf->AddPage();
            }
            $Prefix='1';
            $Opponent='2';
        }
    }

	$FollowingRows=true;
	$WhichScore=($Side=='R');
	$WhereX=$WhereStartX;
	$WhereY=$WhereStartY;
	//Intestazione Atleta
	$pdf->SetLeftMargin($WhereStartX[$WhichScore]);
	$pdf->SetY(35);
	// Flag of Country/Club
	if($pdf->PrintFlags) {
		if(is_file($file= $CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-Fl-'.$MyRow->{'CoShort'.$Prefix}.'.jpg')) {
			$H=12;
			$W=18;
			$OrgY=$pdf->gety();
			$OrgX=$NumCol*$ArrowW+$TotalW+$GoldW+$TotalW-18;
			$pdf->Image($file, $pdf->getx()+$OrgX, $OrgY, $W, $H, 'JPG', '', '', true, 300, '', false, false, 1, true);
			$FlagOffset=$W+1;
		}
	}

	$AthCell=6;
	$AthHeight=6;
	$RankHeight=12;
	// if(($MyRow->EnId and is_array($MyRow->EnId)) or ($MyRow->OppEnId and is_array($MyRow->OppEnId))) {
	// 	$AthCell=4.5;
	// 	if($MyRow->EnId and is_array($MyRow->EnId)) {
	// 		$AthHeight=$AthCell*count($MyRow->EnId);
	// 	} else {
	// 		$AthHeight=$AthCell*count($MyRow->OppEnId);
	// 	}
	// 	$WhereY[$WhichScore]=$WhereY[$WhichScore]+$AthHeight-6;
	// 	$RankHeight=6+$AthHeight;
	// }
	//error_reporting(E_ALL);

	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(20,$AthHeight,(get_text('Athlete')) . ': ', 'TL', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	if($MyRow->{"Athlete{$Prefix}"}) {
		$pdf->Cell($NumCol*$ArrowW+2*$TotalW+$GoldW-20-($pdf->PrintFlags?18:0),$AthHeight,($MyRow->{"Athlete{$Prefix}"}), 'T', 1, 'L', 0);
	} else {
		$pdf->Cell($NumCol*$ArrowW+2*$TotalW+$GoldW-20-($pdf->PrintFlags?18:0),$AthHeight,'', 'T', 1, 'L', 0);
	}
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(20,6,(get_text('Country')) . ': ', 'L', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($NumCol*$ArrowW+2*$TotalW+$GoldW-20,6, ($MyRow->{"CoName{$Prefix}"} . (strlen($MyRow->{"CoShort{$Prefix}"})>0 ?  ' (' . $MyRow->{"CoShort{$Prefix}"}  . ')' : '')), 0, 1, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'',10);
	$pdf->Cell(20,6,(get_text('DivisionClass')) . ': ', 'LB', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($NumCol*$ArrowW+$TotalW+$GoldW-20,6, get_text($MyRow->EvEventName,'','',true), 'B', 0, 'L', 0);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell($TotalW,6, (get_text('Target')) . ' ' . ltrim($MyRow->{"M{$Prefix}Target"},'0'), '1', 1, 'C', 1);

	// Rank number
	$pdf->SetXY($NumCol*$ArrowW+2*$TotalW+$GoldW+$WhereStartX[$WhichScore], 35);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->Cell(2*$GoldW,6, (get_text('Rank')),'TLR',1,'C',1);
	$pdf->SetXY($NumCol*$ArrowW+2*$TotalW+$GoldW+$WhereStartX[$WhichScore],$pdf->GetY());
	$pdf->SetFont($pdf->FontStd,'B',25);
	$pdf->Cell(2*$GoldW,$RankHeight, $MyRow->{"Rank{$Prefix}"},'BLR',1,'C',1);

	//Header
	$PhaseName=$MyRow->RrLevName.' '.$MyRow->RrGrName.' '.get_text('RoundNum', 'RoundRobin', $MyRow->M1Round);
	$pdf->SetFont($pdf->FontStd,'B',10);
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell($GoldW,$pdf->ScoreCellHeight,'',0,0,'C',0);
	$pdf->Cell(2*$GoldW+2*$TotalW+$NumCol*$ArrowW,$pdf->ScoreCellHeight, $PhaseName,1,0,'C',1);
	//Winner Checkbox
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->Cell(2*$GoldW,$pdf->ScoreCellHeight,'',0,0,'C',0);
	//$pdf->Rect($WhereX[$WhichScore]+$GoldW+2,$WhereY[$WhichScore]+2,$GoldW-4,$pdf->ScoreCellHeight-4,'DF',array(),array(255,255,255));
	$pdf->Rect($WhereX[$WhichScore]+$GoldW+2,$WhereY[$WhichScore]+2,$pdf->ScoreCellHeight-4,$pdf->ScoreCellHeight-4,'DF',array(),array(255,255,255));
	if($pdf->FillWithArrows && ($MyRow->{"M{$Prefix}{$ScorePrefix}Score"} > $MyRow->{"M{$Opponent}{$ScorePrefix}Score"} || ($MyRow->{"M{$Prefix}{$ScorePrefix}Score"} == $MyRow->{"M{$Opponent}{$ScorePrefix}Score"} && $MyRow->{"M{$Prefix}Tie"} > $MyRow->{"M{$Opponent}Tie"} ))) {
		$tmpWidth=$pdf->GetLineWidth();
		$pdf->SetLineWidth($tmpWidth*5);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+1,$WhereY[$WhichScore]+1,$WhereX[$WhichScore]+2*$GoldW-1,$WhereY[$WhichScore]+$pdf->ScoreCellHeight-1);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+1,$WhereY[$WhichScore]+$pdf->ScoreCellHeight-1,$WhereX[$WhichScore]+2*$GoldW-1,$WhereY[$WhichScore]+1);
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
	$pdf->Cell($TotalW, $pdf->ScoreCellHeight, get_text(($MyRow->{'RrLevMatchMode'}==0 ? 'EndTotal':'SetTotal'),'Tournament'),1,0,'C',1);


	if($MyRow->{'RrLevMatchMode'}==0) {
		$pdf->Cell($TotalW+2*$GoldW, $pdf->ScoreCellHeight, get_text('RunningTotal','Tournament'),1,1,'C',1);
	} else {
		$pdf->Cell(2*$GoldW,$pdf->ScoreCellHeight,get_text('SetPoints', 'Tournament'),1,0,'C',1);
		$pdf->Cell($TotalW,$pdf->ScoreCellHeight,get_text('TotalPoints','Tournament'),1,1,'C',1);
	}
	$WhereY[$WhichScore]=$pdf->GetY();
	//Righe
	$ScoreTotal = 0;
	$SetTotal = '';
	for($i=1; $i<=$NumRow; $i++)
	{
		$ScoreEndTotal = 0;
		$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
		$pdf->Cell($GoldW,$pdf->ScoreCellHeight,$i,1,0,'C',1);
		$pdf->SetFont($pdf->FontStd,'',10);

		for($j=0; $j<$NumCol; $j++) {
			$pdf->Cell($ArrowW, $pdf->ScoreCellHeight, ($pdf->FillWithArrows ? DecodeFromLetter(substr($MyRow->{"M{$Prefix}Arrowstring"}, ($i - 1) * $NumCol + $j, 1)) : ''), 1, 0, 'C', 0);
		}
		$IsEndScore= trim(substr($MyRow->{"M{$Prefix}Arrowstring"}, ($i-1)*$NumCol, $NumCol));
		$ScoreEndTotal = ValutaArrowString(substr($MyRow->{"M{$Prefix}Arrowstring"},($i-1)*$NumCol,$NumCol));
		$ScoreTotal += $ScoreEndTotal;
		$pdf->SetFont($pdf->FontStd,'', ($MyRow->{'RrLevMatchMode'}==0 ? 10 : 12));
		$pdf->Cell($TotalW,$pdf->ScoreCellHeight,($pdf->FillWithArrows && $IsEndScore ? $ScoreEndTotal : ''),1,0,'C',0);

		//$pdf->Cell($TotalW* ($MyRow->{'RrLevMatchMode'}==0 ? 1:4/5),$pdf->ScoreCellHeight,($pdf->FillWithArrows && $IsEndScore ? $ScoreTotal : ''),1,0,'C',0);
		if($MyRow->{'RrLevMatchMode'}==0) {
			$pdf->SetFont($pdf->FontStd,'', 12);
			$pdf->Cell($TotalW+2*$GoldW,$pdf->ScoreCellHeight,($pdf->FillWithArrows && $IsEndScore ? $ScoreTotal : ''),1,1,'C',0);
		} else {
			$SetTotSx = '';
			if($IsEndScore && $pdf->FillWithArrows) {
				$SetPointSx= ValutaArrowString(substr($MyRow->{"M{$Prefix}Arrowstring"}, ($i-1)*$NumCol, $NumCol));
				$SetPointDx= ValutaArrowString(substr($MyRow->{"M{$Opponent}Arrowstring"}, ($i-1)*$NumCol, $NumCol));

				if($SetPointSx > $SetPointDx) {
					$SetTotSx= 2;
				} elseif($SetPointSx < $SetPointDx) {
					$SetTotSx= 0;
				} else {
					$SetTotSx= 1;
				}
				$SetTotal = intval($SetTotal) + $SetTotSx;
			}

			$pdf->SetFont($pdf->FontStd,'B',11);
			if($SetTotSx==2 && $pdf->FillWithArrows) {
				$pdf->Circle($pdf->GetX()+$GoldW/3,$pdf->GetY()+$pdf->ScoreCellHeight/2, $GoldW/4, 0, 360, 'FD');
			}
			$pdf->Cell($GoldW*2/3,$pdf->ScoreCellHeight,'2',1, 0,'C',0);
			if($SetTotSx==1 && $pdf->FillWithArrows) {
				$pdf->Circle($pdf->GetX()+$GoldW/3,$pdf->GetY()+$pdf->ScoreCellHeight/2, $GoldW/4, 0, 360, 'FD');
			}
			$pdf->Cell($GoldW*2/3,$pdf->ScoreCellHeight,'1',1, 0,'C',0);
			if($SetTotSx==0 && $IsEndScore && $pdf->FillWithArrows) {
				$pdf->Circle($pdf->GetX()+$GoldW/3,$pdf->GetY()+$pdf->ScoreCellHeight/2, $GoldW/4, 0, 360, 'FD');
			}
			$pdf->Cell($GoldW*2/3,$pdf->ScoreCellHeight,'0',1, 0,'C',0);
			$pdf->Cell( $TotalW,$pdf->ScoreCellHeight,($IsEndScore && $pdf->FillWithArrows ? $SetTotal : ''),1, 1,'C',0);


		}
		$WhereY[$WhichScore]=$pdf->GetY();
	}

	//Shoot Off
	$closeToCenter=false;
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]+($pdf->ScoreCellHeight/4));
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell($GoldW,$pdf->ScoreCellHeight*(23)/8,(get_text('TB')),1,0,'C',1);
	$ShootOffW=($MyRow->RrLevSO<=$NumCol ? $ArrowW : ($ArrowW*$NumCol)/$MyRow->RrLevSO);
	$StartX=$pdf->getX();
	for($i=0; $i<3; $i++) {
		$pdf->SetX($StartX);
		for($j=0; $j<$MyRow->RrLevSO; $j++) {
			$pdf->SetXY($pdf->GetX()+0.5,$pdf->GetY());
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell($ShootOffW-0.5,$pdf->ScoreCellHeight*3/4,($pdf->FillWithArrows ? DecodeFromLetter(substr($MyRow->{"M{$Prefix}Tiebreak"}, $i*$MyRow->RrLevSO + $j ,1)) : ''),1,0,'C',0);
			if($pdf->FillWithArrows AND $MyRow->{"M{$Prefix}TbClosest"} != 0) {
				$closeToCenter=true;
			}
		}
		$pdf->ln();
	}
	if($MyRow->{"M{$Prefix}Tie"}==1) $SetTotal++;
	//if($NumCol>$j) {
	//	$pdf->Cell($ArrowW*($NumCol-$j),$pdf->ScoreCellHeight*3/4,'',0,0,'L',0);
	//}

	//Totale
	$Errore=($pdf->FillWithArrows and (strlen($MyRow->{"M{$Prefix}Arrowstring"}) and ($MyRow->{'RrLevMatchMode'} ? $MyRow->{"M{$Prefix}SetScore"}!=$SetTotal : $MyRow->{"M{$Prefix}{$ScorePrefix}Score"}!=$ScoreTotal)));
	$pdf->SetXY($TopX=$StartX+$ArrowW*$NumCol,$WhereY[$WhichScore]);
	$pdf->SetFont($pdf->FontStd,'B',10);
	if($MyRow->{'RrLevMatchMode'}==0) {
		$pdf->Cell($TotalW,$pdf->ScoreCellHeight,get_text('Total'),0,0,'R',0);
		$pdf->SetFont($pdf->FontStd,'B',11);
		$pdf->Cell($TotalW+2*$GoldW,$pdf->ScoreCellHeight,($pdf->FillWithArrows ? $ScoreTotal : ''),1,1,'C',0);
		if($Errore) {
			$pdf->Line($x1 = $TopX+$TotalW, $y1=$pdf->gety()+$pdf->ScoreCellHeight, $x1+$TotalW, $y1-$pdf->ScoreCellHeight);
		}
		$pdf->SetFont($pdf->FontStd,'',10);
	} else {
		$pdf->Cell($TotalW,$pdf->ScoreCellHeight,'',0,0,'R',0);
		$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->Cell(2*$GoldW,$pdf->ScoreCellHeight,get_text('Total'),0,0,'R',0);
		$pdf->SetFont($pdf->FontStd,'B',14);
		$pdf->Cell($TotalW,$pdf->ScoreCellHeight,($pdf->FillWithArrows ? $MyRow->{"M{$Prefix}SetScore"} : ''),1,1,'C',0);
		if($Errore) {
			$pdf->Line($x1 = $TopX+2*$GoldW + $TotalW * 8/5, $y1=$pdf->gety()+$pdf->ScoreCellHeight, $x1 + 2/5*$TotalW, $y1-$pdf->ScoreCellHeight);
		}
	}

	$WhereY[$WhichScore]=$pdf->GetY();

	if($Errore) {
		$pdf->SetX($TopX);
		$pdf->SetFont($pdf->FontStd,'B',11);
		$pdf->Cell($MyRow->{'RrLevMatchMode'} ? 2*$GoldW + $TotalW * 8/5 : $TotalW, $pdf->ScoreCellHeight, (get_text('SignedTotal', 'Tournament') . " "), 0,0,'R',0);
		$pdf->Cell($MyRow->{'RrLevMatchMode'} ? 2/5*$TotalW : $TotalW, $pdf->ScoreCellHeight, $MyRow->{"M{$Prefix}{$ScorePrefix}Score"}, 1, 0, 'C', 0);
		$pdf->ln();
	}

	//Closet to the center
	$pdf->SetFont($pdf->FontStd,'',9);
	$pdf->SetXY($WhereX[$WhichScore]+$GoldW+$ShootOffW/2, $WhereY[$WhichScore]+$pdf->ScoreCellHeight*(13)/8);
	$pdf->Cell($ShootOffW/2,$pdf->ScoreCellHeight/2,'',1,0,'R',0);
	if($closeToCenter)
	{
		$tmpWidth=$pdf->GetLineWidth();
		$pdf->SetLineWidth($tmpWidth*5);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+$ShootOffW/2-1,$WhereY[$WhichScore]+$pdf->ScoreCellHeight*( 13)/8-1,$WhereX[$WhichScore]+$GoldW+$ShootOffW+1,$WhereY[$WhichScore]+$pdf->ScoreCellHeight*(( 13)+4)/8+1);
		$pdf->Line($WhereX[$WhichScore]+$GoldW+$ShootOffW/2-1,$WhereY[$WhichScore]+$pdf->ScoreCellHeight*(( 13)+4)/8+1,$WhereX[$WhichScore]+$GoldW+$ShootOffW+1,$WhereY[$WhichScore]+$pdf->ScoreCellHeight*( 13)/8-1);
		$pdf->SetLineWidth($tmpWidth);
	}
	$pdf->Cell($ArrowW*($NumCol-1),$pdf->ScoreCellHeight*2/4,get_text('Close2Center','Tournament'),0,0,'L',0);
	$WhereY[$WhichScore]=$pdf->GetY()+10;
	//Firme Athletes/agents
	$pdf->SetXY($WhereX[$WhichScore],$WhereY[$WhichScore]);
	$pdf->SetFont($pdf->FontStd,'I',7);
	$pdf->Cell(3*$GoldW+2*$TotalW+$NumCol*$ArrowW,4,(get_text('ArcherSignature','Tournament')),'B',1,'L',0);

}

function get_winner($MatchNo, $Event) {
	$ret=array();
	$q=safe_r_sql("select concat(ucase(EnFirstName), ' ', EnName, ' - ', CoCode) as Athlete, EnId, FinMatchNo, FinWinLose 
		from Finals 
	    left join Entries on EnId=FinAthlete and FinEvent='$Event' and EnTournament=FinTournament
		left join Countries on CoId=EnCountry and CoTournament=FinTournament
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
	return $ret;
}

