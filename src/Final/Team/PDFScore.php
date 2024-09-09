<?php
	require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/pdf/ResultPDF.inc.php');
	require_once('Common/Fun_FormatText.inc.php');
	require_once('Common/Fun_Phases.inc.php');
    checkACL(AclTeams, AclReadOnly);

    if(!empty($_REQUEST['QRCode'])) {
        foreach ($_REQUEST['QRCode'] as $k => $Api) {
            require_once('Api/' . $Api . '/DrawQRCode.php');
        }
    }

	$pdf = new ResultPDF((get_text('IndFinal')));
	$pdf->setlinewidth(0.1);

    $Fasi = array(get_text('64_Phase'),get_text('32_Phase'), get_text('16_Phase'), get_text('8_Phase'), get_text('4_Phase'), get_text('2_Phase'), get_text('ScoreFinalMatch', 'Tournament'));
    $TgtNoFasi = array('s64','s32', 's16', 's8', 's4', 's2', 'sGo');
    $ByeFasi = array('b64', 'b32', 'b16', 'b8', 'b4', 'b2', 'bBr');
    $MatchFasi = array('m64', 'm32', 'm16', 'm8', 'm4', 'm2', 'mBr', 'mGo');
    $OppArray = array('op64', 'op32', 'op16', 'op8', 'op4', 'op2', 'opB', 'opG');
    $NumFasi = array(64, 32, 16, 8, 4, 2, 1);

	$Start2FirstPhase=array();
	$q=safe_r_sql("select PhId, greatest(PhId, PhLevel) as FullLevel from Phases where PhRuleSets in ('', '{$_SESSION['TourLocRule']}') order by PhId desc");
	while($r=safe_fetch($q)) {
		$Start2FirstPhase[$r->PhId]=(($place=array_search($r->FullLevel, $NumFasi))===false ? 6 : $place);
	}

	$MyQuery="";
	if (isset($_REQUEST['Blank'])) {
		$model= empty($_REQUEST['Model'])?'':$_REQUEST['Model'];
		$MyQuery = "SELECT EvCode, '' AS EvEventName, EvMixedTeam,  EvFinalFirstPhase, EvMatchMode, EvMatchArrowsNo, "
        . " '' AS GrPosition, '' AS CoCode, '' AS TeamName, EvMaxTeamPerson, 0 as isBye, '' AS TeamComponents,"
        . " '' AS s64,'' AS s32, '' AS s16, '' AS s8, '' AS s4, '' AS s2, '' AS sBr, '' AS sGo, "
        . " '' AS b64,'' AS b32, '' AS b16, '' AS b8, '' AS b4, '' AS b2, '' AS bBr, '' AS bGo, "
        . " '' AS m64,'' AS m32, '' AS m16, '' AS m8, '' AS m4, '' AS m2, '' AS mBr, '' AS mGo, "
        . " '' op64, '' op32, '' op16, '' op8, '' op4, '' op2, '' opB, '' opG, "
        . " EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO"
        . " from Events where ".($model ? "EvCode='$model' and" : '')." EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 limit 1";
    } else {
		$Events=array();
		if(!empty($_REQUEST['Event'])) {
			if(!is_array($_REQUEST['Event'])) $_REQUEST['Event']=array($_REQUEST['Event']);
			foreach($_REQUEST['Event'] as $Event) {
				if(preg_match('//', $Event)) $Events[]=strSafe_DB($Event);
			}
			sort($Events);
		}

		$TmpJoinType='INNER';
		if(isset($_REQUEST["IncEmpty"]) && $_REQUEST["IncEmpty"]==1)
			$TmpJoinType='LEFT';
		$MyQuery = 'SELECT '
			. ' EvCode, EvMatchMode, EvMatchArrowsNo, EvEventName, EvMixedTeam, EvFinalFirstPhase, GrPosition, '
			. " CoCode, CONCAT(CoName, IF(f.TfSubTeam>1,CONCAT(' (',f.TfSubTeam,')'),'')) AS TeamName, EvMaxTeamPerson, (f.TfTie=2) as isBye, TeamComponents, "
            . ' NULLIF(s64.FSLetter,\'\') s64,NULLIF(s32.FSLetter,\'\') s32, NULLIF(s16.FSLetter,\'\') s16, NULLIF(s8.FSLetter,\'\') s8, NULLIF(s4.FSLetter,\'\') s4, NULLIF(s2.FSLetter,\'\') s2, NULLIF(sb.FSLetter,\'\') sBr, NULLIF(sg.FSLetter,\'\') sGo, '
            . ' f64.TfTie=2 b64, f32.TfTie=2 b32, f16.TfTie=2 b16, f8.TfTie=2 b8, f4.TfTie=2 b4, f2.TfTie=2 b2, fb.TfTie=2 bBr, fg.TfTie=2 bGo, '
            . ' f64.TfMatchNo m64, f32.TfMatchNo m32, f16.TfMatchNo m16, f8.TfMatchNo m8, f4.TfMatchNo m4, f2.TfMatchNo m2, fb.TfMatchNo mBr, fg.TfMatchNo mGo, '
            . ' op64, op32, op16, op8, op4, op2, opB, opG, '
            . " EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO"
            . ' FROM Events '
            . ' INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0 '
            . ' INNER JOIN TeamFinals as f ON EvCode=TfEvent AND EvTournament=f.TfTournament '
            . ' INNER JOIN Grids ON TfMatchNo=GrMatchNo AND GrPhase=greatest(PhId, PhLevel) '
            . ' ' . $TmpJoinType . ' JOIN Countries on TfTeam=CoId AND TfTournament=CoTournament '
            . " LEFT JOIN (select TfcCoId, TfcSubTeam, TfcTournament, TfcEvent, GROUP_CONCAT(if(EnNameOrder=1, concat(ucase(EnFirstName), ' ', EnName), concat(EnName, ' ', ucase(EnFirstName))) ORDER BY TfcOrder SEPARATOR ', ') as TeamComponents FROM TeamFinComponent inner join Entries on EnId=TfcId and EnTournament=TfcTournament GROUP BY TfcCoId, TfcSubTeam, TfcTournament, TfcEvent) TC ON  TfTeam=TfcCoId AND TfSubTeam=TfcSubTeam AND TfTournament=TfcTournament AND TfEvent=TfcEvent"
            . ' LEFT JOIN FinSchedule s64 ON EvCode=s64.FSEvent AND EvTeamEvent=s64.FSTeamEvent AND EvTournament=s64.FSTournament AND IF(GrPhase=64, TfMatchNo, -256)=s64.FSMatchNo'
            . ' LEFT JOIN FinSchedule s32 ON EvCode=s32.FSEvent AND EvTeamEvent=s32.FSTeamEvent AND EvTournament=s32.FSTournament AND IF(GrPhase=32,TfMatchNo,FLOOR(s64.FSMatchNo/2))=s32.FSMatchNo'
            . ' LEFT JOIN FinSchedule s16 ON EvCode=s16.FSEvent AND EvTeamEvent=s16.FSTeamEvent AND EvTournament=s16.FSTournament AND IF(GrPhase=16,TfMatchNo,FLOOR(s32.FSMatchNo/2))=s16.FSMatchNo'
            . ' LEFT JOIN FinSchedule s8 ON EvCode=s8.FSEvent AND EvTeamEvent=s8.FSTeamEvent AND EvTournament=s8.FSTournament AND IF(GrPhase=8,TfMatchNo,FLOOR(s16.FSMatchNo/2))=s8.FSMatchNo'
            . ' LEFT JOIN FinSchedule s4 ON EvCode=s4.FSEvent AND EvTeamEvent=s4.FSTeamEvent AND EvTournament=s4.FSTournament AND IF(GrPhase=4,TfMatchNo,FLOOR(s8.FSMatchNo/2))=s4.FSMatchNo'
            . ' LEFT JOIN FinSchedule s2 ON EvCode=s2.FSEvent AND EvTeamEvent=s2.FSTeamEvent AND EvTournament=s2.FSTournament AND IF(GrPhase=2,TfMatchNo,FLOOR(s4.FSMatchNo/2))=s2.FSMatchNo'
            . ' LEFT JOIN FinSchedule sb ON EvCode=sb.FSEvent AND EvTeamEvent=sb.FSTeamEvent AND EvTournament=sb.FSTournament AND FLOOR(s2.FSMatchNo/2)=sb.FSMatchNo'
            . ' LEFT JOIN FinSchedule sg ON EvCode=sg.FSEvent AND EvTeamEvent=sg.FSTeamEvent AND EvTournament=sg.FSTournament AND FLOOR(s2.FSMatchNo/2)-2=sg.FSMatchNo'

            . ' LEFT JOIN TeamFinals f64 ON EvCode=f64.TfEvent AND EvTournament=f64.TfTournament AND IF(GrPhase=64,f.TfMatchNo,-256)=f64.TfMatchNo'
            . ' LEFT JOIN TeamFinals f32 ON EvCode=f32.TfEvent AND EvTournament=f32.TfTournament AND IF(GrPhase=32,f.TfMatchNo,FLOOR(f64.TfMatchNo/2))=f32.TfMatchNo'
            . ' LEFT JOIN TeamFinals f16 ON EvCode=f16.TfEvent AND EvTournament=f16.TfTournament AND IF(GrPhase=16,f.TfMatchNo,FLOOR(f32.TfMatchNo/2))=f16.TfMatchNo'
            . ' LEFT JOIN TeamFinals  f8 ON EvCode= f8.TfEvent AND EvTournament= f8.TfTournament AND IF(GrPhase= 8,f.TfMatchNo,FLOOR(f16.TfMatchNo/2))= f8.TfMatchNo'
            . ' LEFT JOIN TeamFinals  f4 ON EvCode= f4.TfEvent AND EvTournament= f4.TfTournament AND IF(GrPhase= 4,f.TfMatchNo,FLOOR( f8.TfMatchNo/2))= f4.TfMatchNo'
            . ' LEFT JOIN TeamFinals  f2 ON EvCode= f2.TfEvent AND EvTournament= f2.TfTournament AND IF(GrPhase= 2,f.TfMatchNo,FLOOR( f4.TfMatchNo/2))= f2.TfMatchNo'
            . ' LEFT JOIN TeamFinals  fb ON EvCode= fb.TfEvent AND EvTournament= fb.TfTournament AND FLOOR(f2.TfMatchNo/2)=fb.TfMatchNo'
            . ' LEFT JOIN TeamFinals  fg ON EvCode= fg.TfEvent AND EvTournament= fg.TfTournament AND FLOOR(f2.TfMatchNo/2)-2=fg.TfMatchNo'

            . " LEFT JOIN (select TfMatchNo, TfEvent, TfTournament,CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as op64 from TeamFinals inner join Countries on CoId=TfTeam and CoTournament=TfTournament) opp64 ON opp64.TfEvent=EvCode AND opp64.TfEvent=EvTournament AND opp64.TfEvent=if(f64.TfMatchNo%2=1, f64.TfMatchNo-1, f64.TfMatchNo+1)"
            . " LEFT JOIN (select TfMatchNo, TfEvent, TfTournament,CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as op32 from TeamFinals inner join Countries on CoId=TfTeam and CoTournament=TfTournament) opp32 ON opp32.TfEvent=EvCode AND opp32.TfEvent=EvTournament AND opp32.TfEvent=if(f32.TfMatchNo%2=1, f32.TfMatchNo-1, f32.TfMatchNo+1)"
            . " LEFT JOIN (select TfMatchNo, TfEvent, TfTournament,CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as op16 from TeamFinals inner join Countries on CoId=TfTeam and CoTournament=TfTournament) opp16 ON opp16.TfEvent=EvCode AND opp16.TfEvent=EvTournament AND opp16.TfEvent=if(f16.TfMatchNo%2=1, f16.TfMatchNo-1, f16.TfMatchNo+1)"
            . " LEFT JOIN (select TfMatchNo, TfEvent, TfTournament,CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as  op8 from TeamFinals inner join Countries on CoId=TfTeam and CoTournament=TfTournament) opp8  ON  opp8.TfEvent=EvCode AND  opp8.TfEvent=EvTournament AND  opp8.TfEvent=if( f8.TfMatchNo%2=1,  f8.TfMatchNo-1,  f8.TfMatchNo+1)"
            . " LEFT JOIN (select TfMatchNo, TfEvent, TfTournament,CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as  op4 from TeamFinals inner join Countries on CoId=TfTeam and CoTournament=TfTournament) opp4  ON  opp4.TfEvent=EvCode AND  opp4.TfEvent=EvTournament AND  opp4.TfEvent=if( f4.TfMatchNo%2=1,  f4.TfMatchNo-1,  f4.TfMatchNo+1)"
            . " LEFT JOIN (select TfMatchNo, TfEvent, TfTournament,CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as  op2 from TeamFinals inner join Countries on CoId=TfTeam and CoTournament=TfTournament) opp2  ON  opp2.TfEvent=EvCode AND  opp2.TfEvent=EvTournament AND  opp2.TfEvent=if( f2.TfMatchNo%2=1,  f2.TfMatchNo-1,  f2.TfMatchNo+1)"
            . " LEFT JOIN (select TfMatchNo, TfEvent, TfTournament,CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as  opB from TeamFinals inner join Countries on CoId=TfTeam and CoTournament=TfTournament) oppb  ON  oppb.TfEvent=EvCode AND  oppb.TfEvent=EvTournament AND  oppb.TfEvent=if( fb.TfMatchNo%2=1,  fb.TfMatchNo-1,  fb.TfMatchNo+1)"
            . " LEFT JOIN (select TfMatchNo, TfEvent, TfTournament,CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as  opG from TeamFinals inner join Countries on CoId=TfTeam and CoTournament=TfTournament) oppg  ON  oppg.TfEvent=EvCode AND  oppg.TfEvent=EvTournament AND  oppg.TfEvent=if( fg.TfMatchNo%2=1,  fg.TfMatchNo-1,  fg.TfMatchNo+1)"

            . ' WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=1 ';
        if($Events) {
            $MyQuery .= "AND EvCode in (" . implode(',', $Events) . ") ";
        }
		$MyQuery .= ' ORDER BY EvCode, f.TfMatchNo';
	}
	$Rs=safe_r_sql($MyQuery);
