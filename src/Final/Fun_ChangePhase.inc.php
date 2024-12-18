<?php
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Lib/Obj_RankFactory.php');
	require_once('Common/Lib/Fun_Modules.php');
	require_once('Common/Lib/Fun_Phases.inc.php');
/*Posso avere:
 1) solo la fase e muove tutti gli eventi
 2) Fase e evento e li muove
 3) Matchno e evento e muove solo il matchno in question
*/


function move2NextPhase($Phase=NULL, $Event=NULL, $MatchNo=NULL, $TourId=0, $NoRecalc=false, $Pool='') {
	require_once('Common/Fun_Modules.php');
	global $action;
//verifico i parametri
	if(is_null($Phase) and is_null($MatchNo))	//Devono esistere o la fase o il MatchNo
		return;
	if(is_null($Phase) and is_null($Event))		//Se non ho la Fase (e quindi ho il MatchNo) deve esistere l'evento
		return;

	if(!$TourId) $TourId=$_SESSION['TourId'];

// Remember to check the saved from 64th to 16th!
	$CheckSaved=array();

//Verifico la situazione tiebreak
	$Confirmed=array();
    $movedCoaches=array();
	$Select = "SELECT f.FinEvent, EvMatchMode as MatchMode, 
            EvCheckGolds, EvCheckXNines, if(EvGoldsChars!='', EvGoldsChars, ToGoldsChars) as GoldChars, if(EvXNineChars!='', EvXNineChars, ToXNineChars) as XNineChars, 
            f.FinMatchNo as MatchNo, f2.FinMatchNo as OppMatchNo,  EvFinalFirstPhase,
			f.FinAthlete AS Athlete, f2.FinAthlete AS OppAthlete, f.FinConfirmed as IsConfirmed,
			f.FinIrmType AS IrmType, f2.FinIrmType AS OppIrmType,
			f.FinTbClosest AS Closest, f2.FinTbClosest AS OppClosest,
			f.FinGolds as Golds, f2.FinGolds as OppGolds, 
			f.FinXNines as XNines, f2.FinXNines as OppXNines, 
			@PhaseMatch:=(GrPhase & EvMatchArrowsNo), 
			if(@PhaseMatch, EvElimEnds, EvFinEnds) FinEnds, if(@PhaseMatch, EvElimArrows, EvFinArrows) FinArrows, if(@PhaseMatch, EvElimSO, EvFinSO) FinSO,
			IF(f.FinDateTime>=f2.FinDateTime, f.FinDateTime, f2.FinDateTime) AS DateTime,
			IF(EvMatchMode=0,f.FinScore,f.FinSetScore) AS Score, f.FinTie as Tie, f.FinTieBreak as TbString, IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) as OppScore, f2.FinTie as OppTie, f2.FinTieBreak as OppTbString,
			f.FinArrowString as ArrString, f2.FinArrowString as OppArrString,  f.FinSetPoints as SetPoint, f2.FinSetPoints as OppSetPoint,
			f.FinIrmType as IrmType, f2.FinIrmType as OppIrmType,
			EvFinalFirstPhase<=Grphase as FollowBye
		FROM Finals AS f
		INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f2.FinMatchNo=f.FinMatchNo+1 AND f.FinTournament=f2.FinTournament
		INNER JOIN Events ON f.FinEvent=EvCode AND f.FinTournament=EvTournament AND EvTeamEvent=0 
		INNER JOIN Grids ON f.FinMatchNo=GrMatchNo AND ". (is_null($Phase) ? "GrMatchNo=" . intval(($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1)) : "GrPhase=" . intval($Phase)) ."
		INNER JOIN Tournament on ToId=EvTournament
		WHERE f.FinTournament=" . StrSafe_DB($TourId) . " AND (f.FinMatchNo % 2)=0";

		if(!is_null($Event) and $Event!='')
			$Select .= " AND f.FinEvent=" . StrSafe_DB($Event) . " ";
		$Select .= "ORDER BY f.FinEvent, f.FinMatchNo ";
	//echo $Select;exit;
	$Rs=safe_r_sql($Select);
    while ($MyRow=safe_fetch($Rs)) {
        // set as actual numbers the scores etc
        foreach(['Score', 'OppScore', 'Golds', 'OppGolds', 'XNines', 'OppXNines'] as $fld) {
            $MyRow->{$fld}=intval($MyRow->{$fld});
        }
        if($MyRow->IsConfirmed) {
            $Confirmed[]=array('event' => $MyRow->FinEvent, 'matchno' => min($MyRow->MatchNo, $MyRow->OppMatchNo));
        }
        if(empty($CheckSaved[$MyRow->FinEvent])) {
            $CheckSaved[$MyRow->FinEvent]=($MyRow->EvFinalFirstPhase==48 and $Phase==64);
        }

        // first check the IRM of the athletes if none of them have an IRM status set because the procedures rechecks the winner!
        if(!($MyRow->IrmType and $MyRow->OppIrmType)) {
            if($MyRow->IrmType and !$MyRow->OppIrmType) {
                // opponent gets a bye only if not a DQB
                if($MyRow->IrmType!=20) {
                    $SqlUpdate = "UPDATE Finals SET FinTie=0, FinWinLose=0, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=". StrSafe_DB($MyRow->MatchNo) . " AND FinTournament=" . StrSafe_DB($TourId);
                    safe_w_sql($SqlUpdate);
                    $SqlUpdate = "UPDATE Finals SET FinTie=2, FinWinLose=1, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=". StrSafe_DB($MyRow->OppMatchNo) . " AND FinTournament=" . StrSafe_DB($TourId);
                    safe_w_sql($SqlUpdate);
                }
            } elseif(!$MyRow->IrmType and $MyRow->OppIrmType) {
                // competitor gets a bye only if it is not a DQB
                if ($MyRow->OppIrmType != 20) {
                    $SqlUpdate = "UPDATE Finals SET FinTie=0, FinWinLose=0, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=" . StrSafe_DB($MyRow->OppMatchNo) . " AND FinTournament=" . StrSafe_DB($TourId);
                    safe_w_sql($SqlUpdate);
                    $SqlUpdate = "UPDATE Finals SET FinTie=2, FinWinLose=1, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=" . StrSafe_DB($MyRow->MatchNo) . " AND FinTournament=" . StrSafe_DB($TourId);
                    safe_w_sql($SqlUpdate);
                }
            } elseif($MyRow->FollowBye and $MyRow->Score == 0 and $MyRow->OppScore==0 and (($MyRow->Athlete!= 0 and $MyRow->OppAthlete==0) or ($MyRow->Athlete== 0 and $MyRow->OppAthlete!=0))) {
                if($MyRow->Athlete!= 0 and $MyRow->OppAthlete==0) {
                    //Se uno dei due ATLETI è ZERO ed ENTRAMBI GLI SCORES sono a ZERO imposto il Bye
                    $SqlUpdate = "UPDATE Finals SET FinTie=0, FinWinLose=0, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=". StrSafe_DB($MyRow->OppMatchNo) . " AND FinTournament=" . StrSafe_DB($TourId);
                    safe_w_sql($SqlUpdate);
                    $SqlUpdate = "UPDATE Finals SET FinTie=2, FinWinLose=1, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=". StrSafe_DB($MyRow->MatchNo) . " AND FinTournament=" . StrSafe_DB($TourId);
                    safe_w_sql($SqlUpdate);
                } elseif($MyRow->Athlete== 0 and $MyRow->OppAthlete!=0) {
                    $SqlUpdate = "UPDATE Finals SET FinTie=0, FinWinLose=0, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=". StrSafe_DB($MyRow->MatchNo) . " AND FinTournament=" . StrSafe_DB($TourId);
                    safe_w_sql($SqlUpdate);
                    $SqlUpdate = "UPDATE Finals SET FinTie=2, FinWinLose=1, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=". StrSafe_DB($MyRow->OppMatchNo) . " AND FinTournament=" . StrSafe_DB($TourId);
                    safe_w_sql($SqlUpdate);
                }
            } elseif($MyRow->MatchMode!=0 and !$NoRecalc) {
                //Se non ho il BYE, rigestisco il numero di set vinti e aggiorno il conteggio del totale (se i punti set sono valorizzati & le arrowstring sono vuote)
                if(strlen(trim(str_replace("|","",$MyRow->SetPoint))) and strlen(trim(str_replace("|","",$MyRow->OppSetPoint))) and strlen(trim($MyRow->ArrString))==0 and strlen(trim($MyRow->OppArrString))==0) {
                    $AthSets=explode("|",$MyRow->SetPoint);
                    $OppSets=explode("|",$MyRow->OppSetPoint);
                    $AthSpBe=array();
                    $OppSpBe=array();
                    $AthScore=0;
                    $OppScore=0;
                    $AthWin=0;
                    $OppWin=0;
                    $NumSets=min(count($AthSets), count($OppSets));
                    for($i=0;$i<$NumSets;$i++) {
                        if(intval($AthSets[$i])>intval($OppSets[$i])) {
                            $AthScore += 2;
                            $AthWin++;
                            $AthSpBe[]=2;
                            $OppSpBe[]=0;
                        } elseif(intval($AthSets[$i])<intval($OppSets[$i])) {
                            $OppScore += 2;
                            $OppWin++;
                            $AthSpBe[]=0;
                            $OppSpBe[]=2;
                        } elseif(intval($AthSets[$i])!=0 and intval($OppSets[$i])!=0) {
                            $AthScore += 1;
                            $OppScore += 1;
                            $AthSpBe[]=1;
                            $OppSpBe[]=1;
                        }
                    }
                    if($AthScore > $MyRow->FinEnds+2 or $OppScore > $MyRow->FinEnds+2) {
                        $AthScore=0;
                        $OppScore=0;
                    }
                    $SqlUpdate = "UPDATE Finals SET FinWinLose=".($AthScore>$MyRow->FinEnds ? 1 : 0).", FinSetPointsByEnd='".implode('|', $AthSpBe)."', FinSetScore=" . StrSafe_DB($AthScore) . ", FinWinnerSet=" . StrSafe_DB($AthWin) . ", FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=" . StrSafe_DB($MyRow->MatchNo) . " AND FinTournament=" . StrSafe_DB($TourId);
                    safe_w_sql($SqlUpdate);
                    $MyRow->Score=$AthScore;
                    $SqlUpdate = "UPDATE Finals SET FinWinLose=".($OppScore>$MyRow->FinEnds ? 1 : 0).", FinSetPointsByEnd='".implode('|', $OppSpBe)."', FinSetScore=" . StrSafe_DB($OppScore) . ", FinWinnerSet=" . StrSafe_DB($OppWin) . ", FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=" . StrSafe_DB($MyRow->OppMatchNo) . " AND FinTournament=" . StrSafe_DB($TourId);
                    safe_w_sql($SqlUpdate);
                    $MyRow->OppScore=$OppScore;
                }
            }

            // Scores are tied if they have the same value, or in set system if the difference is 1,
            // and in case we check golds if they are the same
            // and in case we check XNines if they are the same
            $IsTie=(
                ($MyRow->Score == $MyRow->OppScore or ($MyRow->MatchMode!=0 and abs($MyRow->Score-$MyRow->OppScore)==1))
                and (!$MyRow->EvCheckGolds or $MyRow->Golds==$MyRow->OppGolds)
                and (!$MyRow->EvCheckXNines or $MyRow->XNines==$MyRow->OppXNines)
                );
            if(($MyRow->Score!= 0 or $MyRow->OppScore!=0)
                and $MyRow->IrmType==0 and $MyRow->OppIrmType==0
                and !$IsTie
                ) {
                // If opponents are still tied reset SO flag and closest
                $IsTie=false;
                $SqlUpdate = "UPDATE Finals SET FinTie=0, FinTbClosest=0, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo IN (". StrSafe_DB($MyRow->MatchNo) . "," . StrSafe_DB($MyRow->OppMatchNo) . ") AND FinTournament=" . StrSafe_DB($TourId);
                safe_w_sql($SqlUpdate);
            }

            // Se i punteggi sono uguali e diversi da ZERO entro nel dettaglio E le stringhe di frecce sono lunghe uguali e non vuote
            if($MyRow->Score!= 0
                and $IsTie
                and strlen(str_replace(' ', '', $MyRow->TbString))!=0
                and (strlen(str_replace(' ', '', $MyRow->TbString))%$MyRow->FinSO)==0
                and strlen(str_replace(' ', '', $MyRow->TbString))==strlen(str_replace(' ', '', $MyRow->OppTbString))) {
                $WinnerId=-1;
                $XChar=($MyRow->EvCheckGolds ? $MyRow->GoldChars : ($MyRow->EvCheckXNines? $MyRow->XNineChars : null));
                $LTB=ValutaArrowStringSO(trim($MyRow->TbString), $XChar, $XChar?'A':null);
                $RTB=ValutaArrowStringSO(trim($MyRow->OppTbString), $XChar, $XChar?'A':null);

                if($MyRow->Closest) {
                    $WinnerId = $MyRow->MatchNo; // Left is closest to Center
                } elseif($MyRow->OppClosest) {
                    $WinnerId = $MyRow->OppMatchNo; // Right is closer to center
                } elseif($LTB[0]>$RTB[0]) {
                    $WinnerId = $MyRow->MatchNo; // total of L is > than R
                } elseif($LTB[0]<$RTB[0]) {
                    $WinnerId = $MyRow->OppMatchNo; // total of L is < than R
                } elseif($LTB[3]>$RTB[3]) {
                    $WinnerId = $MyRow->MatchNo; // number of X for L is > than R
                } elseif($LTB[3]<$RTB[3]) {
                    $WinnerId = $MyRow->OppMatchNo; // number of X for L is < than R
                }

                //Azzero entrambi i flag di shootoff
                $SqlUpdate = "UPDATE Finals SET FinTie=0, FinWinLose=0, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo IN (". StrSafe_DB($MyRow->MatchNo) . "," . StrSafe_DB($MyRow->OppMatchNo) . ") AND FinTournament=" . StrSafe_DB($TourId);
                safe_w_sql($SqlUpdate);
                if ($WinnerId>-1) {
                    $SqlUpdate = "UPDATE Finals SET FinTie=1, FinWinLose=1, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=". StrSafe_DB($WinnerId) . " AND FinTournament=" . StrSafe_DB($TourId);
                    safe_w_sql($SqlUpdate);
                    if($MyRow->MatchMode!=0 and $MyRow->Score == $MyRow->OppScore) {
                        $SqlUpdate = "UPDATE Finals SET FinSetScore=FinSetScore+1, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=". StrSafe_DB($WinnerId) . " AND FinTournament=" . StrSafe_DB($TourId);
                        safe_w_sql($SqlUpdate);
                    }
                }
//            } elseif($MyRow->EvCheckGolds or $MyRow->EvCheckXNines) {
//                // scores are tie BUT we need to check Golds
//                if($MyRow->EvCheckGolds) {
//                    if($MyRow->Golds>$MyRow->OppGolds) {
//                        $WinnerId = $MyRow->MatchNo;
//                    } elseif($MyRow->Golds<$MyRow->OppGolds) {
//                        $WinnerId = $MyRow->OppMatchNo;
//                    } elseif($MyRow->EvCheckXNines) {
//                        if($MyRow->XNines>$MyRow->OppXNines) {
//                            $WinnerId = $MyRow->MatchNo;
//                        } elseif($MyRow->XNines<$MyRow->OppXNines) {
//                            $WinnerId = $MyRow->OppMatchNo;
//                        }
//                    }
//                } elseif($MyRow->EvCheckXNines) {
//                    if($MyRow->XNines>$MyRow->OppXNines) {
//                        $WinnerId = $MyRow->MatchNo;
//                    } elseif($MyRow->XNines<$MyRow->OppXNines) {
//                        $WinnerId = $MyRow->OppMatchNo;
//                    }
//                }
//
//                //Azzero entrambi i flag di shootoff
//                $SqlUpdate = "UPDATE Finals SET FinTie=0, FinTbClosest=0, FinWinLose=0, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo IN (". StrSafe_DB($MyRow->MatchNo) . "," . StrSafe_DB($MyRow->OppMatchNo) . ") AND FinTournament=" . StrSafe_DB($TourId);
//                safe_w_sql($SqlUpdate);
//                if ($WinnerId>-1) {
//                    $SqlUpdate = "UPDATE Finals SET FinTie=0, FinWinLose=1, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE FinEvent=" . StrSafe_DB($MyRow->FinEvent) . " AND FinMatchNo=". StrSafe_DB($WinnerId) . " AND FinTournament=" . StrSafe_DB($TourId);
//                    safe_w_sql($SqlUpdate);
//                }
            }
        }
    }

