<?php
global $CFG;
require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error'=>1, 'msg'=>get_text('ErrGenericError','Errors'));
if(!CheckTourSession() or !hasACL(AclCompetition, AclReadWrite) or empty($_REQUEST['act']) or !isset($_REQUEST['team'])) {
	JsonOut($JSON);
}

$Team=intval((intval($_REQUEST['team'])!=0));
$Event=$_REQUEST['event']??'';

$IsRunArchery=($_SESSION['TourType']==48);

switch($_REQUEST['act']) {
	case 'update':
		if(empty($_REQUEST['event']) or empty($_REQUEST['field']) or !isset($_REQUEST['value'])) {
			JsonOut($JSON);
		}
		switch($_REQUEST['field']) {
			case 'EvEventName':
			case 'EvQualPrintHead':
			case 'EvFinalPrintHead':
			case 'EvGolds':
			case 'EvXNine':
			case 'EvGoldsChars':
			case 'EvXNineChars':
			case 'EvDistance':
			case 'EvRecCategory':
			case 'EvWaCategory':
			case 'EvTourRules':
			case 'EvCodeParent':
			case 'EvOdfCode':
			case 'EvOdfGender':
				// text fields
				safe_w_SQL("update Events set {$_REQUEST['field']}=".StrSafe_DB($_REQUEST['value'])." where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
				break;
			case 'EvFinalFirstPhase':
			case 'EvProgr':
			case 'EvSession':
			case 'EvPrint':
			case 'EvWinnerFinalRank':
			case 'EvNumQualified':
			case 'EvFirstQualified':
			case 'EvFinalTargetType':
			case 'EvTargetSize':
			case 'EvFinalAthTarget':
			case 'EvMatchMultipleMatches':
			case 'EvElimType':
			case 'EvElim1':
			case 'EvE1Ends':
			case 'EvE1Arrows':
			case 'EvE1SO':
			case 'EvElim2':
			case 'EvE2Ends':
			case 'EvE2Arrows':
			case 'EvE2SO':
			case 'EvPartialTeam':
			case 'EvMultiTeam':
			case 'EvMultiTeamNo':
			case 'EvMixedTeam':
			case 'EvTeamCreationMode':
			case 'EvMaxTeamPerson':
			case 'EvRunning':
			case 'EvMatchMode':
			case 'EvMatchArrowsNo':
			case 'EvElimEnds':
			case 'EvElimArrows':
			case 'EvElimSO':
			case 'EvFinEnds':
			case 'EvFinArrows':
			case 'EvFinSO':
			case 'EvMedals':
			case 'EvArrowPenalty':
			case 'EvLoopPenalty':
				// number fields
				safe_w_SQL("update Events set {$_REQUEST['field']}=".intval($_REQUEST['value'])." where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
				break;
			case 'EvShootOff':
			case 'EvE1ShootOff':
			case 'EvE2ShootOff':
			case 'EvIsPara':
				// checkbox  fields
				safe_w_SQL("update Events set {$_REQUEST['field']}=".(intval($_REQUEST['value']) ? 1 : 0)." where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
				break;
			default:
				JsonOut($JSON);
		}

		$JSON['updated']=0;
		if(safe_w_affected_rows()) {
			// something changed so check what
			$JSON['updated']=1;
			switch($_REQUEST['field']) {
				case 'EvElim2':
				case 'EvElim1':
					checkRunPhases($Team, $Event);
					break;
				case 'EvFinalFirstPhase':
					if($IsRunArchery) {
						$JSON['updates']=[
							[
								'name'=>'EvElim1',
								'disabled'=>(intval($_REQUEST['value'])!=2),
							],
							[
								'name'=>'EvElim2',
								'disabled'=>(intval($_REQUEST['value'])==0),
							],
						];
						checkRunPhases($Team, $Event);
					} else {
						require_once('Common/Lib/Fun_Phases.inc.php');
						require_once('Common/Fun_Sessions.inc.php');
						$NewPhase=intval($_REQUEST['value']);
						$NumQualified=numQualifiedByPhase($NewPhase);
						$GridPhase=valueFirstPhase($NewPhase);

						// controllo che esista l'evento...
						$q = safe_r_sql("select EvElimType from Events WHERE EvCode=" . StrSafe_DB($Event) . " AND EvTeamEvent=$Team AND EvTournament={$_SESSION['TourId']}");
						$EVENT=safe_fetch($q);
						if(!$EVENT) {
							JsonOut($JSON);
						}

						// aggiorno la fase
						$Update = "UPDATE Events 
							SET EvFinalFirstPhase=$NewPhase, EvNumQualified=$NumQualified 
							WHERE EvCode=" . StrSafe_DB($Event) . " AND EvTeamEvent=$Team AND EvTournament={$_SESSION['TourId']}";
						$Rs=safe_w_sql($Update);

						$JSON['error']=0;

						if (safe_w_affected_rows()) {
							// Distruggo la griglia
							if($Team) {
								$Delete = "DELETE FROM TeamFinals WHERE TfEvent=" . StrSafe_DB($Event) . " AND TfTournament={$_SESSION['TourId']}";
							} else {
								$Delete = "DELETE FROM Finals WHERE FinEvent=" . StrSafe_DB($Event) . " AND FinTournament={$_SESSION['TourId']}";
							}
							$Rs=safe_w_sql($Delete);

							if($EVENT->EvElimType==5) {
								// Round Robin!!
								// starts removing the exceeding people ;)
								safe_w_sql("delete from RoundRobinParticipants 
									where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartLevel=0 and RrPartGroup=0 and RrPartDestItem>{$NumQualified}");
								// check if the final level => Brackets is needed!
								if($NewPhase) {
									// inserts all the items
									$sqlPart=array();
									for($n=1;$n<=$NumQualified;$n++) {
										$sqlPart[]="({$_SESSION['TourId']}, $Team, ".StrSafe_DB($Event).", 0, 0, $n)";
									}
									safe_w_SQL("insert ignore into RoundRobinParticipants (RrPartTournament, RrPartTeam, RrPartEvent, RrPartLevel, RrPartGroup, RrPartDestItem) 
										values ".implode(', ', $sqlPart));
									safe_w_sql("update ignore RoundRobinParticipants set RrPartSourceLevel=0, RrPartSourceGroup=0, RrPartSourceRank=0, RrPartDestItem=0, RrPartParticipant=0, RrPartSubTeam=0 
										where (RrPartTournament, RrPartTeam, RrPartEvent, RrPartLevel, RrPartGroup) = ({$_SESSION['TourId']}, $Team, ".StrSafe_DB($Event).", 0, 0)");
								}
							}

							if($GridPhase) {
								// Deletes unused warmups
								$delSchedule = "DELETE FROM FinWarmup USING
							        Events
							        INNER JOIN FinSchedule ON EvCode = FsEvent AND EvTeamEvent = FsTeamEvent AND EvTournament = FsTournament
							        INNER JOIN Grids ON GrMatchNo = FsMatchNo
							        INNER JOIN FinWarmup on FsEvent=FwEvent and FsTeamEvent=FwTeamEvent and FsTournament=FwTournament and FsScheduledDate=FwDay and FsScheduledTime=FwMatchTime
							        WHERE GrPhase > $GridPhase
							        AND EvTournament={$_SESSION['TourId']} AND EvTeamEvent=$Team AND EvCode=" . StrSafe_DB($Event);
								$RsDel=safe_w_sql($delSchedule);

								// deletes schedule
								$delSchedule = "DELETE FROM FinSchedule USING
							        Events
							        INNER JOIN FinSchedule ON EvCode = FsEvent AND EvTeamEvent = FsTeamEvent AND EvTournament = FsTournament
							        INNER JOIN Grids ON GrMatchNo = FsMatchNo
							        WHERE GrPhase > $GridPhase
							        AND EvTournament={$_SESSION['TourId']} AND EvTeamEvent=$Team AND EvCode=" . StrSafe_DB($Event);
								$RsDel=safe_w_sql($delSchedule);

								// Re-create the brackets
								if($Team) {
									$Insert = "INSERT INTO TeamFinals (TfEvent,TfMatchNo,TfTournament,TfDateTime) 
								        SELECT EvCode,GrMatchNo,{$_SESSION['TourId']}," . StrSafe_DB(date('Y-m-d H:i:s')) . " 
								        FROM Events 
								        INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0
								        INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) 
								        WHERE EvTournament={$_SESSION['TourId']} AND EvTeamEvent=$Team AND EvCode=" . StrSafe_DB($Event) . " ";
								} else {
									$Insert = "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) 
								        SELECT EvCode,GrMatchNo,{$_SESSION['TourId']}," . StrSafe_DB(date('Y-m-d H:i:s')) . " 
								        FROM Events 
								        INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0
								        INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) 
								        WHERE EvTournament={$_SESSION['TourId']} AND EvTeamEvent=$Team AND EvCode=" . StrSafe_DB($Event) . " ";
								}
								$RsDel=safe_w_sql($Insert);
							} else {
								// deletes warmups
								$delSchedule = "DELETE FROM FinWarmup WHERE FwTournament={$_SESSION['TourId']} AND FwTeamEvent=$Team AND FwEvent=" . StrSafe_DB($Event);
								$RsDel=safe_w_sql($delSchedule);

								// deletes schedule
								$delSchedule = "DELETE FROM FinSchedule WHERE FsTournament={$_SESSION['TourId']} AND FsTeamEvent=$Team AND FsEvent=" . StrSafe_DB($Event);
								$RsDel=safe_w_sql($delSchedule);
							}

							// Azzero il flag di spareggio
							ResetShootoff($Event,$Team,3);


							// TODO: needs to check the descendent events!
							$q=safe_r_sql("select * from Events where EvFinalFirstPhase>" . StrSafe_DB($_REQUEST['NewPhase']/2) . " and EvTeamEvent=$Team AND EvCodeParent=" . StrSafe_DB($Event) . " and EvTournament={$_SESSION['TourId']}");
							while($r=safe_fetch($q)) {
								$JSON['events']=array_merge(deleteEvent($r->EvCode), $JSON['events']);
							}
						}
					}
					break;
			}
		}
		$JSON['error']=0;
		break;
	case 'add':
		if(empty($Event)) {
			$JSON['msg']=get_text('ErrInvalidCode', 'Errors');
			$JSON['name']='event';
			JsonOut($JSON);
		}
		// check if the code is not already used
		$q=safe_r_sql("select EvCode from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
		if(safe_num_rows($q)) {
			$JSON['msg']=get_text('ErrCodeExists', 'Errors');
			$JSON['name']='event';
			JsonOut($JSON);
		}

        $q=safe_r_sql("select ToGoldsChars,ToGolds,ToXNineChars,ToXNine from Tournament where ToId={$_SESSION['TourId']}");
        $r=safe_fetch($q);

		$SQL=[
			"EvTournament={$_SESSION['TourId']}",
			"EvTeamEvent=$Team",
			"EvGoldsChars='$r->ToGoldsChars'",
			"EvGolds='$r->ToGolds'",
			"EvXNineChars='$r->ToXNineChars'",
			"EvXNine='$r->ToXNine'",
		];
		$EvEventName=$_REQUEST['EvEventName']??'';
		if(!$EvEventName) {
			$JSON['name']='EvEventName';
			JsonOut($JSON);
		}
		$EvProgr=$_REQUEST['EvProgr']??0;
		if(!$EvProgr) {
			$JSON['name']='EvProgr';
			JsonOut($JSON);
		}
		$EvDistance=intval($_REQUEST['EvDistance']??0);
		$EvTargetSize=intval($_REQUEST['EvTargetSize']??0);
		$EvFinalFirstPhase=intval($_REQUEST['EvFinalFirstPhase']??0);
		$SQL[]="EvCode=".StrSafe_DB($Event);
		$SQL[]="EvEventName=".StrSafe_DB($EvEventName);
		$SQL[]="EvDistance=$EvDistance";
		$SQL[]="EvTargetSize=$EvTargetSize";
		$SQL[]="EvFinalFirstPhase=$EvFinalFirstPhase";

		if($IsRunArchery) {
			$EvElimType=intval($_REQUEST['EvElimType']??0);
			$EvFinEnds=intval($_REQUEST['EvFinEnds']??0);
			$EvE1Arrows=intval($_REQUEST['EvE1Arrows']??0);
			$EvFinArrows=intval($_REQUEST['EvFinArrows']??0);
			$EvArrowPenalty=intval($_REQUEST['EvArrowPenalty']??0);
			$EvLoopPenalty=intval($_REQUEST['EvLoopPenalty']??0);
			$EvElim1=intval($_REQUEST['EvElim1']??0);
			$EvElim2=intval($_REQUEST['EvElim2']??0);
			foreach(['EvFinEnds', 'EvE1Arrows', 'EvFinArrows'] as $fld) {
				if(!${$fld}) {
					$JSON['name']=$fld;
					JsonOut($JSON);
				}
				$SQL[]="$fld=".${$fld};
			}
			$SQL[]="EvElimType=$EvElimType";
			$SQL[]="EvArrowPenalty=$EvArrowPenalty";
			$SQL[]="EvLoopPenalty=$EvLoopPenalty";
			$SQL[]="EvElim1=$EvElim1";
			$SQL[]="EvElim2=$EvElim2";
		} else {
			$EvIsPara=intval($_REQUEST['EvIsPara']??0);
			$EvMatchMode=intval($_REQUEST['EvMatchMode']??0);
			$EvFinalTargetType=intval($_REQUEST['EvFinalTargetType']??0);
			if(!$EvFinalTargetType) {
				$JSON['name']='EvFinalTargetType';
				JsonOut($JSON);
			}
			$SQL[]="EvIsPara=$EvIsPara";
			$SQL[]="EvMatchMode=$EvMatchMode";
			$SQL[]="EvFinalTargetType=$EvFinalTargetType";
			if($Team) {
				$SQL[] = "EvElimEnds=4,EvElimArrows=6,EvElimSO=3,EvFinEnds=4,EvFinArrows=6,EvFinSO=3 ";
			} else {
				$SQL[] = "EvElimEnds=5,EvElimArrows=3,EvElimSO=1,EvFinEnds=5,EvFinArrows=3,EvFinSO=1 ";
			}
		}

		safe_w_sql("insert into Events set ".implode(',', $SQL));
		if(safe_w_affected_rows()) {
			set_qual_session_flags();
			// Creo la griglia
			if($EvFinalFirstPhase) {
				if($Team) {
					$Insert = "INSERT INTO TeamFinals (TfEvent,TfMatchNo,TfTournament,TfDateTime)  
						SELECT EvCode,GrMatchNo,{$_SESSION['TourId']}," . StrSafe_DB(date('Y-m-d H:i:s')) . " 
						FROM Events 
				        inner join Phases on PhId=EvFinalFirstPhase and (PhIndTeam & 2)=2
				        INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) 
				        WHERE EvCode=" . StrSafe_DB($Event) . " AND EvTeamEvent=1 AND EvTournament={$_SESSION['TourId']}";
				} else {
					$Insert = "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime)  
						SELECT EvCode,GrMatchNo,{$_SESSION['TourId']}," . StrSafe_DB(date('Y-m-d H:i:s')) . " 
						FROM Events 
				        inner join Phases on PhId=EvFinalFirstPhase and (PhIndTeam & 1)=1
				        INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) 
				        WHERE EvCode=" . StrSafe_DB($Event) . " AND EvTeamEvent=0 AND EvTournament={$_SESSION['TourId']}";
				}
				safe_w_sql($Insert);
			}
		}
		$JSON['error']=0;
		break;
	case 'delete':
		// start removing event and eventclass
		safe_w_sql("delete from Events where EvTeamEvent=$Team and EvTournament={$_SESSION['TourId']} and EvCode=".StrSafe_DB($Event));
		safe_w_sql("delete from EventClass where ".($Team ? 'EcTeamEvent>=1' : 'EcTeamEvent=0')." and EcTournament={$_SESSION['TourId']} and EcCode=".StrSafe_DB($Event));
		// Delete run archery content
		safe_w_sql("delete from RunArcheryRank where RarTeam=$Team and RarTournament={$_SESSION['TourId']} and RarEvent=".StrSafe_DB($Event));
		safe_w_sql("delete from RunArchery where RaTeam=$Team and RaTournament={$_SESSION['TourId']} and RaEvent=".StrSafe_DB($Event));
		safe_w_sql("delete from RunArcheryParticipants where RapTeamEvent=$Team and RapTournament={$_SESSION['TourId']} and RapEvent=".StrSafe_DB($Event));
		// delete scheduled items
		safe_w_sql("delete from FinSchedule where FSTeamEvent=$Team and FsTournament={$_SESSION['TourId']} and FSEvent=".StrSafe_DB($Event));

		// Deletes teams and team components
		if($Team) {
			safe_w_sql("delete from Teams where TeTournament={$_SESSION['TourId']} and TeEvent=".StrSafe_DB($Event));
			safe_w_sql("delete from TeamComponent where TcTournament={$_SESSION['TourId']} and TcEvent=".StrSafe_DB($Event));
			safe_w_sql("delete from TeamFinComponent where TfcTournament={$_SESSION['TourId']} and TfcEvent=".StrSafe_DB($Event));
			safe_w_sql("delete from FinSchedule where FsTournament={$_SESSION['TourId']} and FSTeamEvent=1 and FsEvent=".StrSafe_DB($Event));
		}
		$JSON['error']=0;
		break;
	default:
		JsonOut($JSON);
}

JsonOut($JSON);

function checkRunPhases($Team, $Event) {
	$q=safe_r_sql("select EvFinalFirstPhase, EvElim1, EvElim2, EvFinEnds from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
	if(!($r=safe_fetch($q))) {
		return;
	}
	$SEMI=$r->EvElim1;
	$FINS=$r->EvElim2;
	switch($r->EvFinalFirstPhase) {
		case '0':
			safe_w_sql("delete from RunArcheryRank where RarTournament={$_SESSION['TourId']} and RarPhase>0 and RarTeam=$Team and RarEvent=".StrSafe_DB($Event));
			safe_w_sql("delete from RunArchery where RaTournament={$_SESSION['TourId']} and RaPhase>0 and RaTeam=$Team and RaEvent=".StrSafe_DB($Event));
			safe_w_sql("update Events set EvElim1=0, EvElim2=0 where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
			break;
			$SEMI=0;
			$FINS=0;
		case '1':
			safe_w_sql("delete from RunArcheryRank where RarTournament={$_SESSION['TourId']} and RarPhase=2 and RarTeam=$Team and RarEvent=".StrSafe_DB($Event));
			safe_w_sql("delete from RunArchery where RaTournament={$_SESSION['TourId']} and RaPhase=2 and RaTeam=$Team and RaEvent=".StrSafe_DB($Event));
			safe_w_sql("update Events set EvElim1=0 where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
			$SEMI=0;
			break;
	}
	// as this function is called only on a change, it is safe to (re)create all rows
	$SemiPools=0;
	if($SEMI) {
        // Assign people to semifinals
		$SemiPools=ceil($SEMI/10);
		// elements are position => pool
		if($SemiPools==2) {
			$Serpent=[1=>1, 2=>2, 3=>2, 4=>1, 5=>1, 6=>2, 7=>2, 8=>1, 9=>1, 10=>2, 11=>2, 12=>1, 13=>1, 14=>2, 15=>2, 16=>1, 17=>1, 18=>2, 19=>2, 20=>1,];
		} else {
			$Serpent=[1=>1, 2=>2, 3=>3, 4=>3, 5=>2, 6=>1, 7=>1, 8=>2, 9=>3, 10=>3, 11=>2, 12=>1, 13=>1, 14=>2, 15=>3, 16=>3, 17=>2, 18=>1, 19=>1, 20=>2, 21=>3, 22=>3, 23=>2, 24=>1, 25=>1, 26=>2, 27=>3, 28=>3, 29=>2, 30=>1,];
		}
        // deletes the semi and finals records
        safe_w_sql("delete from RunArcheryRank where RarTournament={$_SESSION['TourId']} and RarPhase>0 and RarTeam=$Team and RarEvent=".StrSafe_DB($Event));
		// now creates the missing records...
		$MaxInt=4294967295;
		$ValR=[];
		$ValL=[];
		foreach($Serpent as $Pos => $Pool) {
			$ValR[]="({$_SESSION['TourId']}, $MaxInt, $Team, ".StrSafe_DB($Event).", 2, $Pool, $Pos)";
			for($j=1;$j<=$r->EvFinEnds;$j++) {
				$ValL[]="({$_SESSION['TourId']}, $MaxInt, $Team, ".StrSafe_DB($Event).", 2, $Pool, $Pos, $j)";
			}
			$MaxInt--;
		}
		safe_w_sql("insert ignore into RunArcheryRank (RarTournament, RarEntry, RarTeam, RarEvent, RarPhase, RarPool, RarFromRank) values ".implode(',', $ValR));
		safe_w_sql("insert ignore into RunArchery (RaTournament, RaEntry, RaTeam, RaEvent, RaPhase, RaPool, RaFromRank, RaLap) values ".implode(',', $ValL));
	}

	if($FINS) {
		// each element is the rankfrom+(100*poolfrom) => pool
		switch($SemiPools) {
			case 2:
				$Serpent=[
					101 => 1,
					102 => 1,
					103 => 1,
					201 => 1,
					202 => 1,
					203 => 1,
					];
				break;
			case 3:
				$Serpent=[
					101 => 1,
					102 => 1,
					201 => 1,
					202 => 1,
					301 => 1,
					302 => 1,
				];
				break;
			default:
				$Serpent=[];
		}
		$num=1;
		$pool=1;
		while(count($Serpent)<$FINS) {
			$Serpent[$num]=$pool;
			$num++;
			if(count($Serpent)==10) {
				$pool++;
			}
		}

        // deletes the old settings
		safe_w_sql("delete from RunArcheryRank where RarTournament={$_SESSION['TourId']} and RarPhase=1 and RarTeam=$Team and RarEvent=".StrSafe_DB($Event));
		// now creates the missing records...
		$MaxInt=4294967295;
		$ValR=[];
		$ValL=[];
		foreach($Serpent as $Pos => $Pool) {
			$TruePos=$Pos%100;
			$Type=intval($Pos/100);
			$ValR[]="({$_SESSION['TourId']}, $MaxInt, $Team, ".StrSafe_DB($Event).", 1, $Pool, $TruePos, $Type)";
			for($j=1;$j<=$r->EvFinEnds;$j++) {
				$ValL[]="({$_SESSION['TourId']}, $MaxInt, $Team, ".StrSafe_DB($Event).", 1, $Pool, $TruePos, $Type, $j)";
			}
			$MaxInt--;
		}
		safe_w_sql("insert ignore into RunArcheryRank (RarTournament, RarEntry, RarTeam, RarEvent, RarPhase, RarPool, RarFromRank, RarFromType) values ".implode(',', $ValR));
		safe_w_sql("insert ignore into RunArchery (RaTournament, RaEntry, RaTeam, RaEvent, RaPhase, RaPool, RaFromRank, RaFromType, RaLap) values ".implode(',', $ValL));
	}
}