// Se il Recordset è valido e contiene almeno una riga
	if (safe_num_rows($Rs)>0) {
        $divider = (empty($_REQUEST['Barcode']) ? 13 : 14);

		$defArrowTotW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(6/$divider);// 1 time;
		$defTotalW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(3/$divider);// 2 times;
		$defGoldW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(1/$divider);// 1 time;
        $defBCodeW = ($pdf->GetPageWidth()-3*$pdf->getSideMargin())/2*(1/$divider);// 1 time;
		$TopPage=50;
		$ScoreHeight=($pdf->GetPageHeight()-$TopPage-35)/3;

		$WhereStartX=array($pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2,$pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2,$pdf->getSideMargin(),$pdf->GetPageWidth()/2+$pdf->getSideMargin()/2);
		$WhereStartY=array($TopPage, $TopPage, $TopPage+5+$ScoreHeight, $TopPage+5+$ScoreHeight, $TopPage+10+$ScoreHeight*2, $TopPage+10+$ScoreHeight*2);
		$WhereX=NULL;
		$WhereY=NULL;
		$RowNo=0;
		while($MyRow=safe_fetch($Rs)) {
			// sets the corrects headers based on the Events...
			$Fasi = array(get_text('64_Phase'),get_text('32_Phase'), get_text('16_Phase'), get_text('8_Phase'), get_text('4_Phase'), get_text('2_Phase'), get_text('ScoreFinalMatch', 'Tournament'));

			switch($MyRow->EvFinalFirstPhase) {
				case 64:
					$Fasi[0]=get_text('64_Phase');
					$Fasi[1]=get_text('32_Phase');
					break;
				case 48:
					$Fasi[0]=get_text('48_Phase');
					$Fasi[1]=get_text('24_Phase');
					break;
				default:
					$Fasi[$Start2FirstPhase[$MyRow->EvFinalFirstPhase]]=get_text($MyRow->EvFinalFirstPhase.'_Phase');
			}

			if($RowNo++ != 0) {
                $pdf->AddPage();
            }
			$WhereX=$WhereStartX;
			$WhereY=$WhereStartY;
//Intestazione Squadra
			$pdf->SetY(30);
		   	$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell(($pdf->GetPageWidth()-$pdf->getSideMargin())*0.1,5,(get_text('Team')) . ': ','TL',0,'L',0);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell(($pdf->GetPageWidth()-$pdf->getSideMargin())*0.8,5, ($MyRow->TeamName . (strlen($MyRow->CoCode??'')>0 ?  ' (' . $MyRow->CoCode  . ')' : '')),'TR',1,'L',0);
            $pdf->SetFont($pdf->FontStd,'',10);
            $pdf->Cell(($pdf->GetPageWidth()-$pdf->getSideMargin())*0.1,5,	get_text('TeamComponents') . ': ','L',0,'L',0);
            $pdf->SetFont($pdf->FontStd,'B',10);
            $pdf->Cell(($pdf->GetPageWidth()-$pdf->getSideMargin())*0.8,5, $MyRow->TeamComponents??'',0,1,'L',0);
			$pdf->SetFont($pdf->FontStd,'',10);
			$pdf->Cell(($pdf->GetPageWidth()-$pdf->getSideMargin())*0.1,5,(get_text('DivisionClass')) . ': ','LB',0,'L',0);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell(($pdf->GetPageWidth()-$pdf->getSideMargin())*0.8,5, get_text($MyRow->EvEventName,'','',true),'B',1,'L',0);

			$pdf->SetXY(($pdf->GetPageWidth()-$pdf->getSideMargin())*0.9,33);
			$pdf->SetFont($pdf->FontStd,'B',25);
			$pdf->Cell(($pdf->GetPageWidth()-$pdf->getSideMargin())*0.1,12, ($MyRow->GrPosition),'BLR',1,'C',1);
			$pdf->SetXY(($pdf->GetPageWidth()-$pdf->getSideMargin())*0.9,30);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell(($pdf->GetPageWidth()-$pdf->getSideMargin())*0.1,5, (get_text('Rank')),'TLR',1,'C',1);

			$WhichScoreEnd=7;
			if ($MyRow->EvFinalFirstPhase==64 || $MyRow->EvFinalFirstPhase==48) {
                $WhichScoreEnd = 6;
            }
            $cnt = $WhichScoreEnd-$Start2FirstPhase[$MyRow->EvFinalFirstPhase] + ($NumFasi[$WhichScoreEnd-1] == 1 ? 1:0);
            $bcodeCnt = 0;
			for($WhichScore = $Start2FirstPhase[$MyRow->EvFinalFirstPhase];$WhichScore<$WhichScoreEnd;$WhichScore++) {
                $MyRow->{$MatchFasi[$WhichScore]} = (intval($MyRow->{$MatchFasi[$WhichScore]}) % 2 == 1 ? $MyRow->{$MatchFasi[$WhichScore]} - 1 : $MyRow->{$MatchFasi[$WhichScore]});
				DrawScore($pdf, $MyRow, $WhichScore, $WhereX[$WhichScore-$Start2FirstPhase[$MyRow->EvFinalFirstPhase]], $WhereY[$WhichScore- $Start2FirstPhase[$MyRow->EvFinalFirstPhase]], $WhichScore==6);
                if(!empty($_REQUEST['QRCode'])) {
                    foreach($_REQUEST['QRCode'] as $k => $Api) {
                        $bcodeX = ($pdf->GetPageWidth()-$pdf->getSideMargin())*0.9-(15 * $cnt--);
                        $bcodeY = 30;
                        $bcodeSmall = true;
                        if($WhichScoreEnd-$Start2FirstPhase[$MyRow->EvFinalFirstPhase]<=5) {
                            $bcodeX = $WhereX[5] + (($bcodeCnt % 3) * 30) ;
                            $bcodeY = $WhereY[5] + (intval($bcodeCnt++ / 3) * 35  + 5);
                            $bcodeSmall = false;
                        }
                        $Function='DrawQRCode_'.preg_replace('/[^a-z0-9]/sim', '_', $Api);
                        $Function($pdf, $bcodeX, $bcodeY, $MyRow->EvCode, $MyRow->{$MatchFasi[$WhichScore]}, $NumFasi[$WhichScore], 0, "MT", false, '', $bcodeSmall);
                        $pdf->SetXY($bcodeX, $bcodeY-4);
                        $pdf->SetFont($pdf->FontStd,'',8);
                        $pdf->Cell(($bcodeSmall ? 15 : 25),3,$NumFasi[$WhichScore]==1 ? get_text('1_Phase') : $Fasi[$WhichScore],0,0,'C');
                        if($NumFasi[$WhichScore]==1) {
                            $bcodeX = ($pdf->GetPageWidth()-$pdf->getSideMargin())*0.9-(15 * $cnt);
                            if(!$bcodeSmall) {
                                $bcodeX = $WhereX[5] + (($bcodeCnt % 3) * 30) ;
                                $bcodeY = $WhereY[5] + (intval($bcodeCnt++ / 3) * 35  + 5);
                            }
                            $Function($pdf, $bcodeX, $bcodeY, $MyRow->EvCode, 0, 0, 0, "MT", false, '', $bcodeSmall);
                            $pdf->SetXY($bcodeX, $bcodeY-4);
                            $pdf->SetFont($pdf->FontStd,'',8);
                            $pdf->Cell(($bcodeSmall ? 15 : 25),3,get_text('0_Phase'),0,0,'C');
                        }
                    }
                }
			}

            //Box the Scorecard
            $pdf->SetLineWidth(0.5);
            $pdf->Line($pdf->GetPageWidth()/2,$WhereStartY[0]-2.5, $pdf->GetPageWidth()/2,$WhereStartY[5]+$ScoreHeight+2.5);
            $pdf->Line($pdf->getSideMargin()/2,$WhereStartY[2]-2.5, $pdf->GetPageWidth()-$pdf->getSideMargin()/2,$WhereStartY[2]-2.5);
            $pdf->Line($pdf->getSideMargin()/2,$WhereStartY[4]-2.5, $pdf->GetPageWidth()-$pdf->getSideMargin()/2,$WhereStartY[4]-2.5);
            $pdf->SetLineWidth(0.1);

        }
		safe_free_result($Rs);
	}