// Faccio i passaggi di fase
	$MyNextMatchNo='xx';
	$QueryFilter = '';

	$Select = "SELECT 
			f.FinEvent AS Event, f.FinMatchNo, f2.FinMatchNo OppMatchNo,
			f.FinIrmType AS IrmType, f2.FinIrmType AS OppIrmType, 
			GrPhase, f.FinAthlete AS Athlete, f2.FinAthlete AS OppAthlete, f.FinCoach AS Coach, f2.FinCoach AS OppCoach,
			IF(EvMatchMode=0,f.FinScore,f.FinSetScore) AS Score, f.FinTie as Tie, IF(EvMatchMode=0,f2.FinScore,f2.FinSetScore) as OppScore, f2.FinTie as OppTie,
			EvCheckGolds, f.FinGolds as Golds, f2.FinGolds as OppGolds,
			EvCheckXNines, f.FinXNines as XNines, f2.FinXNines as OppXNines,
			IF(GrPhase>2, FLOOR(f.FinMatchNo/2),FLOOR(f.FinMatchNo/2)-2) AS NextMatchNo, EvElimType
		FROM Finals AS f
		INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=IF((f.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f.FinTournament=f2.FinTournament
		INNER JOIN Events ON f.FinEvent=EvCode AND f.FinTournament=EvTournament AND EvTeamEvent=0 ";
		if(!is_null($Phase)) {
			$Select .= "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo AND GrPhase=" . StrSafe_DB($Phase) . " ";
		} else {
			$Select .= "INNER JOIN Grids ON f.FinMatchNo=GrMatchNo AND GrMatchNo=" . StrSafe_DB(($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1)) . " ";
		}
		$Select .= "LEFT JOIN Entries ON f.FinAthlete=EnId AND f.FinTournament=EnTournament "
		. "WHERE f.FinTournament=" . StrSafe_DB($TourId) . " AND (f.FinMatchNo % 2)=0 ";

		if(!is_null($Event) and $Event!='')
			$Select .= "AND f.FinEvent=" . StrSafe_DB($Event) . " ";

		$Select .= "ORDER BY f.FinEvent, NextMatchNo ASC, Score DESC, Tie DESC ";


	$Rs=safe_r_sql($Select);

// conterrà i parametri per il calcolo delle RankFinal
	$coppie=array();


	$AthPropTs = NULL;
	if (safe_num_rows($Rs)>0) {
		$AthPropTs = date('Y-m-d H:i:s');

		// Elimination Pools Show Match Winner choose where to go!!!
		$ShowMatchWinner='';
		$ShowMatchLoser='';
		if($Pool) {
			if($Pool=='A') {
				$ShowMatchWinner='4';
				$ShowMatchLoser='7';
			} elseif($Pool=='B') {
				$ShowMatchWinner='7';
				$ShowMatchLoser='4';
			}
		}


		while ($MyRow=safe_fetch($Rs)) {
		/*
		 * Dato che potrei avere più fasi gestite da questa funzione, io ricavo le coppie
		 * per la RankFinal dalle righe del recordset.
		 * Visto che mi imbatterò più volte nella stessa coppia evento/fase, solo se la coppia
		 * non l'ho già contata la aggiungo nel vettore.
		 */
			if (!in_array($MyRow->Event.'@'.$MyRow->GrPhase,$coppie)) {
				$coppie[]=$MyRow->Event.'@'.$MyRow->GrPhase;
			}

			// sets the WinLose Flag
			$WinLose=-1;

			$AthProp = '0';
            $CoachProp = '0';
			$WhereProp = '0';
			if (intval($MyRow->Score)>intval($MyRow->OppScore)
                    or $MyRow->Tie==2
                    or (intval($MyRow->Score)==intval($MyRow->OppScore) and (
                        intval($MyRow->Tie)>intval($MyRow->OppTie)
                        or ($MyRow->EvCheckGolds and intval($MyRow->Golds)>intval($MyRow->OppGolds))
                        or ($MyRow->EvCheckGolds and $MyRow->EvCheckXNines and intval($MyRow->Golds)==intval($MyRow->OppGolds) and intval($MyRow->XNines)>intval($MyRow->OppXNines))
                        or (!$MyRow->EvCheckGolds and $MyRow->EvCheckXNines and intval($MyRow->XNines)>intval($MyRow->OppXNines))
                        ) ) ) {
				$WinLose=$MyRow->FinMatchNo;
				if ($MyRow->GrPhase>=2) {
					$action='reload';
					// Pool Matches...
					if($ShowMatchWinner) {
						$MyUpQuery = "UPDATE Finals SET
							FinAthlete =" . StrSafe_DB($MyRow->Athlete) . ",
							FinCoach =" . StrSafe_DB($MyRow->Coach) . ",
							FinDateTime=" . StrSafe_DB($AthPropTs) . "
							WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB($ShowMatchWinner) . " AND FinTournament=" . StrSafe_DB($TourId) . " ";
						safe_w_sql($MyUpQuery);
						$MyUpQuery = "UPDATE Finals SET
							FinAthlete =" . StrSafe_DB($MyRow->OppAthlete) . ",
							FinCoach =" . StrSafe_DB($MyRow->OppCoach) . ",
							FinDateTime=" . StrSafe_DB($AthPropTs) . "
							WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB($ShowMatchLoser) . " AND FinTournament=" . StrSafe_DB($TourId) . " ";
						safe_w_sql($MyUpQuery);

						// update FinOdfTiming data
						updateOdfTiming('S', $TourId, $MyRow->Event, 0, [$ShowMatchWinner, $ShowMatchLoser]);
					} else {
						$MyUpQuery = "UPDATE Finals SET
							FinAthlete =" . StrSafe_DB($MyRow->Athlete) . ",
							FinCoach =" . StrSafe_DB($MyRow->Coach) . ",
							FinDateTime=" . StrSafe_DB($AthPropTs) . "
							WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB($MyRow->NextMatchNo) . " AND FinTournament=" . StrSafe_DB($TourId) . " ";
						$RsUp=safe_w_sql($MyUpQuery);

						// update FinOdfTiming data
						if(safe_w_affected_rows()) {
							updateOdfTiming('S', $TourId, $MyRow->Event, 0, $MyRow->NextMatchNo);
                            if($MyRow->Coach!=0) {
                                $movedCoaches[]=array('event' => $MyRow->Event, 'matchno' => $MyRow->NextMatchNo);
                            }
						}

						$AthProp=$MyRow->Athlete;
                        $CoachProp=$MyRow->Coach;
						$WhereProp=$MyRow->OppAthlete;
						if($MyRow->GrPhase==2) {
							$MyUpQuery = "UPDATE Finals SET
								FinAthlete =" . StrSafe_DB($MyRow->OppAthlete) . ",
								FinCoach =" . StrSafe_DB($MyRow->OppCoach) . ",
								FinDateTime=" . StrSafe_DB($AthPropTs) . "
								WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB(($MyRow->NextMatchNo+2)) . " AND FinTournament=" . StrSafe_DB($TourId) . " ";
							$RsUp=safe_w_sql($MyUpQuery);

							// update FinOdfTiming data
							if(safe_w_affected_rows()) {
								updateOdfTiming('S', $TourId, $MyRow->Event, 0, $MyRow->NextMatchNo+2);
                                if($MyRow->OppCoach!=0) {
                                    $movedCoaches[]=array('event' => $MyRow->Event, 'matchno' => $MyRow->NextMatchNo+2);
                                }
							}
						}
					}
				}
			} elseif (intval($MyRow->Score)<intval($MyRow->OppScore)
                    or $MyRow->OppTie==2
                    or (intval($MyRow->Score)==intval($MyRow->OppScore) and (
                        intval($MyRow->Tie)<intval($MyRow->OppTie)
                        or ($MyRow->EvCheckGolds and intval($MyRow->Golds)<intval($MyRow->OppGolds))
                        or ($MyRow->EvCheckGolds and $MyRow->EvCheckXNines and intval($MyRow->Golds)==intval($MyRow->OppGolds) and intval($MyRow->XNines)<intval($MyRow->OppXNines))
                        or (!$MyRow->EvCheckGolds and $MyRow->EvCheckXNines and intval($MyRow->XNines)<intval($MyRow->OppXNines))
                        ) ) ) {
				$WinLose=$MyRow->OppMatchNo;
				if ($MyRow->GrPhase>=2) {
					$action='reload';
					// Pool Matches...
					if($ShowMatchWinner) {
						$MyUpQuery = "UPDATE Finals SET
							FinAthlete =" . StrSafe_DB($MyRow->OppAthlete) . ",
							FinCoach =" . StrSafe_DB($MyRow->OppCoach) . ",
							FinDateTime=" . StrSafe_DB($AthPropTs) . "
							WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB($ShowMatchWinner) . " AND FinTournament=" . StrSafe_DB($TourId) . " ";
						safe_w_sql($MyUpQuery);
						// update FinOdfTiming data
						if(safe_w_affected_rows()) {
							updateOdfTiming('S', $TourId, $MyRow->Event, 0, $ShowMatchWinner);
                            if($MyRow->OppCoach!=0) {
                                $movedCoaches[]=array('event' => $MyRow->Event, 'matchno' => $ShowMatchWinner);
                            }
						}

						$MyUpQuery = "UPDATE Finals SET
							FinAthlete =" . StrSafe_DB($MyRow->Athlete) . ",
							FinCoach =" . StrSafe_DB($MyRow->Coach) . ",
							FinDateTime=" . StrSafe_DB($AthPropTs) . "
							WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB($ShowMatchLoser) . " AND FinTournament=" . StrSafe_DB($TourId) . " ";
						safe_w_sql($MyUpQuery);
						// update FinOdfTiming data
						if(safe_w_affected_rows()) {
							updateOdfTiming('S', $TourId, $MyRow->Event, 0, $ShowMatchLoser);
                            if($MyRow->Coach!=0) {
                                $movedCoaches[]=array('event' => $MyRow->Event, 'matchno' => $ShowMatchLoser);
                            }
						}
					} else {
						$MyUpQuery = "UPDATE Finals SET
							FinAthlete =" . StrSafe_DB($MyRow->OppAthlete) . ",
							FinCoach =" . StrSafe_DB($MyRow->OppCoach) . ",
							FinDateTime=" . StrSafe_DB($AthPropTs) . "
							WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB($MyRow->NextMatchNo) . " AND FinTournament=" . StrSafe_DB($TourId) . " ";
						$RsUp=safe_w_sql($MyUpQuery);
						// update FinOdfTiming data
						if(safe_w_affected_rows()) {
							updateOdfTiming('S', $TourId, $MyRow->Event, 0, $MyRow->NextMatchNo);
                            if($MyRow->OppCoach!=0) {
                                $movedCoaches[]=array('event' => $MyRow->Event, 'matchno' => $MyRow->NextMatchNo);
                            }
						}

						$AthProp=$MyRow->OppAthlete;
                        $CoachProp=$MyRow->OppCoach;
						$WhereProp=$MyRow->Athlete;
						if($MyRow->GrPhase==2) {
							$MyUpQuery = "UPDATE Finals SET
								FinAthlete =" . StrSafe_DB($MyRow->Athlete) . ",
								FinCoach =" . StrSafe_DB($MyRow->Coach) . ",
								FinDateTime=" . StrSafe_DB($AthPropTs) . " ";
							$MyUpQuery.= "WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB(($MyRow->NextMatchNo+2)) . " AND FinTournament=" . StrSafe_DB($TourId) . " ";
							$RsUp=safe_w_sql($MyUpQuery);
							// update FinOdfTiming data
							if(safe_w_affected_rows()) {
								updateOdfTiming('S', $TourId, $MyRow->Event, 0, $MyRow->NextMatchNo+2);
                                if($MyRow->Coach!=0) {
                                    $movedCoaches[]=array('event' => $MyRow->Event, 'matchno' => $MyRow->NextMatchNo+2);
                                }
							}
						}
					}
				}
			} else {
				if($MyRow->EvElimType!=3 and $MyRow->EvElimType!=4) {
					if ($MyRow->GrPhase>=2) {
						$action='reload';
						$MyUpQuery = "UPDATE Finals SET
							FinAthlete ='0', FinCoach ='0',
							FinDateTime=" . StrSafe_DB($AthPropTs) . "
							WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB($MyRow->NextMatchNo) . " AND FinTournament=" . StrSafe_DB($TourId) . " ";
						$RsUp=safe_w_sql($MyUpQuery);
						// update FinOdfTiming data
						if(safe_w_affected_rows()) {
							updateOdfTiming('S', $TourId, $MyRow->Event, 0, $MyRow->NextMatchNo);
						}

						if($MyRow->GrPhase==2) {
							$MyUpQuery = "UPDATE Finals SET
								FinAthlete ='0', FinCoach ='0',
								FinDateTime=" . StrSafe_DB($AthPropTs) . "
								WHERE FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinMatchNo=" . StrSafe_DB(($MyRow->NextMatchNo+2)) . " AND FinTournament=" . StrSafe_DB($TourId) . " ";
							$RsUp=safe_w_sql($MyUpQuery);
							// update FinOdfTiming data
							if(safe_w_affected_rows()) {
								updateOdfTiming('S', $TourId, $MyRow->Event, 0, $MyRow->NextMatchNo+2);
							}
						}
					}
				}
			}

			// update the winner of previous match
			safe_w_sql("update Finals set FinWinLose=if(FinMatchNo=".$WinLose.", 1, 0) where FinMatchNo in (" . StrSafe_DB($MyRow->FinMatchNo) . "," . StrSafe_DB($MyRow->OppMatchNo) . ") AND FinEvent=" . StrSafe_DB($MyRow->Event) . " AND FinTournament=" . StrSafe_DB($TourId) . "");
			if(safe_w_affected_rows()) {
				// update FinOdfTiming data
				updateOdfTiming('U', $TourId, $MyRow->Event, 0, $WinLose);
			}
			$OldId=($AthProp!=0 ? StrSafe_DB($WhereProp) : StrSafe_DB($MyRow->Athlete) . ',' . StrSafe_DB($MyRow->OppAthlete));
			if($OldId!="'0'" and !$ShowMatchWinner) {
				$action='reload';
				// if Athlete and Opponent are both there or if athlete is not present
				// propagates the winner in next matches. Swap of winners, for example.

				$Update
					= "UPDATE Finals SET "
					. "FinAthlete=" . StrSafe_DB($AthProp) . ", "
                    . "FinCoach=" . StrSafe_DB($CoachProp) . ", "
					. "FinDateTime=" . StrSafe_DB($AthPropTs) . " "
					. ($AthProp==0 ? ', FinWinLose=0 ' : '')
					. "WHERE FinAthlete IN (" . $OldId . ") "
					. "AND FinTournament=" . StrSafe_DB($TourId) . " "
					. "AND FinEvent=" . StrSafe_DB($MyRow->Event) . " "
					. "AND FinMatchNo<"	. StrSafe_DB($MyRow->NextMatchNo) . " ";

				$RsProp = safe_w_sql($Update);
			}
		}
	}

	foreach($CheckSaved as $MyEvent=>$DoSave) {
		if(!$DoSave) continue;
		// we get here only if we check phase 64 and we have a starting phase of 48, so the first 8 are automatically put in the 16th matches
		// start putting a bye to the first saved athletes in 24th
		$Saved=SavedInPhase(48);
        $date=date('Y-m-d H:i:s');
		$SqlUpdate = "UPDATE Finals inner join Grids on FinMatchNo=GrMatchNo and GrPhase=32 and GrPosition2<=$Saved and GrPosition2>0
			SET FinTie=2, FinWinLose=1, FinDateTime=" . StrSafe_DB($date) . "
			WHERE FinEvent=" . StrSafe_DB($MyEvent) . " AND FinTournament=" . StrSafe_DB($TourId);
		safe_w_SQL($SqlUpdate);
		if(safe_w_affected_rows()) {
			// update the ODF timings
			$updates=[];
			$q=safe_r_sql("select * from Grids where GrPhase=32 and GrPosition2<=$Saved and GrPosition2>0");
			while($r=safe_fetch($q)) {
				$updates[]=$r->GrMatchNo;
			}
			updateOdfTiming('S', $TourId, $MyEvent, 0, $updates);
		}

		// Push the first saved to 16th... postponed as it breaks the brackets in the infosystem
		// in write SQL as it must be certain that it takes the right people without waiting for replica!!!
		$q=safe_w_sql("select FinAthlete, FinMatchNo, GrPosition2 from Finals inner join Grids on FinMatchNo=GrMatchNo and GrPhase=64 and GrPosition2<=$Saved and GrPosition2>0
			WHERE FinEvent=" . StrSafe_DB($MyEvent) . " AND FinTournament=" . StrSafe_DB($TourId));
		while($r=safe_fetch($q)) {
			safe_w_sql("UPDATE Finals inner join Grids on FinMatchNo=GrMatchNo and GrPhase=16 and GrPosition2=$r->GrPosition2
				SET FinAthlete=$r->FinAthlete, FinDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "
				WHERE FinEvent=" . StrSafe_DB($MyEvent) . " AND FinTournament=" . StrSafe_DB($TourId));
			if(safe_w_affected_rows()) {
				safe_w_SQL("insert into FinOdfTiming 
	                (FinOdfTournament, FinOdfEvent, FinOdfTeamEvent, FinOdfMatchno, FinOdfStartlist) 
					values ({$TourId}, '{$MyEvent}', 0, {$r->FinMatchNo}, '{$date}')
	                on duplicate key update FinOdfStartlist='{$date}'");
			}
		}
	}

	// se ho delle coppie calcolo per queste la RankFinal
	if (count($coppie)>0) {
		// manages the loosers if there is a looser event
		$coppie=array_merge($coppie, moveToNextPhaseLoosers($coppie, $TourId));

		Obj_RankFactory::create('FinalInd',array('tournament' => $TourId, 'eventsC'=>$coppie))->calculate();
		foreach($coppie as $cp) {
			runJack("FinRankUpdate", $TourId, array("Event"=>substr($cp, 0, strpos($cp, '@')), "Team"=>0, "TourId"=>$TourId));
		}
	}

	//Verifico se esiste qualcosa di specifico da fare (tipo gestione Bracket looser)
	global $CFG;
	$q=safe_r_sql("select ToType, ToLocRule, ToTypeSubRule from Tournament where ToId={$TourId}");
	$r=safe_fetch($q);
	$ToType=$r->ToType;
	$ToLocRule=$r->ToLocRule;
	$ToSubRule=$r->ToTypeSubRule;
	$Common=$CFG->DOCUMENT_PATH . "Modules/Sets/$ToLocRule/Functions/move2NextPhase%s.php";
	if(file_exists($file=sprintf($Common, "-$ToType-$ToSubRule"))
		or file_exists($file=sprintf($Common, "-$ToType"))
		or file_exists($file=sprintf($Common, "-$ToSubRule"))
		or file_exists($file=sprintf($Common, ""))
		) {
			require_once($file);
	}

	// trigger the match confirmed for Jack
	foreach($Confirmed as $Item) {
		runJack("MatchConfirmed", $TourId, array("Event"=>$Item['event'], "Team"=>0, "MatchNo"=>$Item['matchno'], "TourId"=>$TourId));
	}
    foreach ($movedCoaches as $Item) {
        runJack("CoachAssigned", $TourId, array("Event"=>$Item['event'], "Team"=>0, "MatchNo"=>$Item['matchno'], "TourId"=>$TourId));
    }

	return $AthPropTs;
}

function move2NextPhaseTeam($Phase=NULL, $Event=NULL, $MatchNo=NULL, $TourId=0, $NoRecalc=false) {
	require_once('Common/Fun_Modules.php');
//verifico i parametri
	if(is_null($Phase) and is_null($MatchNo))	//Devono esistere o la fase o il MatchNo
		return;
	if(is_null($Phase) and is_null($Event))		//Se non ho la Fase (e quindi ho il MatchNo) deve esistere l'evento
		return;

	if(!$TourId) $TourId=$_SESSION['TourId'];

//Verifico la situazione tiebreak
	$Confirmed=array();
    $movedCoaches=array();
	$Select = "SELECT EvMatchMode as MatchMode, tf.TfEvent, tf.TfMatchNo as MatchNo, tf2.TfMatchNo as OppMatchNo, tf.TfTbClosest Closest, tf2.TfTbClosest OppClosest,
            EvCheckGolds, EvCheckXNines, if(EvGoldsChars!='', EvGoldsChars, ToGoldsChars) as GoldChars, if(EvXNineChars!='', EvXNineChars, ToXNineChars) as XNineChars, 
			tf.TfTeam AS Team, tf.TfSubteam AS SubTeam, tf2.TfTeam AS OppTeam, tf2.TfSubTeam AS OppSubTeam, tf.TfConfirmed as IsConfirmed, 
			@PhaseMatch:=(GrPhase & EvMatchArrowsNo), 
			if(@PhaseMatch, EvElimEnds, EvFinEnds) FinEnds, if(@PhaseMatch, EvElimArrows, EvFinArrows) FinArrows, if(@PhaseMatch, EvElimSO, EvFinSO) FinSO,
			IF(tf.TfDateTime>=tf2.TfDateTime, tf.TfDateTime, tf2.TfDateTime) AS DateTime,
			IF(EvMatchMode=0,tf.TfScore,tf.TfSetScore) AS Score, tf.TfTie as Tie, tf.TfTieBreak as TbString, IF(EvMatchMode=0,tf2.TfScore,tf2.TfSetScore) as OppScore, tf2.TfTie as OppTie, tf2.TfTieBreak as OppTbString,
			tf.TfArrowString as ArrString, tf2.TfArrowString as OppArrString,  tf.TfSetPoints as SetPoint, tf2.TfSetPoints as OppSetPoint,
			tf.TfNotes as Notes, tf2.TfNotes as OppNotes,
			tf.TfIrmType as IrmType, tf2.TfIrmType as OppIrmType,
			EvFinalFirstPhase<=Grphase as FollowBye
		FROM TeamFinals AS tf
		INNER JOIN TeamFinals AS tf2 ON tf.TfEvent=tf2.TfEvent AND tf2.TfMatchNo=tf.TfMatchNo+1 AND tf.TfTournament=tf2.TfTournament and tf2.TfIrmType<15
		INNER JOIN Tournament ON ToId=tf.TfTournament
		INNER JOIN Events ON tf.TfEvent=EvCode AND tf.TfTournament=EvTournament AND EvTeamEvent=1
		INNER JOIN Grids ON tf.TfMatchNo=GrMatchNo AND ".(is_null($Phase) ? "GrMatchNo=" . intval($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1) . "" : "GrPhase=" . intval($Phase))."
		WHERE tf.TfTournament=" . StrSafe_DB($TourId) . " AND (tf.TfMatchNo % 2)=0 and tf.TfIrmType<15 ";

	if(!is_null($Event) and $Event!='')
		$Select .= "AND tf.TfEvent=" . StrSafe_DB($Event) . " ";
	$Select .= "ORDER BY tf.TfEvent, tf.TfMatchNo ";

	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)>0) {
		while ($MyRow=safe_fetch($Rs)) {
			if($MyRow->IsConfirmed) {
				$Confirmed[]=array('event' => $MyRow->TfEvent, 'matchno' => min($MyRow->MatchNo, $MyRow->OppMatchNo));
			}

            // first check the IRM of the opponents: both of them have an IRM status set bypass the procedure
            if(!($MyRow->IrmType and $MyRow->OppIrmType)) {
                // check if we need to "follow the bye"
                if($MyRow->IrmType and !$MyRow->OppIrmType) {
                    // opponent gets a bye only if not a DQB
                    if($MyRow->IrmType!=20) {
                        $SqlUpdate = "UPDATE TeamFinals SET TfTie=0, TfWinLose=0, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=". StrSafe_DB($MyRow->MatchNo) . " AND TfTournament=" . StrSafe_DB($TourId);
                        safe_w_sql($SqlUpdate);
                        $SqlUpdate = "UPDATE TeamFinals SET TfTie=2, TfWinLose=1, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=". StrSafe_DB($MyRow->OppMatchNo) . " AND TfTournament=" . StrSafe_DB($TourId);
                        safe_w_sql($SqlUpdate);
                    }
                } elseif(!$MyRow->IrmType and $MyRow->OppIrmType) {
                    // competitor gets a bye only if it is not a DQB
                    if ($MyRow->OppIrmType != 20) {
                        $SqlUpdate = "UPDATE TeamFinals SET TfTie=0, TfWinLose=0, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=" . StrSafe_DB($MyRow->OppMatchNo) . " AND TfTournament=" . StrSafe_DB($TourId);
                        safe_w_sql($SqlUpdate);
                        $SqlUpdate = "UPDATE TeamFinals SET TfTie=2, TfWinLose=1, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=" . StrSafe_DB($MyRow->MatchNo) . " AND TfTournament=" . StrSafe_DB($TourId);
                        safe_w_sql($SqlUpdate);
                    }
                } elseif($MyRow->FollowBye) {
                    if($MyRow->Team!=0 and $MyRow->OppTeam==0 and $MyRow->Score == 0 and $MyRow->OppScore==0) {
                        // if both scores are 0 and only one team set, sets a bye
                        $SqlUpdate = "UPDATE TeamFinals SET TfTie=0, TfWinLose=0, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=". StrSafe_DB($MyRow->OppMatchNo) . " AND TfTournament=" . StrSafe_DB($TourId);
                        safe_w_sql($SqlUpdate);
                        $SqlUpdate = "UPDATE TeamFinals SET TfTie=2, TfWinLose=1, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=". StrSafe_DB($MyRow->MatchNo) . " AND TfTournament=" . StrSafe_DB($TourId);
                        safe_w_sql($SqlUpdate);
                    } elseif($MyRow->Team==0 and $MyRow->OppTeam!=0 and $MyRow->Score == 0 and $MyRow->OppScore==0) {
                        $SqlUpdate = "UPDATE TeamFinals SET TfTie=0, TfWinLose=0, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=". StrSafe_DB($MyRow->MatchNo) . " AND TfTournament=" . StrSafe_DB($TourId);
                        safe_w_sql($SqlUpdate);
                        $SqlUpdate = "UPDATE TeamFinals SET TfTie=2, TfWinLose=1, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=". StrSafe_DB($MyRow->OppMatchNo) . " AND TfTournament=" . StrSafe_DB($TourId);
                        safe_w_sql($SqlUpdate);
                    }
                } elseif($MyRow->MatchMode!=0 and !$NoRecalc) {
                    //Se non ho il BYE, rigestisco il numero di set vinti e aggiorno il conteggio del totale (se i punti set sono valorizzati & le arrowstring sono vuote)
                    if(strlen(trim(str_replace("|","",$MyRow->SetPoint))) and strlen(trim(str_replace("|","",$MyRow->OppSetPoint))) and strlen(trim($MyRow->ArrString))==0 and strlen(trim($MyRow->OppArrString))==0) {
                        $AthSets=explode("|",$MyRow->SetPoint);
                        $OppSets=explode("|",$MyRow->OppSetPoint);
                        $AthSpBe=array();
                        $OppSpBe=array();
                        $AthScore=0;
                        $OppScore=0;
                        $AthWin=0;
                        $OppWin=0;
                        if(count($AthSets) == count($OppSets)) {
                            for($i=0;$i<count($AthSets);$i++) {
                                if(intval($AthSets[$i])>intval($OppSets[$i])) {
                                    $AthScore += 2;
                                    $AthWin++;
                                    $AthSpBe[]=2;
                                    $OppSpBe[]=0;
                                } elseif(intval($AthSets[$i])<intval($OppSets[$i])) {
                                    $OppScore += 2;
                                    $OppWin++;
                                    $AthSpBe[]=0;
                                    $OppSpBe[]=2;
                                } elseif(intval($AthSets[$i])!=0 and intval($OppSets[$i])!=0) {
                                    $AthScore += 1;
                                    $OppScore += 1;
                                    $AthSpBe[]=1;
                                    $OppSpBe[]=1;
                                }
                            }
                            $SqlUpdate = "UPDATE TeamFinals SET TfWinLose=".($AthScore>$MyRow->FinEnds ? 1 : 0).", TfSetPointsByEnd='".implode('|', $AthSpBe)."', TfSetScore=" . StrSafe_DB($AthScore) . ", TfWinnerSet=" . StrSafe_DB($AthWin) . ", TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=" . StrSafe_DB($MyRow->MatchNo) . " AND TfTournament=" . StrSafe_DB($TourId);
                            safe_w_sql($SqlUpdate);
                            $MyRow->Score=$AthScore;
                            $SqlUpdate = "UPDATE TeamFinals SET TfWinLose=".($OppScore>$MyRow->FinEnds ? 1 : 0).", TfSetPointsByEnd='".implode('|', $OppSpBe)."', TfSetScore=" . StrSafe_DB($OppScore) . ", TfWinnerSet=" . StrSafe_DB($OppWin) . ", TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=" . StrSafe_DB($MyRow->OppMatchNo) . " AND TfTournament=" . StrSafe_DB($TourId);
                            safe_w_sql($SqlUpdate);
                            $MyRow->OppScore=$OppScore;
                        }
                    }
                }

                // Se uno dei due è diverso da ZERO e non sono uguali oppure se è a set e differiscono di più di un punto
                if(($MyRow->Score!= 0 or $MyRow->OppScore!=0)
                    and (($MyRow->MatchMode==0 and $MyRow->Score != $MyRow->OppScore) or ($MyRow->MatchMode!=0 and abs($MyRow->Score-$MyRow->OppScore)>1))
                    and $MyRow->IrmType==0 and $MyRow->OppIrmType==0) {
                    //Azzero entrambi i flag di shootoff
                    $SqlUpdate = "UPDATE TeamFinals SET TfTie=0, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo IN (". StrSafe_DB($MyRow->MatchNo) . "," . StrSafe_DB($MyRow->OppMatchNo) . ") AND TfTournament=" . StrSafe_DB($TourId);
                    safe_w_sql($SqlUpdate);
                }

                // Se i punteggi sono uguali e diversi da ZERO entro nel dettaglio E le stringhe di frecce sono lunghe uguali e non vuote
                $MyRow->TbString=rtrim($MyRow->TbString);
                $MyRow->OppTbString=rtrim($MyRow->OppTbString);
                $M1Len=strlen($MyRow->TbString);
                $M2Len=strlen($MyRow->OppTbString);
                if($MyRow->Score!=0 and $MyRow->Score == $MyRow->OppScore and $M1Len!=0 and $M1Len%$MyRow->FinSO==0 and $M1Len==$M2Len) {
                    $WinnerId=-1;

                    $XChar=($MyRow->EvCheckGolds ? $MyRow->GoldChars : ($MyRow->EvCheckXNines? $MyRow->XNineChars : null));
                    $LTB=ValutaArrowStringSO(trim($MyRow->TbString), $XChar, $XChar?'A':null);
                    $RTB=ValutaArrowStringSO(trim($MyRow->OppTbString), $XChar, $XChar?'A':null);

                    if($MyRow->Closest) {
                        $WinnerId = $MyRow->MatchNo; // Closest is left
                    } elseif($MyRow->OppClosest) {
                        $WinnerId = $MyRow->OppMatchNo; // Closest is right
                    } elseif($LTB[0]>$RTB[0]) {
                        $WinnerId = $MyRow->MatchNo; // total of L is > than R
                    } elseif($LTB[0]<$RTB[0]) {
                        $WinnerId = $MyRow->OppMatchNo; // total of L is < than R
                    } elseif($LTB[3]>$RTB[3]) { // totals tie, check number of Xs
                        $WinnerId = $MyRow->MatchNo; // number of X for L is > than R
                    } elseif($LTB[3]<$RTB[3]) {
                        $WinnerId = $MyRow->OppMatchNo; // number of X for L is < than R
                    } else {
                        // X are tie, check the arrows
                        foreach($LTB[4] as $k => $v) {
                            if($v > $RTB[4][$k]) {
                                $WinnerId = $MyRow->MatchNo;
                                break;
                            }
                            if($v < $RTB[4][$k]) {
                                $WinnerId = $MyRow->OppMatchNo;
                                break;
                            }
                        }
                    }

                    //Azzero entrambi i flag di shootoff
                    $SqlUpdate = "UPDATE TeamFinals SET TfTie=0, TfWinLose=0, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo IN (". StrSafe_DB($MyRow->MatchNo) . "," . StrSafe_DB($MyRow->OppMatchNo) . ") AND TfTournament=" . StrSafe_DB($TourId);
                    safe_w_sql($SqlUpdate);
                    if ($WinnerId>-1) {
                        $SqlUpdate = "UPDATE TeamFinals SET TfTie=1, TfWinLose=1, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=". StrSafe_DB($WinnerId) . " AND TfTournament=" . StrSafe_DB($TourId);
                        safe_w_sql($SqlUpdate);
                        if($MyRow->MatchMode!=0 and $MyRow->Score == $MyRow->OppScore) {
                            $SqlUpdate = "UPDATE TeamFinals SET TfSetScore=TfSetScore+1, TfDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " WHERE TfEvent=" . StrSafe_DB($MyRow->TfEvent) . " AND TfMatchNo=". StrSafe_DB($WinnerId) . " AND TfTournament=" . StrSafe_DB($TourId);
                            safe_w_sql($SqlUpdate);
                        }
                    }
                }
            }
		}
	}

// Faccio i passaggi di fase
	$MyNextMatchNo='xx';
	$QueryFilter = '';

	$Select = "SELECT
			tf.TfEvent AS Event, tf.TfMatchNo, tf2.TfMatchNo OppMatchNo,
			GrPhase, tf.TfTeam AS Team,tf.TfSubTeam AS SubTeam, tf.TfCoach as Coach, tf2.TfTeam AS OppTeam,tf2.TfSubTeam AS OppSubTeam, tf2.TfCoach as OppCoach, 
			IF(EvMatchMode=0,tf.TfScore,tf.TfSetScore) AS Score, tf.TfTie as Tie, IF(EvMatchMode=0,tf2.TfScore,tf2.TfSetScore) as OppScore, tf2.TfTie as OppTie,
			IF(GrPhase>2, FLOOR(tf.TfMatchNo/2),FLOOR(tf.TfMatchNo/2)-2) AS NextMatchNo
		FROM TeamFinals AS tf
		INNER JOIN TeamFinals AS tf2 ON tf.TfEvent=tf2.TfEvent AND tf.TfMatchNo=IF((tf.TfMatchNo % 2)=0,tf2.TfMatchNo-1,tf2.TfMatchNo+1) AND tf.TfTournament=tf2.TfTournament
		INNER JOIN Events ON tf.TfEvent=EvCode AND tf.TfTournament=EvTournament AND EvTeamEvent=1
		INNER JOIN Grids ON tf.TfMatchNo=GrMatchNo AND ".(is_null($Phase) ? "GrMatchNo=" . StrSafe_DB(($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1)) : "GrPhase=" . StrSafe_DB($Phase))."
		LEFT JOIN Countries ON tf.TfTeam=CoId AND tf.TfTournament=CoTournament
		WHERE tf.TfTournament=" . StrSafe_DB($TourId) . " AND (tf.TfMatchNo % 2)=0 ";

	if(!is_null($Event) and $Event!='') {
		$Select .= "AND tf.TfEvent=" . StrSafe_DB($Event) . " ";
	}
	$Select .= "ORDER BY tf.TfEvent, NextMatchNo ASC, Score DESC, Tie DESC ";

	$Rs=safe_r_sql($Select);
	//print $Select . '<br>';exit;

	// conterrà i parametri per il calcolo delle RankFinal
	$coppie=array();

	$AthPropTs = NULL;

	if (safe_num_rows($Rs)>0) {
		$AthPropTs = date('Y-m-d H:i:s');
		while ($MyRow=safe_fetch($Rs)) {
		/*
		 * Dato che potrei avere più fasi gestite da questa funzione, io ricavo le coppie
		 * per la RankFinal dalle righe del recordset.
		 * Visto che mi imbatterò più volte nella stessa coppia evento/fase, solo se la coppia
		 * non l'ho già contata la aggiungo nel vettore.
		 */
			if (!in_array($MyRow->Event.'@'.$MyRow->GrPhase,$coppie)) {
				$coppie[]=$MyRow->Event.'@'.$MyRow->GrPhase;
			}

			$AthProp = '0';
			$AthSubProp = '0';
            $CoachProp = '0';
			$WhereProp = '0';
			$WhereSubProp = '0';

			// Flag for winlose
			$WinLose=-1;

			if (intval($MyRow->Score)>intval($MyRow->OppScore) or $MyRow->Tie==2 or (intval($MyRow->Score)==intval($MyRow->OppScore) and intval($MyRow->Tie)>intval($MyRow->OppTie))) {
				$WinLose=$MyRow->TfMatchNo;
				if ($MyRow->GrPhase>=2) {
					$MyUpQuery = "UPDATE TeamFinals SET
						TfTeam =" . StrSafe_DB($MyRow->Team) . ",
						TfSubTeam =" . StrSafe_DB($MyRow->SubTeam) . ",
						TfCoach =" . StrSafe_DB($MyRow->Coach) . ",
						TfDateTime=" . StrSafe_DB($AthPropTs) . "
						WHERE TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfMatchNo=" . StrSafe_DB($MyRow->NextMatchNo) . " AND TfTournament=" . StrSafe_DB($TourId) . " ";
					safe_w_sql($MyUpQuery);
					// update FinOdfTiming data
					if(safe_w_affected_rows()) {
						updateOdfTiming('S', $TourId, $MyRow->Event, 1, $MyRow->NextMatchNo);
                        if($MyRow->Coach!=0) {
                            $movedCoaches[]=array('event' => $MyRow->Event, 'matchno' => $MyRow->NextMatchNo);
                        }
					}

					$AthProp=$MyRow->Team;
					$AthSubProp=$MyRow->SubTeam;
                    $CoachProp=$MyRow->Coach;
					$WhereProp=$MyRow->OppTeam;
					$WhereSubProp=$MyRow->OppSubTeam;

					if($MyRow->GrPhase==2) {
						$MyUpQuery = "UPDATE TeamFinals SET
							TfTeam =" . StrSafe_DB($MyRow->OppTeam) . ",
							TfSubTeam =" . StrSafe_DB($MyRow->OppSubTeam) . ",
							TfCoach =" . StrSafe_DB($MyRow->OppCoach) . ",
							TfDateTime=" . StrSafe_DB($AthPropTs) . "
							WHERE TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfMatchNo=" . StrSafe_DB(($MyRow->NextMatchNo+2)) . " AND TfTournament=" . StrSafe_DB($TourId) . " ";
						$RsUp=safe_w_sql($MyUpQuery);
						if(safe_w_affected_rows()) {
							// update FinOdfTiming data
							updateOdfTiming('S', $TourId, $MyRow->Event, 1, $MyRow->NextMatchNo+2);
                            if($MyRow->OppCoach!=0) {
                                $movedCoaches[]=array('event' => $MyRow->Event, 'matchno' => $MyRow->NextMatchNo+2);
                            }
						}
					}
				}
			} elseif (intval($MyRow->Score)<intval($MyRow->OppScore) or $MyRow->OppTie==2 or (intval($MyRow->Score)==intval($MyRow->OppScore) and intval($MyRow->Tie)<intval($MyRow->OppTie))) {
				$WinLose=$MyRow->OppMatchNo;
				if ($MyRow->GrPhase>=2) {
					$MyUpQuery = "UPDATE TeamFinals SET
						TfTeam =" . StrSafe_DB($MyRow->OppTeam) . ",
						TfSubTeam =" . StrSafe_DB($MyRow->OppSubTeam) . ",
						TfCoach =" . StrSafe_DB($MyRow->OppCoach) . ",
						TfDateTime=" . StrSafe_DB($AthPropTs) . "
						WHERE TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfMatchNo=" . StrSafe_DB($MyRow->NextMatchNo) . " AND TfTournament=" . StrSafe_DB($TourId) . " ";
					$RsUp=safe_w_sql($MyUpQuery);
					if(safe_w_affected_rows()) {
						// update FinOdfTiming data
						updateOdfTiming('S', $TourId, $MyRow->Event, 1, $MyRow->NextMatchNo);
                        if($MyRow->OppCoach!=0) {
                            $movedCoaches[]=array('event' => $MyRow->Event, 'matchno' => $MyRow->NextMatchNo);
                        }
					}

					$AthProp=$MyRow->OppTeam;
					$AthSubProp=$MyRow->OppSubTeam;
                    $CoachProp=$MyRow->OppCoach;
					$WhereProp=$MyRow->Team;
					$WhereSubProp=$MyRow->SubTeam;
					if($MyRow->GrPhase==2)
					{
						$MyUpQuery = "UPDATE TeamFinals SET
							TfTeam =" . StrSafe_DB($MyRow->Team) . ",
							TfSubTeam =" . StrSafe_DB($MyRow->SubTeam) . ",
							TfCoach =" . StrSafe_DB($MyRow->Coach) . ",
							TfDateTime=" . StrSafe_DB($AthPropTs) . "
							WHERE TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfMatchNo=" . StrSafe_DB(($MyRow->NextMatchNo+2)) . " AND TfTournament=" . StrSafe_DB($TourId) . " ";
						$RsUp=safe_w_sql($MyUpQuery);
						if(safe_w_affected_rows()) {
							// update FinOdfTiming data
							updateOdfTiming('S', $TourId, $MyRow->Event, 1, $MyRow->NextMatchNo+2);
                            if($MyRow->Coach!=0) {
                                $movedCoaches[]=array('event' => $MyRow->Event, 'matchno' => $MyRow->NextMatchNo+2);
                            }
						}
					}
				}
			} else {
				if ($MyRow->GrPhase>=2) {
					$MyUpQuery = "UPDATE TeamFinals SET
						TfTeam = 0,
						TfSubTeam =0,
						TfCoach = 0,
						TfDateTime=" . StrSafe_DB($AthPropTs) . "
						WHERE TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfMatchNo=" . StrSafe_DB($MyRow->NextMatchNo) . " AND TfTournament=" . StrSafe_DB($TourId) . " ";
					safe_w_sql($MyUpQuery);
					if(safe_w_affected_rows()) {
						// update FinOdfTiming data
						updateOdfTiming('S', $TourId, $MyRow->Event, 1, $MyRow->NextMatchNo);
					}

					if($MyRow->GrPhase==2) {
						$MyUpQuery = "UPDATE TeamFinals SET
							TfWinLose = 0,
							TfTeam = 0,
							TfSubTeam = 0,
							TfCoach = 0,
							TfDateTime=" . StrSafe_DB($AthPropTs) . "
							WHERE TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfMatchNo=" . StrSafe_DB(($MyRow->NextMatchNo+2)) . " AND TfTournament=" . StrSafe_DB($TourId) . " ";
						safe_w_sql($MyUpQuery);
						if(safe_w_affected_rows()) {
							// update FinOdfTiming data
							updateOdfTiming('S', $TourId, $MyRow->Event, 1, $MyRow->NextMatchNo+2);
						}
					}
				}
			}

			// reset winners of previous matches
			safe_w_sql("update TeamFinals set TfWinLose=if(TfMatchNo=$WinLose, 1, 0) WHERE TfMatchNo in (" . StrSafe_DB($MyRow->TfMatchNo) . "," . StrSafe_DB($MyRow->OppMatchNo) . ") AND TfEvent=" . StrSafe_DB($MyRow->Event) . " AND TfTournament=" . StrSafe_DB($TourId));
			if(safe_w_affected_rows()) {
				// update FinOdfTiming data
				updateOdfTiming('U', $TourId, $MyRow->Event, 1, $WinLose);
			}

			$OldId=($AthProp!=0 ? StrSafe_DB($WhereProp) : StrSafe_DB($MyRow->Team) . ',' . StrSafe_DB($MyRow->OppTeam));
			$OldSubId=($AthSubProp!=0 ? StrSafe_DB($WhereSubProp) : StrSafe_DB($MyRow->SubTeam) . ',' . StrSafe_DB($MyRow->OppSubTeam));

			$Update
				= "UPDATE TeamFinals SET "
				. "TfTeam=" . StrSafe_DB($AthProp) . ", "
				. "TfSubTeam=" . StrSafe_DB($AthSubProp) . ", "
                . "TfCoach=" . StrSafe_DB($CoachProp) . ", "
				. "TfDateTime=" . StrSafe_DB($AthPropTs) . " "
				. ($AthProp==0 ? ', TfWinLose=0 ' : '')
				. "WHERE TfTeam IN (" . $OldId . ") "
				. "AND TfSubTeam IN (" . $OldSubId . ") "
				. "AND TfTournament=" . StrSafe_DB($TourId) . " "
				. "AND TfEvent=" . StrSafe_DB($MyRow->Event) . " "
				. "AND tfMatchNo<"	. StrSafe_DB($MyRow->NextMatchNo) . " ";

			if($OldId!="'0'") {
				$RsProp = safe_w_sql($Update);
			}
		}
	}

// se ho delle coppie calcolo per queste la RankFinal
	if (count($coppie)>0) {
		// manages the loosers if there is a looser event
		$coppie=array_merge($coppie, moveToNextPhaseLoosersTeam($coppie, $TourId));

		Obj_RankFactory::create('FinalTeam',array('tournament'=> $TourId, 'eventsC'=>$coppie))->calculate();
		foreach($coppie as $cp) {
			runJack("FinRankUpdate", $TourId, array("Event"=>substr($cp, 0, strpos($cp, '@')), "Team"=>1, "TourId"=>$TourId));
		}
	}

	// trigger the appropriate jack
	foreach($Confirmed as $Item) {
		runJack("MatchConfirmed", $TourId, array("Event"=>$Item['event'], "Team"=>1, "MatchNo"=>$Item['matchno'], "TourId"=>$TourId));
	}
    foreach ($movedCoaches as $Item) {
        runJack("CoachAssigned", $TourId, array("Event"=>$Item['event'], "Team"=>1, "MatchNo"=>$Item['matchno'], "TourId"=>$TourId));
    }

	return $AthPropTs;
}


/**
 * @param string $Type the type of update: <ul><li>S: Startlist (change of opponent)<li>G: Getting Ready (Live in ianseo)<li>L: Live(First arrow arriving)<li>U: Unconfirmed (winner)<li>C: Unofficial (Confirm by UI)<li>O: Official (confirmed in scoring)<li>R: Reset dates</ul>
 * @param integer $Tourid Competition ID
 * @param string $Event Event Code
 * @param integer $TeamEvent 1 is team, 0 is individual
 * @param mixed $Matchno matchno, can be an integer or an array of integers
 * @return void
 */
function updateOdfTiming($Type, $TourId, $Event, $TeamEvent, $Matchno) {
	$date=date('Y-m-d H:i:s');
	switch($Type[0]) {
		case 'S': // ODF Startlist: every time an opponent is changed
			if(!is_array($Matchno)) {
				$Matchno=[$Matchno];
			}
			$Values=[];
			foreach($Matchno as $m) {
				$m1=($m%2 ? $m-1 : $m);
				$m2=$m1+1;
				$Values[]="({$TourId}, '{$Event}', $TeamEvent, $m1, '{$date}')";
				$Values[]="({$TourId}, '{$Event}', $TeamEvent, $m2, '{$date}')";
			}
			safe_w_sql("insert into FinOdfTiming (FinOdfTournament, FinOdfEvent, FinOdfTeamEvent, FinOdfMatchno, FinOdfStartlist) 
				values ".implode(',', $Values)."
				on duplicate key update FinOdfStartlist='{$date}'");
			break;
		case 'G': // ODF Getting ready: triggered when the "live" button is pressed in ianseo
			// date is accepted only if unconfirmed, unofficial and offical dates are all empty
			$m1=($Matchno%2 ? $Matchno-1 : $Matchno);
			$m2=$m1+1;
			safe_w_sql("update FinOdfTiming 
				set FinOdfGettingReady='{$date}', FinOdfUnofficial=0, FinOdfOfficial=0
				where FinOdfTournament={$TourId} and FinOdfEvent='{$Event}' and FinOdfTeamEvent={$TeamEvent} and FinOdfMatchno in ($m1, $m2)
				and FinOdfUnconfirmed=0 and FinOdfUnofficial=0 and FinOdfOfficial=0");
			break;
		case 'L': // ODF Live: triggered after first arrow enters
			$m1=($Matchno%2 ? $Matchno-1 : $Matchno);
			$m2=$m1+1;
			safe_w_sql("update FinOdfTiming 
				set FinOdfLive=if(FinOdfLive=0, '{$date}', FinOdfLive), FinOdfUnconfirmed=0, FinOdfUnofficial=0, FinOdfOfficial=0
				where FinOdfTournament={$TourId} and FinOdfEvent='{$Event}' and FinOdfTeamEvent={$TeamEvent} and FinOdfMatchno in ($m1, $m2)
				");
			break;
		case 'U': // ODF Unconfirmed: triggered as soon as ianseo has a winner
			$m1=($Matchno%2 ? $Matchno-1 : $Matchno);
			$m2=$m1+1;
			safe_w_sql("update FinOdfTiming 
				set FinOdfUnconfirmed='{$date}', FinOdfUnofficial=0, FinOdfOfficial=0
				where FinOdfTournament={$TourId} and FinOdfEvent='{$Event}' and FinOdfTeamEvent={$TeamEvent} and FinOdfMatchno in ($m1, $m2)");
			break;
		case 'C': // ODF Unofficial (Confirmed): triggered after a confirm in spotting/scoring UI
			$m1=($Matchno%2 ? $Matchno-1 : $Matchno);
			$m2=$m1+1;
			safe_w_sql("update FinOdfTiming 
				set FinOdfUnofficial='{$date}', FinOdfOfficial=0
				where FinOdfTournament={$TourId} and FinOdfEvent='{$Event}' and FinOdfTeamEvent={$TeamEvent} and FinOdfMatchno in ($m1, $m2)");
			break;
		case 'O': // ODF Official: triggered upon confirming score
			$m1=($Matchno%2 ? $Matchno-1 : $Matchno);
			$m2=$m1+1;
			safe_w_sql("update FinOdfTiming 
				set FinOdfOfficial='{$date}'
				where FinOdfTournament={$TourId} and FinOdfEvent='{$Event}' and FinOdfTeamEvent={$TeamEvent} and FinOdfMatchno in ($m1, $m2)");
			break;
		case 'R': // RESET Unconfirmed, Unofficial and Official times
			$m1=($Matchno%2 ? $Matchno-1 : $Matchno);
			$m2=$m1+1;
			safe_w_sql("update FinOdfTiming 
				set FinOdfUnconfirmed=0, FinOdfUnofficial=0, FinOdfOfficial=0
				where FinOdfTournament={$TourId} and FinOdfEvent='{$Event}' and FinOdfTeamEvent={$TeamEvent} and FinOdfMatchno in ($m1, $m2)");
			break;
	}
}