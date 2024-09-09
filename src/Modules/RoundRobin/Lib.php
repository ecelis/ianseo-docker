<?php

require_once('Common/Lib/Obj_RankFactory.php');

function createRound($Team, $Event, $Level, $Arch=1, $Waves=0) {
	// gets the informations
	$q=safe_r_sql("select RrLevGroups, RrLevGroupArchers, EvFinalFirstPhase from RoundRobinLevel
		inner join Events on EvTournament=RrLevTournament and EvCode=RrLevEvent and EvTeamEvent=RrLevTeam
		where RrLevTeam=$Team and RrLevLevel=$Level and RrLevTournament={$_SESSION['TourId']} and RrLevEvent=".StrSafe_DB($Event));
	$EVENT=safe_fetch($q);
	if(!$EVENT) {
		return;
	}

	// removes all level related items
	safe_w_sql("delete from RoundRobinGroup where RrGrTeam=$Team and RrGrLevel=$Level and RrGrTournament={$_SESSION['TourId']} and RrGrEvent=".StrSafe_DB($Event)." and RrGrGroup>{$EVENT->RrLevGroups}");
	safe_w_sql("delete from RoundRobinGrids where RrGridTeam=$Team and RrGridLevel=$Level and RrGridTournament={$_SESSION['TourId']} and RrGridEvent=".StrSafe_DB($Event));
	safe_w_sql("delete from RoundRobinMatches where RrMatchTeam=$Team and RrMatchLevel=$Level and RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent=".StrSafe_DB($Event));
	safe_w_sql("delete from RoundRobinParticipants where RrPartTeam=$Team and RrPartLevel=$Level and RrPartTournament={$_SESSION['TourId']} and RrPartEvent=".StrSafe_DB($Event));

	// number of people MUST be even, rounds formula is easy:
	// Round 1:
	// +-----+-----+-----+---
	// |  1  |  2  |  3  | ...
	// +-----+-----+-----+---
	// |  n  | n-1 | n-2 | ...
	// +-----+-----+-----+---
	//
	// Round 2
	// +-----+-----+-----+---
	// |  1  |  n  |  2  | ...
	// +-----+-----+-----+---
	// | n-1 | n-2 | n-3 | ...
	// +-----+-----+-----+---
	//
	// etc until
	// +-----+-----+-----+---
	// |  1  |  3  |  4  | ...
	// +-----+-----+-----+---
	// |  2  |  n | n-1 | ...
	// +-----+-----+-----+---

	$Matches=(int) ceil($EVENT->RrLevGroupArchers/2);
	$EvenNumber=(int) $Matches*2;
	$Rounds=$EvenNumber-1;

	/*****************
	 * redo the groups, including for each group grid, matches and participants
	 ***************** */
	$GridItems=[];
	foreach(range(1,$EVENT->RrLevGroups) as $Group) {
		safe_w_sql("insert ignore into RoundRobinGroup set
			RrGrTournament={$_SESSION['TourId']},
			RrGrLevel=$Level,
			RrGrGroup=$Group,
			RrGrEvent=".StrSafe_DB($Event).",
			RrGrTeam=$Team,
			RrGrName=".StrSafe_DB(get_text('GroupNum', 'RoundRobin', $Group)).",
			RrGrSession=0,
			RrGrTargetArchers={$Arch},
			RrGrArcherWaves={$Waves}
			");

		// DO THE GRID
		$Circle=range(2, $EvenNumber); // this will do the magic in assembling opponents!
		for($Round=1;$Round<=$Rounds;$Round++) {
			$Matchno=0;
			$RealCircle=array_merge([1],$Circle); // position 0 is always item 1!
			for($Match=0; $Match<$Matches; $Match++) {
				// Matchno EVEN
				$GridItems[]="({$_SESSION['TourId']}, ".StrSafe_DB($Event).", $Team, $Level, $Group, $Round, ".$RealCircle[$Match].", $Matchno)";
				$Matchno++;
				// Matchno ODD (opponent)
				$GridItems[]="({$_SESSION['TourId']}, ".StrSafe_DB($Event).", $Team, $Level, $Group, $Round, ".$RealCircle[$EvenNumber-$Match-1].", $Matchno)";
				$Matchno++;
			}

			// do the magic!
			$lastItem=array_pop($Circle);
			array_unshift($Circle, $lastItem);
		}
	}

	safe_w_sql("insert into RoundRobinGrids (RrGridTournament, RrGridEvent, RrGridTeam, RrGridLevel, RrGridGroup, RrGridRound, RrGridItem, RrGridMatchno) values ".implode(',', $GridItems));

	// redo the matches
	safe_w_sql("insert into RoundRobinMatches (RrMatchTournament, RrMatchLevel, RrMatchGroup, RrMatchEvent, RrMatchTeam, RrMatchRound, RrMatchMatchNo)  
		select RrGridTournament, RrGridLevel, RrGridGroup, RrGridEvent, RrGridTeam, RrGridRound, RrGridMatchno 
		from RoundRobinGrids 
		where RrGridTournament={$_SESSION['TourId']} and RrGridLevel=$Level and RrGridEvent=".StrSafe_DB($Event)." and RrGridTeam=$Team");

	// redo the participants
	safe_w_sql("insert into RoundRobinParticipants (RrPartTournament, RrPartLevel, RrPartGroup, RrPartEvent, RrPartTeam, RrPartDestItem)  
		select distinct RrGridTournament, RrGridLevel, RrGridGroup, RrGridEvent, RrGridTeam, RrGridItem 
		from RoundRobinGrids 
		where RrGridTournament={$_SESSION['TourId']} and RrGridLevel=$Level and RrGridEvent=".StrSafe_DB($Event)." and RrGridTeam=$Team");
}

/**
 * @param $Team
 * @param $Event
 * @param $Level
 * @return void
 */
function calculateFinalRank($Team, $Event, $Level, $TourId=0) {
	$options=[
		'tournament' => ($TourId ?: $_SESSION['TourId']),
		'team' => $Team,
		'event' => $Event,
		'level' => $Level,
	];
	Obj_RankFactory::create('Robin', $options)->calculate();
}

function CreateFinalLevel($Team, $Event, $NumQualified) {
	// starts removing the exceeding people ;)
	safe_w_sql("delete from RoundRobinParticipants 
		where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartLevel=0 and RrPartGroup=0 and RrPartDestItem>{$NumQualified}");
	// inserts all the items
	$sqlPart=array();
	for($n=1;$n<=$NumQualified;$n++) {
		$sqlPart[]="({$_SESSION['TourId']}, $Team, ".StrSafe_DB($Event).", 0, 0, $n)";
	}
	safe_w_SQL("insert ignore into RoundRobinParticipants (RrPartTournament, RrPartTeam, RrPartEvent, RrPartLevel, RrPartGroup, RrPartDestItem) 
							values ".implode(', ', $sqlPart));
	safe_w_sql("update RoundRobinParticipants set RrPartSourceLevel=0, RrPartSourceGroup=0, RrPartSourceRank=0, RrPartGroupRank=0, RrPartGroupRankBefSO=0, RrPartLevelRank=0, RrPartLevelRankBefSO=0, RrPartPoints=0, RrPartTieBreaker=0, RrPartTieBreaker2=0, RrPartParticipant=0, RrPartSubTeam=0 
							where (RrPartTournament, RrPartTeam, RrPartEvent, RrPartLevel, RrPartGroup) = ({$_SESSION['TourId']}, $Team, ".StrSafe_DB($Event).", 0, 0)");
}

function SetRoundRobinTarget($Event, $Team, $Level, $Group, $Round, $Matchno, $ValueOrg, $Type='AB', $Letter='') {
	$ret=array();

	$Value=$ValueOrg;
	// cerco la fase del matchno
	$Select = "SELECT EvElim1 as MaxLevels, RrGrTargetArchers as Ath4Tgt, RrGrArcherWaves as Match4Tgt, (ceil(RrLevGroupArchers/2)*2)-1 as MaxMatchno, RrLevGroupArchers-1 as TotRounds, RrLevGroupArchers
		FROM RoundRobinGroup
		inner join RoundRobinLevel on RrLevTournament=RrGrTournament and RrLevTeam=RrGrTeam and RrLevEvent=RrGrEvent and RrLevLevel=RrGrLevel
		inner join Events on EvCode=RrGrEvent and EvTeamEvent=RrGrTeam and EvTournament=RrGrTournament
		WHERE RrGrTournament={$_SESSION['TourId']} and RrGrTeam=$Team and RrGrEvent=".StrSafe_DB($Event)." and RrGrLevel=$Level and RrGrGroup=$Group";
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)!=1) {
		return;
	}

	$RR=safe_fetch($Rs);
	$ABCD='';

	switch(substr($Value,-1)) {
		case '*':
		case '+':
			// target is followed by a "+" sign fills up the phase from this point up to the last with increments of 1
			// target is followed by a "*" sign fills up the phase from this point up to the last with increments of 1, leaving a gap of 1 target after each match
			// target is followed by a "++" sign fills up the phase from this point up to the last with increments of 1, repeating for all levels
			// target is followed by a "**" sign fills up the phase from this point up to the last with increments of 1, leaving a gap of 2 targets after each match
			$Gap=substr_count($Value,'*');
            if(substr_count($Value,'+')>1) {
                $LevelsToDo=range($Level, $RR->MaxLevels);
            } else {
                $LevelsToDo=[$Level];
            }
			$Value=intval($Value);
            foreach($LevelsToDo as $Level) {
                $val=$Value;
                foreach(range($Matchno, $RR->MaxMatchno) as $k => $n) {
                    switch($RR->Ath4Tgt.'-'.$RR->Match4Tgt) {
                        case '0-0':
                            // one archer per butt, single wave, no letter
                            // always change from the current group to the last one
                            foreach(range($Round, $RR->TotRounds) as $r) {
                                $ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r, $n, $val);
                            }
                            $val++;
                            if($k%2==1) {
                                // every 2 matchnos
                                $val+=$Gap;
                            }
                            break;
                        case '1-0':
                            // two archers per butt, single wave, always A+B
                            $ABCD=($ABCD=='A' ? 'B' : 'A');
                            foreach(range($Round, $RR->TotRounds) as $r) {
                                $ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r, $n, $val, $ABCD);
                            }
                            if($ABCD=='B') {
                                $val++;
                                if($k%4==3) {
                                    // every 4 matchnos, so 2 buts
                                    $val+=$Gap;
                                }
                            }
                            break;
                        case '0-1':
                            // one archer per butt, double wave, based on $Type it can be always A, always C or A+A followed by C+C
                            switch($Type) {
                                case 'AB':
                                    foreach(range($Round, $RR->TotRounds) as $r) {
                                        $ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r, $n, $val, 'A');
                                    }
                                    $val++;
                                    if($k%2==1) {
                                        // every 2 matchnos
                                        $val+=$Gap;
                                    }
                                    break;
                                case 'CD':
                                    foreach(range($Round, $RR->TotRounds) as $r) {
                                        $ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r, $n, $val, 'C');
                                    }
                                    $val++;
                                    if($k%2==1) {
                                        // every 2 matchnos
                                        $val+=$Gap;
                                    }
                                    break;
                                case 'ABCD':
                                    // means 1A vs 2A and 1C vs 2C
                                    $ABCD=(($ABCD=='A' and $n%2) ? 'C' : 'A');
                                    foreach(range($Round, $RR->TotRounds) as $r) {
                                        $ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r, $n, $val, $ABCD);
                                    }
                                    $val++;
                                    if($n%2 and $ABCD=='A') {
                                        // 2nd matchno of the couple, if 'AB' needs to go back 2 targets
                                        $val-=2;
                                    }
                                    if($k%4==3) {
                                        // every 4 matchnos
                                        $val+=$Gap;
                                    }
                                    break;
                            }
                            break;
                        case '1-1':
                            // two archers per butt, double wave, based on $Type it can be always A+B, always C+B or A+B followed by C+D
                            switch($Type) {
                                case 'AB': // always AB
                                    $ABCD=($ABCD=='A' ? 'B' : 'A');
                                    foreach(range($Round, $RR->TotRounds) as $r) {
                                        $ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r, $n, $val, $ABCD);
                                    }
                                    if($ABCD=='B') {
                                        $val++;
                                    }
                                    if($k%4==3) {
                                        // every 4 matchnos (2 butts) jumps
                                        $val+=$Gap;
                                    }
                                    break;
                                case 'CD': // always CD
                                    $ABCD=($ABCD=='C' ? 'D' : 'C');
                                    foreach(range($Round, $RR->TotRounds) as $r) {
                                        $ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r, $n, $val, $ABCD);
                                    }
                                    if($ABCD=='D') {
                                        $val++;
                                    }
                                    if($k%4==3) {
                                        // every 4 matchnos (2 butts) jumps
                                        $val+=$Gap;
                                    }
                                    break;
                                case 'ABCD':
                                    $ABCD=($ABCD=='A' ? 'B' : ($ABCD=='B' ? 'C' : ($ABCD=='C' ? 'D' : 'A')));
                                    foreach(range($Round, $RR->TotRounds) as $r) {
                                        $ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r, $n, $val, $ABCD);
                                    }
                                    if($ABCD=='D') {
                                        // after 4 matchnos, moves 1 target
                                        $val++;
                                    }
                                    if($k%8==7) {
                                        // every 4 matchnos (2 butts) jumps
                                        $val+=$Gap;
                                    }
                                    break;
                            }
                    }
                }
            }
			break;
		case '-':
			// removes the byes... byes can only be the "true" byes from setup... this happens only if the participants are an odd number
			// if this happens, the competitor against the last matchno has always a bye so skips
			$MaxArchers=ceil($RR->RrLevGroupArchers/2)*2;
			$HasBye=($MaxArchers!=$RR->RrLevGroupArchers);
			$Value=intval($Value);
			$val=$Value;

			$Matchno=2*floor($Matchno/2);
			$q=safe_r_sql("select l.RrGridRound, l.RrGridMatchno as Matchno1, r.RrGridMatchno as Matchno2, l.RrGridItem=$MaxArchers or r.RrGridItem=$MaxArchers as IsBye 
				from RoundRobinGrids l
				inner join RoundRobinGrids r on r.RrGridTournament=l.RrGridTournament and r.RrGridTeam=l.RrGridTeam and r.RrGridEvent=l.RrGridEvent and r.RrGridLevel=l.RrGridLevel and r.RrGridGroup=l.RrGridGroup and r.RrGridRound=l.RrGridRound and r.RrGridMatchno=l.RrGridMatchno+1
				where l.RrGridTournament={$_SESSION['TourId']} and l.RrGridTeam=$Team and l.RrGridEvent=".StrSafe_DB($Event)." and l.RrGridLevel=$Level and l.RrGridGroup=$Group and l.RrGridRound>=$Round and l.RrGridRound>=$Matchno and (l.RrGridMatchno % 2)=0
				order by  l.RrGridRound, Matchno1");

			$OldRound='';
			while($r=safe_fetch($q)) {
				if($OldRound!=$r->RrGridRound) {
					$val=$Value;
				}
				$OldRound=$r->RrGridRound;
				if($HasBye and $r->IsBye) {
					$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno1, '');
					$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno2, '');
				} else {
					switch($RR->Ath4Tgt.'-'.$RR->Match4Tgt) {
						case '0-0':
							// one archer per butt, single wave, no letter
							$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno1, $val++);
							$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno2, $val++);
							break;
						case '1-0':
							// two archers per butt, single wave, always A+B
							$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno1, $val, 'A');
							$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno2, $val, 'B');
							$val++;
							break;
						case '0-1':
							// one archer per butt, double wave, based on $Type it can be always A, always C or A+A followed by C+C
							switch($Type) {
								case 'AB':
									$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno1, $val++, 'A');
									$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno2, $val++, 'A');
									break;
								case 'CD':
									$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno1, $val++, 'C');
									$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno2, $val++, 'C');
									break;
								case 'ABCD':
									// means 1A vs 2A and 1C vs 2C
									$ABCD=($ABCD=='A' ? 'C' : 'A');
									$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno1, $val, $ABCD);
									$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno2, $val+1, $ABCD);
									if($ABCD=='C') {
										// if 'CD' needs to go forwards 2 targets
										$val+=2;
									}
									break;
							}
							break;
						case '1-1':
							// two archers per butt, double wave, based on $Type it can be always A+B, always C+D or A+B followed by C+D
							switch($Type) {
								case 'AB': // always AB
									$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno1, $val, 'A');
									$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno2, $val, 'B');
									$val++;
									break;
								case 'CD': // always CD
									$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno1, $val, 'C');
									$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno2, $val, 'D');
									$val++;
									break;
								case 'ABCD':
									$ABCD=($ABCD=='A' ? 'C' : 'A');
									$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno1, $val, $ABCD++);
									$ret[]=SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $r->RrGridRound, $r->Matchno2, $val, $ABCD);
									if($ABCD=='D') {
										// after 4 matchnos, moves 1 target
										$val++;
									}
									break;
							}
					}
				}
			}
			break;
		default:
			// single value... need to check if single wave or not, 1 or 2 archers per butt
			$Value=intval($Value);
			switch($RR->Ath4Tgt.'-'.$RR->Match4Tgt) {
				case '0-0':
					// straight!
					$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno, $Value);
					break;
				case '1-0':
					// A/B always
					$ValueOrg=strtoupper($ValueOrg);
					if(substr($ValueOrg, -1)=='A' or substr($ValueOrg, -1)=='B') {
						$TargetLetter=substr($ValueOrg, -1);
					} else {
						$TargetLetter=(($Matchno%2) ? 'B': 'A');
					}
					$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno, $Value, $TargetLetter);
					break;
				case '0-1':
					// 1 per butt, 2 waves, depends on type
					$Matchno=intval($Matchno/2)*2;
					switch ($Type) {
						case 'AB':
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno, $Value, 'A');
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno + 1, $Value + 1, 'A');
							break;
						case 'CD':
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno, $Value, 'C');
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno + 1, $Value + 1, 'C');
							break;
						case 'ABCD':
							// first 2 are AB
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno, $Value, 'A');
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno + 1, $Value + 1, 'A');
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno + 2, $Value, 'C');
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno + 3, $Value + 1, 'C');
							break;
					}
					break;
				case '1-1':
					// 2 per butt, 2 waves, depends on type
					$Matchno=intval($Matchno/2)*2;
					switch ($Type) {
						case 'AB':
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno, $Value, 'A');
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno + 1, $Value, 'B');
							break;
						case 'CD':
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno, $Value, 'C');
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno + 1, $Value, 'D');
							break;
						case 'ABCD':
							// first 2 are AB
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno, $Value, 'A');
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno + 1, $Value, 'B');
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno + 2, $Value, 'C');
							$ret[] = SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno + 3, $Value, 'D');
							break;
					}
					break;
			}
			break;
	}
	return $ret;
}