$pdf->Output();


function DrawScore(&$pdf, $MyRow, $WhichScore, $WhereX, $WhereY, $FinalScore=false) {
    global $defTotalW, $defGoldW, $defArrowTotW, $defBCodeW, $ScoreHeight, $NumFasi, $Fasi, $TgtNoFasi, $TieFasi, $ByeFasi, $Score3D, $MatchFasi, $OppArray, $Start2FirstPhase;

	$scoreStartX = $WhereX;
	$scoreStartY = $WhereY;

	$TotalW = $defTotalW;
	$GoldW = $defGoldW;
	$NumCol = 3;
	$NumRow=5;
	$CellH=6;
// OCIO al 2*: il motivo è che il bit meno significativo è la finale quindi abbiamo tutto traslato a sinistra di un bit (=moltiplicato per due)
	if($MyRow->EvMatchArrowsNo & 2*bitwisePhaseId($NumFasi[$WhichScore])) {
		// eliminatorie
		$NumRow=$MyRow->EvElimEnds;
		$NumCol=$MyRow->EvElimArrows;
		$NumSO=$MyRow->EvElimSO;
	} else {
		$NumRow=$MyRow->EvFinEnds;
		$NumCol=$MyRow->EvFinArrows;
		$NumSO=$MyRow->EvFinSO;
	}

	$ArrowW=$defArrowTotW/$NumCol;
	$CellH=($ScoreHeight - 6*6)/$NumRow;

	//Header
	$pdf->SetFont($pdf->FontStd,'B',10);
    $pdf->SetDefaultColor();
	$pdf->SetXY($WhereX,$WhereY);
	$pdf->Cell($GoldW,6,'',0,0,'C',0);
	$pdf->Cell(2*$TotalW+$defArrowTotW, 6, ($Fasi[$WhichScore]),1,(is_null($MyRow->{$TgtNoFasi[$WhichScore]}) ? 1 : 0),'C',1);

	if(!is_null($MyRow->{$TgtNoFasi[$WhichScore]})) {
		$pdf->SetXY($pdf->GetX()-15,$pdf->GetY());
		if(!$FinalScore) {
			$pdf->SetFont($pdf->FontStd,'',6);
			$pdf->Cell(15,3,get_text('Target'),'LRT',0,'C',1);
			$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->SetXY($pdf->GetX()-15,$pdf->GetY()+3);
			$pdf->Cell(15,3, ltrim($MyRow->{$TgtNoFasi[$WhichScore]},0),'LRB',0,'C',1);
		} else {
			$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(15,3,get_text('MedalGold') . ' ' . ltrim($MyRow->sGo,'0'),'LRT',0,'C',1);
			$pdf->SetXY($pdf->GetX()-15,$pdf->GetY()+3);
			$pdf->Cell(15,3, get_text('MedalBronze') . ' ' . ltrim($MyRow->sBr,'0'),'LRB',0,'C',1);
		}
	}
	$pdf->Rect($WhereX+$GoldW+1,$WhereY+1,4,4,'DF',array(),array(255,255,255));
	$pdf->SetDefaultColor();
	$pdf->SetXY($WhereX+$GoldW+5,$WhereY+1);
	$pdf->SetFont($pdf->FontStd,'B',6);
	$pdf->Cell($NumCol*$ArrowW-5,4, get_text('Winner'),0,1,'L',0);

	$pdf->SetXY($pdf->GetX(),$WhereY+6);
	$WhereY=$pdf->GetY();

	//Intestazioni Colonne
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->SetXY($WhereX,$WhereY);
	$pdf->Cell($GoldW,6,'',0,0,'C',0);
	for($j=0; $j<$NumCol; $j++) {
        $pdf->Cell($ArrowW, 6, ($j + 1), 1, 0, 'C', 1);
    }

	if($MyRow->EvMatchMode) {
		$pdf->Cell($TotalW * 4/5, 6,get_text('SetTotal','Tournament'),1,0,'C',1);
		$pdf->Cell($TotalW * 4/5, 6,get_text('SetPoints', 'Tournament'),1,0,'C',1);
		$pdf->Cell($TotalW * 2/5, 6,get_text('TotalShort','Tournament'),1,0,'C',1);
	} else {
		$pdf->Cell($TotalW,6,get_text('TotalProg','Tournament'),1,0,'C',1);
		$pdf->Cell($TotalW,6,get_text('TotalShort','Tournament'),1,0,'C',1);
	}
	$pdf->ln();
	$WhereY=$pdf->GetY();

	//Righe
    for ($i = 1; $i <= $NumRow; $i++) {
		$pdf->SetFont($pdf->FontStd,'B',10);
		$pdf->SetXY($WhereX,$WhereY);
		$pdf->Cell($GoldW,$CellH,$i,1,0,'C',1);
		for($j=0; $j<$NumCol; $j++) {
            $pdf->Cell($ArrowW, $CellH, '', 1, 0, 'C', 0);
        }
        if ($MyRow->EvMatchMode == 0) {
	        $pdf->Cell($TotalW, $CellH, '', 1, 0, 'C', 0);
	        $pdf->Cell($TotalW, $CellH, '', 1, 0, 'C', 0);
        } else {
	        $pdf->Cell($TotalW * 4/5, $CellH, '', 1, 0, 'C', 0);
			$pdf->SetFont($pdf->FontStd,'B',10);
			$pdf->Cell($TotalW * 4/15,$CellH,'2',1, 0,'C',0);
			$pdf->Cell($TotalW * 4/15,$CellH,'1',1, 0,'C',0);
			$pdf->Cell($TotalW * 4/15,$CellH,'0',1, 0,'C',0);
			$pdf->Cell($TotalW * 2/5,$CellH,'',1, 0,'C',0);
		}
		$pdf->ln();

		$WhereY=$pdf->GetY();
	}

	//Shoot Off
	$pdf->SetXY($WhereX,$WhereY+0.5);
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell($GoldW,6.5,(get_text('TB')),1,0,'C',1);
	$ShootOffW=($NumSO<=$NumCol ? $ArrowW : ($ArrowW*$NumCol)/$NumSO);
    for ($j = 0; $j < $NumSO; $j++) {
		$pdf->SetXY($pdf->GetX()+0.5,$pdf->GetY());
        $pdf->Cell($ShootOffW - 0.5, 4, ' ', 1, 0, 'C', 0);
	}
	$pdf->SetXY($WhereX+$GoldW+0.5,$WhereY+5);
	$pdf->SetFont($pdf->FontStd,'',1);
    $pdf->Cell(2, 2, '', 1, 0, 'R', 0);
	$pdf->SetXY($WhereX+$GoldW+2.5,$WhereY+4.5);
	$pdf->SetFont($pdf->FontStd,'',6);
	$pdf->Cell($ArrowW*1.5, 2.5, get_text('Close2Center','Tournament'),0,0,'L',0);
    $pdf->Cell($ArrowW*($NumCol-2)+$TotalW, 2.5, get_text('ArcherSignature','Tournament'),0,0,'C',0);

    $OppName='';
	if(!$MyRow->{$ByeFasi[$WhichScore]}) {
		$OppName=$MyRow->{$OppArray[$WhichScore]};
	}
	//Totale
	$pdf->SetXY($WhereX+$GoldW+$ArrowW, $WhereY);
	$pdf->SetFont($pdf->FontStd,'B',10);
    if ($MyRow->EvMatchMode == 0) {
		$pdf->Cell(($ArrowW*($NumCol-1))+$TotalW-9, 6, '', 0, 0, 'C', 0);
		$pdf->Cell(9, 6, get_text('Total'), 0,0,'R',0);
        $pdf->Cell($TotalW, 6, '', 1, 0, 'C', 0);
    } else {
		$pdf->Cell($defArrowTotW-$ArrowW+$TotalW*3/5, 6, '',0,0,'C',0);
		$pdf->Cell($TotalW, 6,get_text('Total'),0,0,'R',0);
		$pdf->Cell(2/5*$TotalW,6,'',1,0,'C',0);
	}
	$pdf->ln();

	// Opponent score summary
	$WhereY=$pdf->GetY()+4;
    if($MyRow->{$ByeFasi[$WhichScore]}) {
	    $OppName=get_text('Bye');
    }
	$pdf->SetXY($WhereX,$WhereY);
	$pdf->Cell($GoldW + $ArrowW , 5, (get_text('Opponent', 'Tournament')),0,0,'L',0);
	$pdf->Cell(2*$defTotalW + ($defArrowTotW-$ArrowW-$GoldW) + $defGoldW, 5, ($OppName ?? ''), 'B', 1,'L',0);


	$WhereY=$pdf->GetY()+1;
	$pdf->SetXY($WhereX,$WhereY+0.5);
	$pdf->SetFont($pdf->FontStd,'B',8);
	$pdf->Cell($GoldW, 6.5, (get_text('TB')),1,0,'C',1);
	$ShootOffW=($NumSO<=$NumCol ? $ArrowW : ($ArrowW*$NumCol)/$NumSO);
	if($Score3D) {
		$ShootOffW/=5;
	}
    $hasClosest = false;
    for ($j = 0; $j < $NumSO; $j++) {
        $pdf->SetXY($pdf->GetX()+0.5,$pdf->GetY());
        $pdf->Cell($ShootOffW - 0.5, 4, ' ', 1, 0, 'C', 0);
	}
	$pdf->SetXY($WhereX+$GoldW+0.5,$WhereY+5);
	$pdf->SetFont($pdf->FontStd,'',1);
    $pdf->Cell(2, 2, '', 1, 0, 'R', 0);
	$pdf->SetXY($WhereX+$GoldW+2.5,$WhereY+4.5);
	$pdf->SetFont($pdf->FontStd,'',6);
	if($Score3D) {
		$pdf->Cell($ArrowW*1.5/5, 2.5, get_text('Close2Center','Tournament'),0,0,'L',0);
		$pdf->Cell($ArrowW*3.5/5+$TotalW-2.5, 2.5, get_text('OpponentSignature','Tournament'),0,0,'C',0);
	} else {
		$pdf->Cell($ArrowW * 1.5, 2.5, get_text('Close2Center', 'Tournament'), 0, 0, 'L', 0);
		$pdf->Cell($ArrowW * ($NumCol - 2) + $TotalW, 2.5, get_text('OpponentSignature', 'Tournament'), 0, 0, 'C', 0);
	}

	//Totale
	$pdf->SetXY($WhereX+$GoldW+$ArrowW, $WhereY+0.5);
	$pdf->SetFont($pdf->FontStd,'B',10);
    if ($MyRow->EvMatchMode == 0) {
		$pdf->Cell($TotalW + ($ArrowW*($NumCol-1)) - 9, 6.5,'',0,0,'C',0);
		$pdf->Cell(9, 6.5,(get_text('Total')),0,0,'R',0);
        $pdf->Cell($TotalW, 6.5, '', 1, 0, 'C', 0);
    } else {
		$pdf->Cell($defArrowTotW+$TotalW*3/5 - $ArrowW, 6.5, '',0,0,'C',0);
		$pdf->Cell($TotalW, 6.5, get_text('Total'),0,0,'R',0);
		$pdf->Cell(2/5*$TotalW,6.5,'',1,0,'C',0);
	}

    //Barcode
    if(!empty($_REQUEST['Barcode'])) {
        $pdf->SetXY($scoreStartX + $defGoldW + 2 * $defTotalW + $defArrowTotW + $defBCodeW + 4, $scoreStartY);
        $pdf->SetFont('barcode', '', 22);
        $pdf->StartTransform();
        $pdf->Rotate(-90, $scoreStartX + $defGoldW + 2 * $defTotalW + $defArrowTotW + $defBCodeW + 4, $scoreStartY);
        $pdf->Cell($ScoreHeight * ($NumFasi[$WhichScore] == 1 ? 0.45 : 1), 5, '*' . mb_convert_encoding($MyRow->{$MatchFasi[$WhichScore]} . '-1-' . $MyRow->EvCode, "UTF-8", "cp1252") . "*", 0, 0, 'L', 0);

        if ($NumFasi[$WhichScore] == 1) {
            $pdf->Cell($ScoreHeight * 0.1, 5, '');
            $pdf->Cell($ScoreHeight * 0.45, 5, '*' . mb_convert_encoding($MyRow->{$MatchFasi[$WhichScore + 1]} . '-1-' . $MyRow->EvCode, "UTF-8", "cp1252") . "*", 0, 0, 'R', 0);
        }
        $pdf->StopTransform();
        $pdf->SetFont($pdf->FontStd, '', 10);
    }

	$pdf->SetLineWidth(0.75);
	// draws a line on unused scorecards
	if($MyRow->{$ByeFasi[$WhichScore]}) {
		$pdf->Line($scoreStartX,$scoreStartY,$scoreStartX+(2*$defTotalW + $defArrowTotW + $defGoldW),$scoreStartY + $ScoreHeight);
	}

	$pdf->SetLineWidth(0.1);

}