function SetRoundRobinTargetAssign($Event, $Team, $Level, $Group, $Round, $Matchno, $Value, $Letter='') {
	$Insert = "update RoundRobinMatches set RrMatchTarget='%2\$s'
		where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=" . StrSafe_DB($Event) . " and RrMatchLevel=$Level
		and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo='%1\$s'";
	if ($Value) {
		$Target = str_pad($Value,TargetNoPadding,'0',STR_PAD_LEFT).$Letter;
	} else {
		$Target='';
	}

	safe_w_sql(sprintf($Insert, $Matchno, $Target));

	return array('l'=>$Level, 'g'=>$Group, 'r'=>$Round, 't' => $Target, 'm'=>$Matchno);
}

function getRrMatchDateTimes($Team) {
	$ret=[];
	$tmp=[];
	$q=safe_r_SQL("select RrMatchScheduledDate, left(RrMatchScheduledTime,5) as MatchTime, RrMatchScheduledLength, RrMatchEvent, RrMatchLevel, RrMatchGroup, RrMatchRound
			from RoundRobinMatches
			inner join RoundRobinLevel on RrLevTournament=RrMatchTournament and RrLevTeam=RrMatchTeam and RrLevEvent=RrMatchEvent and RrLevLevel=RrMatchLevel
            inner join RoundRobinGroup on RrGrTournament=RrMatchTournament and RrGrTeam=RrMatchTeam and RrGrEvent=RrMatchEvent and RrGrLevel=RrMatchLevel and RrGrGroup=RrMatchGroup
			where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchScheduledDate>0
			group by RrMatchScheduledDate, RrMatchScheduledTime, RrMatchScheduledLength, RrMatchEvent, RrMatchLevel, RrMatchRound, RrMatchGroup
			order by RrMatchScheduledDate, RrMatchScheduledTime, RrMatchScheduledLength, RrMatchEvent, RrMatchLevel, RrMatchRound, RrMatchGroup");
	while($r=safe_fetch($q)) {
		$tmp[$r->RrMatchScheduledDate.'|'.$r->MatchTime.'|'.$r->RrMatchScheduledLength]["{$r->RrMatchScheduledDate}@{$r->MatchTime}/{$r->RrMatchScheduledLength}"][$r->RrMatchEvent][$r->RrMatchLevel][$r->RrMatchRound][]=$r->RrMatchGroup;
	}

	foreach($tmp as $k => $keys) {
		foreach($keys as $key => $events) {
			$tmp=$key . ' => ';
			foreach($events as $event => $levels) {
				$tmp.=$event.'(';
				foreach($levels as $level => $rounds) {
					$tmp.=get_text('LevelNumShort', 'RoundRobin', $level).':';
					foreach($rounds as $round => $groups) {
						$tmp.=' '.get_text('RoundNumShort', 'RoundRobin', $round).'-'.get_text('GroupNumShort', 'RoundRobin', implode('+',$groups));
					}
				}
				$tmp.='); ';
			}
		}
		$ret[]=[
			'val'=>$k,
			'txt'=>$tmp,
		];
	}

	return $ret;
}

function setWinner($m1, $EV, $ScoreField, $Totals=true) {
	$m2=$m1+1;
	$SQL1="RrMatchTieBreaker=RrMatchTieBreaker";
	$SQL2="RrMatchTieBreaker=RrMatchTieBreaker";
	$RetSQL="select 
			m1.RrMatchSetPoints as EndPoints1, m1.RrMatchTie as Tie1, m1.RrMatchTbClosest as Closest1, m1.RrMatchTiebreak as TieArrows1, m1.RrMatchScore as Score1, m1.$ScoreField as Total1, m1.RrMatchTbDecoded as TbDecoded1,
			m2.RrMatchSetPoints as EndPoints2, m2.RrMatchTie as Tie2, m2.RrMatchTbClosest as Closest2, m2.RrMatchTiebreak as TieArrows2, m2.RrMatchScore as Score2, m2.$ScoreField as Total2, m2.RrMatchTbDecoded as TbDecoded2
		from RoundRobinMatches m1
		inner join RoundRobinMatches m2 on m2.RrMatchTournament=m1.RrMatchTournament and m2.RrMatchTeam=m1.RrMatchTeam and m2.RrMatchEvent=m1.RrMatchEvent and m2.RrMatchLevel=m1.RrMatchLevel and m2.RrMatchGroup=m1.RrMatchGroup and m2.RrMatchRound=m1.RrMatchRound and m2.RrMatchMatchNo=m1.RrMatchMatchNo+1				
		where m1.RrMatchTournament={$_SESSION['TourId']} and m1.RrMatchTeam={$EV->Team} and m1.RrMatchEvent=".StrSafe_DB($EV->Event)." and m1.RrMatchLevel={$EV->Level} and m1.RrMatchGroup={$EV->Group} and m1.RrMatchRound={$EV->Round} and m1.RrMatchMatchNo=$m1
		";
	$q=safe_r_sql($RetSQL);
	$r=safe_fetch($q);
	if($r->Tie1==2) {
		// There is a bye, the whole row is empty except the winner!
		$SQL1="RrMatchWinLose=1, RrMatchTiebreak='', RrMatchTiebreaker='', RrMatchTiebreaker2='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition='', RrMatchSetScore=0, RrMatchSetPointsByEnd='', RrMatchRoundPoints={$EV->WinPoints}";
		$SQL2="RrMatchWinLose=0, RrMatchTiebreak='', RrMatchTiebreaker='', RrMatchTiebreaker2='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition='', RrMatchSetScore=0, RrMatchSetPointsByEnd='', RrMatchRoundPoints=0, RrMatchTie=0";
	} elseif($r->Tie2==2) {
		// There is a bye, the whole row is empty except the winner!
		$SQL1="RrMatchWinLose=0, RrMatchTiebreak='', RrMatchTiebreaker='', RrMatchTiebreaker2='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition='', RrMatchSetScore=0, RrMatchSetPointsByEnd='', RrMatchRoundPoints=0, RrMatchTie=0";
		$SQL2="RrMatchWinLose=1, RrMatchTiebreak='', RrMatchTiebreaker='', RrMatchTiebreaker2='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition='', RrMatchSetScore=0, RrMatchSetPointsByEnd='', RrMatchRoundPoints={$EV->WinPoints}";
	} else {
		if($Totals) {
			$set1=$r->Total1;
			$set2=$r->Total2;
			switch($EV->TieSystem) {
				case '2':
					$SQL1="RrMatchTieBreaker=$set1";
					$SQL2="RrMatchTieBreaker=$set2";
					break;
				case '3':
					$SQL1="RrMatchTieBreaker=$r->Score1";
					$SQL2="RrMatchTieBreaker=$r->Score2";
					break;
				case '5':
					if($EV->MatchMode) {
						$SQL1="RrMatchTieBreaker=".($set1-$set2);
						$SQL2="RrMatchTieBreaker=".($set2-$set1);
					} else {
						$SQL1="RrMatchTieBreaker=".($r->Score1-$r->Score2);
						$SQL2="RrMatchTieBreaker=".($r->Score2-$r->Score1);
					}
					break;
			}
			switch($EV->TieSystem2) {
				case '0':
					$SQL1.=", RrMatchTieBreaker2=0";
					$SQL2.=", RrMatchTieBreaker2=0";
					break;
				case '2':
					$SQL1.=", RrMatchTieBreaker2=$set1";
					$SQL2.=", RrMatchTieBreaker2=$set2";
					break;
				case '3':
					$SQL1.=", RrMatchTieBreaker2=$r->Score1";
					$SQL2.=", RrMatchTieBreaker2=$r->Score2";
					break;
				case '5':
					if($EV->MatchMode) {
						$SQL1.=", RrMatchTieBreaker2=".($set1-$set2);
						$SQL2.=", RrMatchTieBreaker2=".($set2-$set1);
					} else {
						$SQL1.=", RrMatchTieBreaker2=".($r->Score1-$r->Score2);
						$SQL2.=", RrMatchTieBreaker2=".($r->Score2-$r->Score1);
					}
					break;
			}

		} else {
			if($r->EndPoints1) {
				$ends1=explode('|', $r->EndPoints1);
			} else {
				$ends1=[];
			}
			if($r->EndPoints2) {
				$ends2=explode('|', $r->EndPoints2);
			} else {
				$ends2=[];
			}
			$set1=0;
			$set2=0;
			$numWins1=0;
			$numWins2=0;
			$setByEnds1=[];
			$setByEnds2=[];
			for($n=0; $n<min(count($ends1),count($ends2)); $n++) {
				if($ends1[$n]<$ends2[$n]) {
					$set2+=2;
					$numWins2++;
					$setByEnds1[]=0;
					$setByEnds2[]=2;
				} elseif($ends1[$n]>$ends2[$n]) {
					$set1+=2;
					$numWins1++;
					$setByEnds1[]=2;
					$setByEnds2[]=0;
				} else {
					$set1++;
					$set2++;
					$setByEnds1[]=1;
					$setByEnds2[]=1;
				}
			}
			$SQL1="RrMatchSetPointsByEnd=".StrSafe_DB(implode('|',$setByEnds1)).", RrMatchWinnerSet=$numWins1";
			$SQL2="RrMatchSetPointsByEnd=".StrSafe_DB(implode('|',$setByEnds2)).", RrMatchWinnerSet=$numWins2";
		}
		$r->TbDecoded1=rtrim($r->TbDecoded1,' ,+');
		$r->TbDecoded2=rtrim($r->TbDecoded2,' ,+');
		if($EV->MatchMode) {
			// calculates the single sets
			$SQL1.=", RrMatchSetScore=$set1";
			$SQL2.=", RrMatchSetScore=$set2";
			if($set1>$EV->NumEnds) {
				// m1 is winner
				$SQL1.=", RrMatchWinLose=1, RrMatchRoundPoints=".$EV->WinPoints;
				$SQL2.=", RrMatchWinLose=0, RrMatchRoundPoints=0";
				if($set2==$EV->NumEnds) {
					// wins by tie...
					$SQL1.=", RrMatchTie=1";
					$SQL2.=", RrMatchTie=0";
				} else {
					$SQL1.=", RrMatchTie=0, RrMatchTiebreak='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition=''";
					$SQL2.=", RrMatchTie=0, RrMatchTiebreak='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition=''";
				}
			} elseif($set2>$EV->NumEnds) {
				// m2 is winner
				$SQL2.=", RrMatchWinLose=1, RrMatchRoundPoints=".$EV->WinPoints;
				$SQL1.=", RrMatchWinLose=0, RrMatchRoundPoints=0";
				if($set1==$EV->NumEnds) {
					// wins by tie...
					$SQL2.=", RrMatchTie=1";
					$SQL1.=", RrMatchTie=0";
				} else {
					$SQL2.=", RrMatchTie=0, RrMatchTiebreak='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition=''";
					$SQL1.=", RrMatchTie=0, RrMatchTiebreak='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition=''";
				}
			} elseif($set1==$set2 and $set1==$EV->NumEnds and $EV->TieAllowed) {
				// both tie and ties allowed
				$SQL1.=", RrMatchWinLose=0, RrMatchRoundPoints=".$EV->TiePoints.", RrMatchTie=0, RrMatchTiebreak='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition=''";
				$SQL2.=", RrMatchWinLose=0, RrMatchRoundPoints=".$EV->TiePoints.", RrMatchTie=0, RrMatchTiebreak='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition=''";
			} elseif($r->TieArrows1 and strlen($r->TieArrows1)==strlen($r->TieArrows2) and strlen($r->TieArrows1)%$EV->NumSO==0) {
				// no winners... and no points, check if ties...
				$a1=str_split($r->TieArrows1, $EV->NumSO);
				$a2=str_split($r->TieArrows2, $EV->NumSO);
				$arrows1=ValutaArrowString(end($a1));
				$arrows2=ValutaArrowString(end($a2));
				if($arrows1 and ($arrows1>$arrows2 or $r->Closest1)) {
					// winner is 1 closest or higher value
					if($r->Closest1) {
						$r->TbDecoded1.='+';
					}
					$set1++;
					$SQL1.=", RrMatchWinLose=1, RrMatchTbDecoded='$r->TbDecoded1', RrMatchSetScore=".($EV->NumEnds+1).", RrMatchRoundPoints=".$EV->WinPoints.", RrMatchTie=1, RrMatchTbClosest={$r->Closest1}";
					$SQL2.=", RrMatchWinLose=0, RrMatchTbDecoded='$r->TbDecoded2', RrMatchSetScore=".($EV->NumEnds).", RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTbClosest=0";
				} elseif($arrows2 and ($arrows2>$arrows1 or $r->Closest2)) {
					// winner is 2 closest or higher value
					if($r->Closest2) {
						$r->TbDecoded2.='+';
					}
					$set2++;
					$SQL1.=", RrMatchWinLose=0, RrMatchTbDecoded='$r->TbDecoded1', RrMatchSetScore=".($EV->NumEnds).", RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTbClosest=0";
					$SQL2.=", RrMatchWinLose=1, RrMatchTbDecoded='$r->TbDecoded2', RrMatchSetScore=".($EV->NumEnds+1).", RrMatchRoundPoints=".$EV->WinPoints.", RrMatchTie=1, RrMatchTbClosest={$r->Closest2}";
				} else {
					// no winners yet
					$SQL1.=", RrMatchWinLose=0, RrMatchTbDecoded='$r->TbDecoded1', RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTbClosest=0";
					$SQL2.=", RrMatchWinLose=0, RrMatchTbDecoded='$r->TbDecoded2', RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTbClosest=0";
				}
			} else {
				// no winners yet
				$r->TbDecoded1=rtrim($r->TbDecoded1,' ,+');
				$r->TbDecoded2=rtrim($r->TbDecoded2,' ,+');
				$SQL1.=", RrMatchWinLose=0, RrMatchTbDecoded='$r->TbDecoded1', RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTbClosest=0";
				$SQL2.=", RrMatchWinLose=0, RrMatchTbDecoded='$r->TbDecoded2', RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTbClosest=0";
			}
		} else {
			// cumulative score! We have a winner only if we have the correct amount of ends shot
			if($Totals or (count($ends1)==count($ends2) and count($ends1)==$EV->NumEnds)) {
				if($r->Score1>$r->Score2) {
					// m1 is winner
					$SQL1.=", RrMatchWinLose=1, RrMatchRoundPoints=".$EV->WinPoints.", RrMatchTie=0, RrMatchTiebreak='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition=''";
					$SQL2.=", RrMatchWinLose=0, RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTiebreak='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition=''";
				} elseif($r->Score2>$r->Score1) {
					// m2 is winner
					$SQL1.=", RrMatchWinLose=0, RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTiebreak='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition=''";
					$SQL2.=", RrMatchWinLose=1, RrMatchRoundPoints=".$EV->WinPoints.", RrMatchTie=0, RrMatchTiebreak='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition=''";
				} elseif($r->Score1 and $r->Score1==$r->Score2 and $EV->TieAllowed) {
					// both tie and ties allowed
					$SQL1.=", RrMatchWinLose=0, RrMatchRoundPoints=".$EV->TiePoints.", RrMatchTie=0, RrMatchTiebreak='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition=''";
					$SQL2.=", RrMatchWinLose=0, RrMatchRoundPoints=".$EV->TiePoints.", RrMatchTie=0, RrMatchTiebreak='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchTiePosition=''";
				} elseif($r->TieArrows1 and strlen($r->TieArrows1)==strlen($r->TieArrows2) and strlen($r->TieArrows1)%$EV->NumSO==0) {
					// no winners... and no points, check if ties...
					$a1=str_split($r->TieArrows1, $EV->NumSO);
					$a2=str_split($r->TieArrows2, $EV->NumSO);
					$arrows1=ValutaArrowString(end($a1));
					$arrows2=ValutaArrowString(end($a2));
					if($arrows1 and ($arrows1>$arrows2 or $r->Closest1)) {
						// winner is 1 closest or higher value
						if($r->Closest1) {
							$r->TbDecoded1.='+';
						}
						$SQL1.=", RrMatchWinLose=1, RrMatchTbDecoded='$r->TbDecoded1', RrMatchRoundPoints=".$EV->WinPoints.", RrMatchTie=1, RrMatchTbClosest={$r->Closest1}";
						$SQL2.=", RrMatchWinLose=0, RrMatchTbDecoded='$r->TbDecoded2', RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTbClosest=0";
					} elseif($arrows2 and ($arrows2>$arrows1 or $r->Closest2)) {
						// winner is 2 closest or higher value
						if($r->Closest2) {
							$r->TbDecoded2.='+';
						}
						$SQL1.=", RrMatchWinLose=0, RrMatchTbDecoded='$r->TbDecoded1', RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTbClosest=0";
						$SQL2.=", RrMatchWinLose=1, RrMatchTbDecoded='$r->TbDecoded2', RrMatchRoundPoints=".$EV->WinPoints.", RrMatchTie=1, RrMatchTbClosest={$r->Closest2}";
					} else {
						// no winners yet
						$SQL1.=", RrMatchWinLose=0, RrMatchTbDecoded='$r->TbDecoded1', RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTbClosest=0";
						$SQL2.=", RrMatchWinLose=0, RrMatchTbDecoded='$r->TbDecoded2', RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTbClosest=0";
					}
				} else {
					// no winners yet
					$SQL1.=", RrMatchWinLose=0, RrMatchTbDecoded='$r->TbDecoded1', RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTbClosest=0";
					$SQL2.=", RrMatchWinLose=0, RrMatchTbDecoded='$r->TbDecoded2', RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTbClosest=0";
				}
			} else {
				// no winners yet
				$SQL1.=", RrMatchWinLose=0, RrMatchTbDecoded='$r->TbDecoded1', RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTbClosest=0";
				$SQL2.=", RrMatchWinLose=0, RrMatchTbDecoded='$r->TbDecoded2', RrMatchRoundPoints=0, RrMatchTie=0, RrMatchTbClosest=0";
			}
		}

		// calculates and sets the tiebreakers
		if(!$Totals) {
			/* Values are:
			* <ul>
			* <li>1: Number of sets won</li>
			* <li>2: Total set points</li>
			* <li>3: Total points</li>
			* <li>4: Shoot off</li>
			* <li>5: Sum (set)points - Sum opponents (set)points</li>
			* </ul>*/
			switch($EV->TieSystem) {
				case '1':
					if(isset($numWins1)) {
						$SQL1.=", RrMatchTieBreaker=$numWins1";
						$SQL2.=", RrMatchTieBreaker=$numWins2";
					}
					break;
				case '2':
					$SQL1.=", RrMatchTieBreaker=$set1";
					$SQL2.=", RrMatchTieBreaker=$set2";
					break;
				case '3':
					$SQL1.=", RrMatchTieBreaker=$r->Score1";
					$SQL2.=", RrMatchTieBreaker=$r->Score2";
					break;
				case '5':
					if($EV->MatchMode) {
						$SQL1.=", RrMatchTieBreaker=".($set1-$set2);
						$SQL2.=", RrMatchTieBreaker=".($set2-$set1);
					} else {
						$SQL1.=", RrMatchTieBreaker=".($r->Score1-$r->Score2);
						$SQL2.=", RrMatchTieBreaker=".($r->Score2-$r->Score1);
					}
					break;
			}
			switch($EV->TieSystem2) {
				case '0':
					$SQL1.=", RrMatchTieBreaker2=0";
					$SQL2.=", RrMatchTieBreaker2=0";
					break;
				case '1':
					$SQL1.=", RrMatchTieBreaker2=$numWins1";
					$SQL2.=", RrMatchTieBreaker2=$numWins2";
					break;
				case '2':
					if(isset($set1)) {
						$SQL1.=", RrMatchTieBreaker2=$set1";
						$SQL2.=", RrMatchTieBreaker2=$set2";
					}
					break;
				case '3':
					$SQL1.=", RrMatchTieBreaker2=$r->Score1";
					$SQL2.=", RrMatchTieBreaker2=$r->Score2";
					break;
				case '5':
					if($EV->MatchMode) {
						$SQL1.=", RrMatchTieBreaker2=".($set1-$set2);
						$SQL2.=", RrMatchTieBreaker2=".($set2-$set1);
					} else {
						$SQL1.=", RrMatchTieBreaker2=".($r->Score1-$r->Score2);
						$SQL2.=", RrMatchTieBreaker2=".($r->Score2-$r->Score1);
					}
					break;
			}
		}
	}
	if($SQL1) {
		safe_w_sql("update RoundRobinMatches set $SQL1 where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$EV->Team and RrMatchEvent=".StrSafe_DB($EV->Event)." and RrMatchLevel=$EV->Level and RrMatchGroup=$EV->Group and RrMatchRound=$EV->Round and RrMatchMatchNo=$m1");
		$Up1=safe_w_affected_rows();
		safe_w_sql("update RoundRobinMatches set $SQL2 where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$EV->Team and RrMatchEvent=".StrSafe_DB($EV->Event)." and RrMatchLevel=$EV->Level and RrMatchGroup=$EV->Group and RrMatchRound=$EV->Round and RrMatchMatchNo=$m2");
		$Up2=safe_w_affected_rows();

		// recalculates the rank
		if($Up1 or $Up2) {
			safe_w_sql("update RoundRobinMatches set RrMatchDateTime=now() where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$EV->Team and RrMatchEvent=".StrSafe_DB($EV->Event)." and RrMatchLevel=$EV->Level and RrMatchGroup=$EV->Group and RrMatchRound=$EV->Round and RrMatchMatchNo in ($m1,$m2)");
			calculateGroupRank($EV);
		}
	}
	return $RetSQL;
}


/**
 * Calculates the group rank of a single group. Ties in group are managed by the Event->TieSystem
 * Values are:
 * <ul>
 * <li>1: Number of sets won</li>
 * <li>2: Total set points</li>
 * <li>3: Total points</li>
 * <li>4: Shoot off</li>
 * <li>5: Sum (set)points - Sum opponents (set)points</li>
 * </ul>
 *
 * @param $Event Object (see case updateScore in InsertPoint-action.php)
 * @return void
 *
 */
function calculateGroupRank($Event, $TourId=0) {
	$options=[
		'tournament' => ($TourId ?: $_SESSION['TourId']),
		'team' => $Event->Team,
		'event' => $Event->Event,
		'level' => $Event->Level,
		'group' => $Event->Group,
	];

	Obj_RankFactory::create('Robin', $options)->calculateGroup();
}

function calculateLevelRank($Team, $Event, $Level, $TourId=0) {
	$options=[
		'tournament' => ($TourId ?: $_SESSION['TourId']),
		'team' => $Team,
		'event' => $Event,
		'level' => $Level,
	];

	Obj_RankFactory::create('Robin', $options)->calculateLevel();
}

function RobinResetSO($Team, $Event, $Level, $Group) {
	safe_w_SQL("update RoundRobinGroup 
    	inner join RoundRobinLevel on RrLevTournament=RrGrTournament and RrLevTeam=RrGrTeam and RrLevEvent=RrGrEvent and RrLevLevel=RrGrLevel 
		set RrGrSoSolved=0, RrLevSoSolved=0, RrGrDateTime=now(), RrLevDateTime=now()
		where RrGrTournament={$_SESSION['TourId']} and RrGrTeam=$Team and RrGrEvent=".StrSafe_DB($Event)." and RrGrLevel=$Level".($Group ? " and RrGrGroup=$Group" : ""));
	// reset all SO for next levels
	$q=safe_r_sql("select distinct RrPartLevel, RrPartGroup
		from RoundRobinParticipants
		where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartSourceLevel=$Level".($Group ? " and RrPartSourceGroup=$Group" : ""));
	while($r=safe_fetch($q)) {
		if($r->RrPartLevel==0)  {
			// brackets so resets the event SO
			require_once('Common/Fun_Sessions.inc.php');
			ResetShootoff($Event, $Team,3);
			// also removes people from the participants list!!!!
			safe_w_sql("update RoundRobinParticipants set RrPartParticipant=0, RrPartSubTeam=0 where RrPartLevel=0 and RrPartTeam=$Team and RrPartEvent='$Event' and RrPartTournament={$_SESSION['TourId']}");
		} else {
			RobinResetSO($Team, $Event, $r->RrPartLevel, $r->RrPartGroup);
		}
	}
	// reset level positions
	safe_w_sql("update RoundRobinParticipants set RrPartLevelRankBefSO=0,RrPartLevelRank=0 where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartLevel=$Level");
	if($Team) {
		// removes final positions
		safe_w_sql("update Teams 
    		inner join RoundRobinParticipants on RrPartParticipant=TeCoId and RrPartSubTeam=TeSubTeam and  RrPartTournament=TeTournament and RrPartEvent=TeEvent and TeFinEvent=1 
			set TeTimestampFinal=now(), TeRankFinal=0
			where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartLevel=$Level");
	} else {
		// reset final positions.
		safe_w_sql("update Individuals 
    		inner join RoundRobinParticipants on RrPartParticipant=IndId and RrPartTournament=IndTournament and RrPartEvent=IndEvent 
			set IndTimestampFinal=now(), IndRankFinal=0
			where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartLevel=$Level");
	}
}

function RobinUpdateArrowString($MatchNo, $Event, $Team, $Level, $Group, $Round, $ArrowString, $ArrowStart, $ArrowEnd, $CompId=0, $Closest=-1) {
	global $CFG;
	if(empty($CompId) && !empty($_SESSION['TourId'])) {
		$CompId = $_SESSION['TourId'];
	}

	$MainFilter="RrMatchTournament=$CompId and RrMatchEvent='$Event' and RrMatchTeam=$Team and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round";

	$Select ="select RrMatchEvent as EvCode, RrMatchMatchNo as MatchNo, RrMatchArrowstring as ArString, RrMatchTiebreak as TbString, RrMatchConfirmed as Confirmed,
			RrLevMatchMode, EvMatchArrowsNo, RrMatchLive as LiveMatch, RrMatchTbClosest as Closest,
       		RrLevArrows as arrows, RrLevSO as so, RrLevEnds as ends, RrLevBestRankMode
		from RoundRobinMatches
		inner join RoundRobinLevel on RrLevTournament=RrMatchTournament and RrLevEvent=RrMatchEvent and RrLevTeam=RrMatchTeam and RrLevLevel=RrMatchLevel
		inner join Events on EvTournament=RrMatchTournament and EvTeamEvent=RrMatchTeam and EvCode=RrMatchEvent
		where $MainFilter and RrMatchMatchNo=$MatchNo";

	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)==1) {
		$MyRow=safe_fetch($Rs);

		$maxArrows=$MyRow->ends*$MyRow->arrows;
		$maxSoArrows=$MyRow->so;

		$ArrowStart--;
		$Len=$ArrowEnd-$ArrowStart;
		$Offset=($ArrowStart<$maxArrows ? 0 : $maxArrows);

		$SubArrowString=substr($ArrowString,0,$Len);
		$tmpArrowString=str_pad(($Offset==0 ? $MyRow->ArString : $MyRow->TbString),($Offset==0 ? $maxArrows : $maxSoArrows)," ",STR_PAD_RIGHT);
		$tmpArrowString=substr_replace($tmpArrowString,$SubArrowString,$ArrowStart-$Offset,$Len);

		if($Offset==0) {
			$tmpArrowString = substr($tmpArrowString, 0, $maxArrows);
		} elseif($Closest>0) {
			// must first remove the closest and tie from the other match
			$OppMatchno=($MatchNo%2 ? $MatchNo-1 : $MatchNo+1);
			safe_w_sql("update RoundRobinMatches 
				SET RrMatchTbClosest=0, RrMatchTie=0, RrMatchWinLose=0, RrMatchTbDecoded=replace(RrMatchTbDecoded, '+','') 
                where $MainFilter and RrMatchTie!=2 AND RrMatchMatchNo=$OppMatchno");
		}

		$TbDecoded='';
		if($Offset) {
			// check the decoded arrows of the tiebreak!
			$decoded=array();
			foreach(str_split(rtrim($tmpArrowString), $MyRow->so) as $k) {
				if($MyRow->so==1) {
					$decoded[]=DecodeFromLetter($k);
				} else {
					$decoded[]=ValutaArrowString($k);
				}
			}
			$TbDecoded=", RrMatchTbDecoded=".StrSafe_DB(implode(',', $decoded).($Closest>0 ? '+' : ''));
		}

		if($Offset) {
			$query="UPDATE RoundRobinMatches
				SET
				RrMatchTiebreak='$tmpArrowString',
				".($Closest!=-1 ? "RrMatchTbClosest=".intval($Closest)."," : '')."
				RrMatchDateTime=RrMatchDateTime
				$TbDecoded
				WHERE $MainFilter and RrMatchTie!=2 AND RrMatchMatchNo=$MatchNo";
		} else {

			$query="UPDATE RoundRobinMatches
				SET RrMatchArrowstring='$tmpArrowString',
				RrMatchDateTime=RrMatchDateTime
				$TbDecoded
				WHERE $MainFilter and RrMatchTie!=2 AND RrMatchMatchNo=$MatchNo";
		}
		safe_w_sql($query);

		if(safe_w_affected_rows()) {
			$m=array($MyRow->MatchNo, $MyRow->MatchNo%2 ? $MyRow->MatchNo-1 : $MyRow->MatchNo+1);
			$query="UPDATE RoundRobinMatches
				SET 
				RrMatchDateTime='".date('Y-m-d H:i:s')."',
				RrMatchStatus=2
				WHERE $MainFilter and RrMatchMatchNo=$MatchNo";
			safe_w_sql($query);

			// The Winner status must be reset and the match switches back to not confirmed
			// If the BestRankSystem is based on TieSystem, reset also the tie status
			$TieStatus=($MyRow->RrLevBestRankMode==1 ? '0' : "if(RrMatchTie=1,0,RrMatchTie)");
			safe_w_sql("update RoundRobinMatches set RrMatchConfirmed=0, RrMatchWinLose=0, RrMatchTie=$TieStatus 
				WHERE $MainFilter and RrMatchMatchNo in ($m[0], $m[1])");
			if($MyRow->Confirmed) {
				// the match was confirmed so status to 3 of the other match
				safe_w_sql("update RoundRobinMatches set RrMatchStatus=(RrMatchStatus | 2) 
					WHERE $MainFilter and RrMatchMatchNo = $m[1]");
			}
		}
		//print $query;
		return RobinMatchTotal($MatchNo, $Event, $Team, $Level, $Group, $Round, $CompId);
	}
}

function RobinMatchTotal($MatchNo, $Event, $Team, $Level, $Group, $Round, $CompId=0) {
	if(empty($CompId) && !empty($_SESSION['TourId'])) {
		$CompId = $_SESSION['TourId'];
	}

	if(is_null($MatchNo) || is_null($Event)) {
		return;
	}

	$M1=(int) intval($MatchNo/2)*2;
	$M2=$M1+1;
	$MatchFinished=false;
	$MainFilter="RrMatchTournament=$CompId and RrMatchEvent='$Event' and RrMatchTeam=$Team and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round";

	$Select = "SELECT 
       		EvCode, RrLevMatchMode, EvMatchArrowsNo, RrLevCheckGolds, RrLevCheckXNines, EvGoldsChars, EvXNineChars,
	      	MatchNo, Score, SetScore, Tie, ArString, TbString, TbClosest, Athlete,
       		OppMatchNo, OppScore, OppSetScore, OppTie, OppArString, OppTbString, OppTbClosest, OppAthlete,
			greatest(M1DateTime, M2DateTime) AS DateTime,
       		RrLevArrows as arrows, RrLevSO as so, RrLevEnds as ends, RrLevEnds+1 as winAt, 
            RrLevTieAllowed, RrLevWinPoints, RrLevTiePoints, RrLevTieBreakSystem, RrLevTieBreakSystem2
		FROM (
		    select RrMatchEvent, RrMatchTeam, RrMatchTournament,
		           RrMatchDateTime as M1DateTime, RrMatchAthlete as Athlete, RrMatchMatchNo as MatchNo, RrMatchScore as Score, RrMatchSetScore as SetScore, RrMatchTie as Tie, coalesce(RrMatchArrowstring,'') as ArString, coalesce(RrMatchTiebreak) as TbString, coalesce(RrMatchTbClosest,0) as TbClosest
			from RoundRobinMatches 
		    where $MainFilter and RrMatchMatchNo=$M1
		    ) AS M1
		inner join (
		    select RrMatchDateTime as M2DateTime, RrMatchAthlete as OppAthlete, RrMatchMatchNo as OppMatchNo, RrMatchScore as OppScore, RrMatchSetScore as OppSetScore, RrMatchTie as OppTie, coalesce(RrMatchArrowstring,'') as OppArString, coalesce(RrMatchTiebreak) as OppTbString, coalesce(RrMatchTbClosest,0) as OppTbClosest
			from RoundRobinMatches 
		    where $MainFilter and RrMatchMatchNo=$M2
		    ) AS M2 on OppMatchNo=MatchNo+1
		INNER JOIN Events ON EvCode=RrMatchEvent AND EvTournament=RrMatchTournament AND EvTeamEvent=RrMatchTeam
		INNER JOIN RoundRobinLevel ON RrLevTournament=RrMatchTournament AND RrLevTeam=RrMatchTeam and RrLevEvent=RrMatchEvent and RrLevLevel=$Level
		 ";

	//print $Select . "<br>";exit;
	$MatchUpdated=false; // serve per aggiornare il timestamp
	$AthRoundPoints=0;
	$OppRoundPoints=0;
	$AthTieBreaker=0;
	$AthTieBreaker2=0;
	$OppTieBreaker=0;
	$OppTieBreaker2=0;
	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)==1) {
		$MyRow=safe_fetch($Rs);
		$TotArrows=$MyRow->ends*$MyRow->arrows;
		$Winner=-1;
        $WinnerByTie=0;

		// set winner... of Ties
		if($MyRow->Tie) {
			$Winner=$MyRow->MatchNo;
			$MatchFinished=true;
			$AthRoundPoints=$MyRow->RrLevWinPoints;
            $WinnerByTie=1;
		} elseif ($MyRow->OppTie) {
			$Winner=$MyRow->OppMatchNo;
			$MatchFinished=true;
			$OppRoundPoints=$MyRow->RrLevWinPoints;
            $WinnerByTie=1;
		}

		list($Score, $Golds, $XNines)=ValutaArrowStringGX(substr($MyRow->ArString, 0, $TotArrows), $MyRow->EvGoldsChars, $MyRow->EvXNineChars);
		list($OppScore,$OppGolds, $OppXNines)=ValutaArrowStringGX(substr($MyRow->OppArString, 0, $TotArrows), $MyRow->EvGoldsChars, $MyRow->EvXNineChars);
		$SetAth=0;
		$SetOpp=0;
		$SetPointsAth=array();
		$SetPointsOpp=array();

		if($MyRow->RrLevMatchMode==0) {
			for($i=0; $i<$TotArrows; $i=$i+$MyRow->arrows) {
				//Cicla per tutte le volee dell'incontro
				$SetPointsAth[] = ValutaArrowString(substr($MyRow->ArString, $i, $MyRow->arrows));
				$SetPointsOpp[] = ValutaArrowString(substr($MyRow->OppArString, $i, $MyRow->arrows));
			}
			//Sistema Cumulativo
			$a1=strlen(str_replace(' ', '', $MyRow->ArString));
			$a2=strlen(str_replace(' ', '', $MyRow->OppArString));
			$t1=strlen(str_replace(' ', '', $MyRow->TbString));
			$t2=strlen(str_replace(' ', '', $MyRow->OppTbString));
			if(($a1==$TotArrows or !$MyRow->Athlete)
				and ($a2==$TotArrows or !$MyRow->OppAthlete)
				and ($t1==$t2)
			) {
				// all arrows have been shot from both sides...
				// if match is over establish the winner
				// only if not already decided by the tie
				// and if there are no doubts
				// and no SO are going on
				if($Winner==-1) {
					// No winner decided yet...
					$Proceed=(ctype_upper($MyRow->ArString.$MyRow->OppArString));
					//Da Remmare dopo ANKARA
					$Proceed=true;
					if(!$Proceed) {
						// check if the stars would make any change
						$Regexp='';
						$RaisedScore=$Score+RaiseStars(substr($MyRow->ArString, 0, $TotArrows), $Regexp, $MyRow->EvCode, $Team, $CompId);
						$RaisedOppScore=$OppScore+RaiseStars(substr($MyRow->OppArString, 0, $TotArrows), $Regexp, $MyRow->EvCode, $Team, $CompId);
						if($RaisedScore < $OppScore or $RaisedOppScore < $Score) {
							// Even with all stars "in" the ath will not make more than the opponent
							$Proceed=true;
						}
					}
					if($Proceed) {
						if($Score>$OppScore
                            or ($Score==$OppScore and $MyRow->RrLevCheckGolds and $Golds>$OppGolds)
                            or ($Score==$OppScore and $MyRow->RrLevCheckGolds and $MyRow->RrLevCheckXNines and $Golds==$OppGolds and $XNines>$OppXNines)
                            or ($Score==$OppScore and !$MyRow->RrLevCheckGolds and $MyRow->RrLevCheckXNines and $XNines>$OppXNines)
                            ) {
							$Winner=$MyRow->MatchNo;
							$MatchFinished=true;
							$AthRoundPoints=$MyRow->RrLevWinPoints;
						} elseif($Score<$OppScore
                            or ($Score==$OppScore and $MyRow->RrLevCheckGolds and $Golds<$OppGolds)
                            or ($Score==$OppScore and $MyRow->RrLevCheckGolds and $MyRow->RrLevCheckXNines and $Golds==$OppGolds and $XNines<$OppXNines)
                            or ($Score==$OppScore and !$MyRow->RrLevCheckGolds and $MyRow->RrLevCheckXNines and $XNines<$OppXNines)
                            ) {
							$Winner=$MyRow->OppMatchNo;
							$MatchFinished=true;
							$OppRoundPoints=$MyRow->RrLevWinPoints;
						} elseif($MyRow->RrLevTieAllowed) {
							// Ties are allowed, so match is over without a winner
							$AthRoundPoints=$MyRow->RrLevTiePoints;
							$OppRoundPoints=$MyRow->RrLevTiePoints;
							$MatchFinished=true;
                        } else {
							if( strlen(str_replace(' ', '', $MyRow->TbString))!=0
								and (strlen(str_replace(' ', '', $MyRow->TbString)) % $MyRow->so) == 0
								and strlen(str_replace(' ', '', $MyRow->TbString))==strlen(str_replace(' ', '', $MyRow->OppTbString))
							) {
								// Verifico le stringhe CASE INSENSITIVE - in questo momento me ne frego degli "*"
                                $XChar=($MyRow->RrLevCheckGolds ? $MyRow->EvGoldsChars : ($MyRow->RrLevCheckXNines ? $MyRow->EvXNineChars : null));
								list($AthTbValue, $AthWeight, $AthStars, $AthNumX, $AthArrows) = ValutaArrowStringSO($MyRow->TbString, $XChar, $XChar?'A':null);
								list($OppTbValue, $OppWeight, $OppStars, $OppNumX, $OppArrows) = ValutaArrowStringSO($MyRow->OppTbString, $XChar, $XChar?'A':null);

								$MatchFinished=true;
                                $WinnerByTie=1;

								if($MyRow->TbClosest) {
									// Athlete 1 has at a closest to center
									$Winner = $MyRow->MatchNo;
									$WinnerId = $MyRow->MatchNo;
									$AthRoundPoints=$MyRow->RrLevWinPoints;
								} elseif($MyRow->OppTbClosest) {
									// Athlete 2 has one arrow closer to center
									$Winner = $MyRow->OppMatchNo;
									$WinnerId = $MyRow->OppMatchNo;
									$OppRoundPoints=$MyRow->RrLevWinPoints;
								} elseif($AthTbValue > $OppTbValue) {
									//TbString è maggiore di OppTbString --> il primo vince
									$Winner = $MyRow->MatchNo;
									$WinnerId = $MyRow->MatchNo;
									$AthRoundPoints=$MyRow->RrLevWinPoints;
								} elseif($AthTbValue < $OppTbValue) {
									//OppTbString è maggiore di TbString --> il secondo vince
									$Winner = $MyRow->OppMatchNo;
									$WinnerId = $MyRow->OppMatchNo;
									$OppRoundPoints=$MyRow->RrLevWinPoints;
								} elseif($AthNumX > $OppNumX) {
									// Athlete 1 has more Xs than Athlete 2
									$Winner = $MyRow->MatchNo;
									$WinnerId = $MyRow->MatchNo;
									$AthRoundPoints=$MyRow->RrLevWinPoints;
								} elseif($AthNumX < $OppNumX) {
									// Athlete 2 has more Xs than Athlete 1
									$Winner = $MyRow->OppMatchNo;
									$WinnerId = $MyRow->OppMatchNo;
									$OppRoundPoints=$MyRow->RrLevWinPoints;
								} else {
									$MatchFinished=false;
									if($AthArrows and $OppArrows) {
										if($AthArrows[0] > $OppArrows[0]) {
											$Winner = $MyRow->MatchNo;
											$WinnerId = $MyRow->MatchNo;
											$MatchFinished=true;
											$AthRoundPoints=$MyRow->RrLevWinPoints;
										} elseif($AthArrows[0] < $OppArrows[0]) {
											$Winner = $MyRow->OppMatchNo;
											$WinnerId = $MyRow->OppMatchNo;
											$MatchFinished=true;
											$OppRoundPoints=$MyRow->RrLevWinPoints;
										}
									}
								}
							}

						}
					}
				}
			} else {
				// match is not over, so if no byes reset the winner!
				if($MyRow->Tie!=2 and $MyRow->OppTie!=2) {
					$Winner=-1;
				}
			}

			$query1="UPDATE RoundRobinMatches
				SET RrMatchSetScore=0,
				    RrMatchSetPointsByEnd='',
				    RrMatchWinnerSet=0,
					RrMatchTie=" . (($Score==$OppScore and $WinnerByTie and $Winner==$MyRow->MatchNo) ? '1' : '0');

			$query2="UPDATE RoundRobinMatches
				SET RrMatchSetScore=0,
				    RrMatchSetPointsByEnd='',
				    RrMatchWinnerSet=0,
					RrMatchTie=" . (($Score==$OppScore and $WinnerByTie and $Winner==$MyRow->OppMatchNo) ? '1' : '0');
		} else {
			//Sistema a Set
			$AthSpBe=array();
			$OppSpBe=array();
			$SetAthWin=0;
			$SetOppWin=0;
			$WinnerId=-1;
			for($i=0; $i<$TotArrows; $i=$i+$MyRow->arrows) {
				//Cicla per tutte le volee dell'incontro
				$AthEndString=rtrim(substr($MyRow->ArString, $i, $MyRow->arrows));
				$OppEndString=rtrim(substr($MyRow->OppArString, $i, $MyRow->arrows));
				$MatchString=$AthEndString.$OppEndString;
				$AthSetPoints=ValutaArrowString($AthEndString);
				$OppSetPoints=ValutaArrowString($OppEndString);
				$SetPointsAth[] = $AthSetPoints;
				$SetPointsOpp[] = $OppSetPoints;


				if(strpos($MatchString, ' ')===false and strlen($AthEndString) and strlen($AthEndString)==strlen($OppEndString) and strlen($AthEndString)==$MyRow->arrows) {
					// All arrows of the end have been shot
					$Proceed=ctype_upper($MatchString); // check if there are stars
					// TODO: Da Remmare dopo ANKARA
					$Proceed=true;
					if(!$Proceed) {
						// check if stars can change result
						$RegExp='';
						$AthSetPointsUpper=$AthSetPoints+RaiseStars($AthEndString, $RegExp, $MyRow->EvCode, $Team, $CompId);
						$OppSetPointsUpper=$OppSetPoints+RaiseStars($OppEndString, $RegExp, $MyRow->EvCode, $Team, $CompId);
						if($AthSetPointsUpper < $OppSetPoints or $OppSetPointsUpper < $AthSetPoints) {
							// even with all stars as higher points will the score beat the opponent's score
							$Proceed=true;
						}
					}
					if($Proceed) {
						if($AthSetPoints>$OppSetPoints) {
							$SetAth += 2;
							$SetAthWin++;
							$AthSpBe[]=2;
							$OppSpBe[]=0;
						} elseif($AthSetPoints<$OppSetPoints) {
							$SetOpp += 2;
							$SetOppWin++;
							$AthSpBe[]=0;
							$OppSpBe[]=2;
						} else {
							$SetAth++;
							$SetOpp++;
							$AthSpBe[]=1;
							$OppSpBe[]=1;
						}
					}
				} else if(strlen($AthEndString)!= 0 OR strlen($OppEndString) != 0) {
					$AthSpBe[]=0;
					$OppSpBe[]=0;
				}
			}

			if($SetAth > $MyRow->ends+2 or $SetOpp > $MyRow->ends+2) {
				$SetAth=0;
				$SetOpp=0;
			}

			if($SetAth==$SetOpp
				and strlen(str_replace(' ', '', $MyRow->TbString))!=0
				and (strlen(str_replace(' ', '', $MyRow->TbString))%$MyRow->so)==0
				and strlen(trim($MyRow->TbString))==strlen(trim($MyRow->OppTbString))
			) {
				// Verifico le stringhe CASE INSENSITIVE - in questo momento me ne frego degli "*"
                $XChar=($MyRow->RrLevCheckGolds ? $MyRow->EvGoldsChars : ($MyRow->RrLevCheckXNines ? $MyRow->EvXNineChars : null));
                list($AthTbValue, $AthWeight, $AthStars, $AthNumX, $AthArrows) = ValutaArrowStringSO($MyRow->TbString, $XChar, $XChar?'A':null);
                list($OppTbValue, $OppWeight, $OppStars, $OppNumX, $OppArrows) = ValutaArrowStringSO($MyRow->OppTbString, $XChar, $XChar?'A':null);

				if($MyRow->TbClosest) {
					// Athlete 1 has at least one arrow set as closest to center
					$Winner = $MyRow->MatchNo;
					$WinnerId = $MyRow->MatchNo;
					$SetAth++;
				} elseif($MyRow->OppTbClosest) {
					// Athlete 2 has one arrow closer to center
					$Winner = $MyRow->OppMatchNo;
					$WinnerId = $MyRow->OppMatchNo;
					$SetOpp++;
				} elseif($AthTbValue > $OppTbValue) {
					//TbString è maggiore di OppTbString --> il primo vince
					$Winner = $MyRow->MatchNo;
					$WinnerId = $MyRow->MatchNo;
					$SetAth++;
				} elseif($AthTbValue < $OppTbValue) {
					//OppTbString è maggiore di TbString --> il secondo vince
					$Winner = $MyRow->OppMatchNo;
					$WinnerId = $MyRow->OppMatchNo;
					$SetOpp++;
				} elseif($AthNumX > $OppNumX) {
					// Athlete 1 has more Xs than Athlete 2
					$Winner = $MyRow->MatchNo;
					$WinnerId = $MyRow->MatchNo;
					$SetAth++;
				} elseif($AthNumX < $OppNumX) {
					// Athlete 2 has more Xs than Athlete 1
					$Winner = $MyRow->OppMatchNo;
					$WinnerId = $MyRow->OppMatchNo;
					$SetOpp++;
				} else {
					if($AthArrows and $OppArrows) {
						if($AthArrows[0] > $OppArrows[0]) {
							$Winner = $MyRow->MatchNo;
							$WinnerId = $MyRow->MatchNo;
							$SetAth++;
						} elseif($AthArrows[0] < $OppArrows[0]) {
							$Winner = $MyRow->OppMatchNo;
							$WinnerId = $MyRow->OppMatchNo;
							$SetOpp++;
						}
					}
				}
			} elseif($SetAth>=$MyRow->winAt) {
				$Winner = $MyRow->MatchNo;
			} elseif($SetOpp>=$MyRow->winAt) {
				$Winner = $MyRow->OppMatchNo;
			} elseif($MyRow->RrLevTieAllowed) {
				// Ties are allowed, so match is over without a winner
			}

			$query1="UPDATE RoundRobinMatches
				SET RrMatchSetScore=$SetAth,
					RrMatchSetPointsByEnd=" . StrSafe_DB(implode('|', $AthSpBe)) . ",
					RrMatchWinnerSet=$SetAthWin,
					RrMatchTie=" . ($WinnerId == $MyRow->MatchNo ? '1':'0') . "";

			$query2="UPDATE RoundRobinMatches
				SET RrMatchSetScore=$SetOpp,
					RrMatchSetPointsByEnd=" . StrSafe_DB(implode('|', $OppSpBe)) . ",
					RrMatchWinnerSet=$SetOppWin,
					RrMatchTie=" . ($WinnerId == $MyRow->OppMatchNo ? '1':'0') . "";

			if($SetAth >= $MyRow->winAt or $SetOpp >= $MyRow->winAt or ($MyRow->RrLevTieAllowed and $SetAth==$MyRow->ends and $SetOpp==$MyRow->ends)) {
				$MatchFinished=true;
				if($SetAth >= $MyRow->winAt) {
					$AthRoundPoints=$MyRow->RrLevWinPoints;
				} elseif($SetOpp >= $MyRow->winAt) {
					$OppRoundPoints=$MyRow->RrLevWinPoints;
				} else {
					$AthRoundPoints=$MyRow->RrLevTiePoints;
					$OppRoundPoints=$MyRow->RrLevTiePoints;
				}
			}
		}

		if($MatchFinished) {
			// Match is done, calculate the tiebreaker values...
			// * <li>1: Number of sets won</li>
			// * <li>2: Total set points</li>
			// * <li>3: Total points</li>
			// * <li>4: Shoot off</li>
			// * <li>5: Sum (set)points - Sum opponents (set)points</li>
			switch($MyRow->RrLevTieBreakSystem) {
				case '1':
					foreach($SetPointsAth as $k => $v) {
						if($v>$SetPointsOpp[$k]) {
							$AthTieBreaker++;
						} elseif($v<$SetPointsOpp[$k]) {
							$OppTieBreaker++;
						}
					}
					break;
				case '2':
					$AthTieBreaker=$SetAth;
					$OppTieBreaker=$SetOpp;
					break;
				case '3':
					$AthTieBreaker=$Score;
					$OppTieBreaker=$OppScore;
					break;
				case '5':
					$AthTieBreaker=($MyRow->RrLevMatchMode ? ($SetAth-$SetOpp) : ($Score-$OppScore));
					$OppTieBreaker=($MyRow->RrLevMatchMode ? ($SetOpp-$SetAth) : ($OppScore-$Score));
					break;
			}
			switch($MyRow->RrLevTieBreakSystem2) {
				case '1':
					foreach($SetPointsAth as $k => $v) {
						if($v>$SetPointsOpp[$k]) {
							$AthTieBreaker2++;
						} elseif($v<$SetPointsOpp[$k]) {
							$OppTieBreaker2++;
						}
					}
					break;
				case '2':
					$AthTieBreaker2=$SetAth;
					$OppTieBreaker2=$SetOpp;
					break;
				case '3':
					$AthTieBreaker2=$Score;
					$OppTieBreaker2=$OppScore;
					break;
				case '5':
					$AthTieBreaker2=($MyRow->RrLevMatchMode ? ($SetAth-$SetOpp) : ($Score-$OppScore));
					$OppTieBreaker2=($MyRow->RrLevMatchMode ? ($SetOpp-$SetAth) : ($OppScore-$Score));
					break;
			}
		}

		$query1.=", RrMatchRoundPoints=$AthRoundPoints, RrMatchTieBreaker=$AthTieBreaker, RrMatchTieBreaker2=$AthTieBreaker2, RrMatchDateTime=RrMatchDateTime, RrMatchWinLose=" . ($Winner==$MyRow->MatchNo ? '1' : '0') . ", RrMatchScore=$Score, RrMatchGolds=$Golds, RrMatchXNines=$XNines, RrMatchSetPoints=" . StrSafe_DB(implode('|', $SetPointsAth));
		$query2.=", RrMatchRoundPoints=$OppRoundPoints, RrMatchTieBreaker=$OppTieBreaker, RrMatchTieBreaker2=$OppTieBreaker2, RrMatchDateTime=RrMatchDateTime, RrMatchWinLose=" . ($Winner==$MyRow->OppMatchNo ? '1' : '0') . ", RrMatchScore=$OppScore, RrMatchGolds=$OppGolds, RrMatchXNines=$OppXNines, RrMatchSetPoints=" . StrSafe_DB(implode('|', $SetPointsOpp));

		safe_w_sql($query1." WHERE $MainFilter AND RrMatchMatchNo={$MyRow->MatchNo}");
		$MatchUpdated = ($MatchUpdated or safe_w_affected_rows());
		safe_w_sql($query2." WHERE $MainFilter AND RrMatchMatchNo={$MyRow->OppMatchNo}");
		$MatchUpdated = ($MatchUpdated or safe_w_affected_rows());


		if($MatchUpdated) {
			$query="UPDATE RoundRobinMatches
				SET RrMatchDateTime='".date('Y-m-d H:i:s')."'
				WHERE $MainFilter and RrMatchMatchNo in ($MyRow->MatchNo, $MyRow->OppMatchNo)";
			safe_w_sql($query);
			if($MatchFinished) {
				// calculate the group rank
				calculateGroupRank((object) ['Team'=>$Team,  'Event'=>$Event, 'Level'=>$Level, 'Group'=>$Group], $CompId);
			}
		}

		 if(!isset($_REQUEST['Changed']) or $_REQUEST['Changed']) {
		 	// this should avoid launching ODF events if nothing changed
		 	runJack("FinArrUpdate", $CompId, array("Event"=>$Event ,"Team"=>$Team,"MatchNo"=>$MatchNo+100*$Round+10000*$Group+1000000*$Level ,"TourId"=>$CompId));
		 }

	}
	return $MatchFinished;
}

function getRobinArrowsParams($Team, $Event, $Level, $TourId=0) {
	if(!$TourId) {
		$TourId=$_SESSION['TourId'];
	}
	$q=safe_r_sql("select RrLevArrows as arrows, RrLevSO as so, RrLevEnds as ends, RrLevEnds+1 as winAt, RrLevMatchMode 
		from RoundRobinLevel 
		where RrLevTournament=$TourId and RrLevTeam=$Team and RrLevLevel=$Level and RrLevEvent=".StrSafe_DB($Event));
	return safe_fetch($q);
}