<?php
require_once('Common/Lib/ArrTargets.inc.php');
/**
 * Obj_Rank_Robin
 * $opts array can have:
 * <ul>
 * <li>tournament</li>
 * <li>team</li>
 * <li>events</li>
 * <li>levels</li>
 * <li>groups</li>
 * <li>rounds</li>
 * </ul>
 */
class Obj_Rank_Robin_3_SetFRD12023 extends Obj_Rank{
	public $PFilter='';
	public $MFilter='';
	public $OnlyRank=false;
	public $OnlyMatch=false;
	/**
	 * @param $opts
	 * <ul>
	 * <li>tournament</li>
	 * <li>team</li>
	 * <li>events</li>
	 * <li>levels</li>
	 * <li>groups</li>
	 * <li>rounds</li>
	 * </ul>
	 */
	public function __construct($opts){
		// sets default team status to individual
		if(array_key_exists('events', $opts) and !is_array($opts['events'])) {
			$opts['events']=[$opts['events']];
		}
		if(array_key_exists('levels', $opts) and !is_array($opts['levels'])) {
			$opts['levels']=[$opts['levels']];
		}
		if(array_key_exists('groups', $opts) and !is_array($opts['groups'])) {
			$opts['groups']=[$opts['groups']];
		}
		if(array_key_exists('rounds', $opts) and !is_array($opts['rounds'])) {
			$opts['rounds']=[$opts['rounds']];
		}
		parent::__construct($opts);
	}

	/**
	 * safeFilter()
	 * Protegge con gli apici gli elementi di $this->opts['events']
	 *
	 * @return mixed: false se non c'è filtro oppure la stringa da inserire nella where delle query
	 */
	protected function safeFilter($type='P', $Partial='') {
		switch($type[0]) {
			case 'P':
				$ret=array(
					"RrPartTournament={$this->tournament}",
				);

				if (isset($this->opts['team'])) {
					$ret[] = "RrPartTeam=" . $this->opts['team'];
				}

				if (array_key_exists('events',$this->opts)) {
					$ret[]="RrPartEvent in (".implode(',', StrSafe_DB($this->opts['events'])).")";
				} elseif (array_key_exists('event',$this->opts)) {
					$ret[]="RrPartEvent = ".StrSafe_DB($this->opts['event']);
				}

				switch($Partial) {
					case 'S':
						if (array_key_exists('level',$this->opts)) {
							$ret[]="RrPartSourceLevel = {$this->opts['level']}";
						}
						if (array_key_exists('group',$this->opts)) {
							$ret[]="RrPartSourceGroup = {$this->opts['group']}";
						}
						break;
					case 'P':
						if (array_key_exists('levels',$this->opts)) {
							$ret[]="RrPartLevel in (".implode(',', StrSafe_DB($this->opts['levels'])).")";
						} elseif (array_key_exists('level',$this->opts)) {
							$ret[]="RrPartLevel = {$this->opts['level']}";
						}
						break;
					default:
						if (array_key_exists('levels',$this->opts)) {
							$ret[]="RrPartLevel in (".implode(',', StrSafe_DB($this->opts['levels'])).")";
						} elseif (array_key_exists('level',$this->opts)) {
							$ret[]="RrPartLevel = {$this->opts['level']}";
						}
						if (array_key_exists('groups',$this->opts)) {
							$ret[]="RrPartGroup in (".implode(',', StrSafe_DB($this->opts['groups'])).")";
						} elseif (array_key_exists('group',$this->opts)) {
							$ret[]="RrPartGroup = {$this->opts['group']}";
						}
				}
				break;
			case 'M':
				$ret=array(
					"RrMatchTournament={$this->tournament}",
				);

				if (array_key_exists('team',$this->opts)) {
					$ret[] = "RrMatchTeam=" . $this->opts['team'];
				}

				if (array_key_exists('events',$this->opts)) {
					$ret[]="RrMatchEvent in (".implode(',', StrSafe_DB($this->opts['events'])).")";
				} elseif (array_key_exists('event',$this->opts)) {
					$ret[]="RrMatchEvent = ". StrSafe_DB($this->opts['event']);
				}

				if (array_key_exists('levels',$this->opts)) {
					$ret[]="RrMatchLevel in (".implode(',', StrSafe_DB($this->opts['levels'])).")";
				} elseif (array_key_exists('level',$this->opts)) {
					if($Partial[0]=='C') {
						// need to get all the previous levels
						$ret[]="RrMatchLevel between 1 and {$this->opts['level']}";
					} else {
						$ret[]="RrMatchLevel = {$this->opts['level']}";
					}
				}
				if (array_key_exists('groups',$this->opts)) {
					$ret[]="RrMatchGroup in (".implode(',', StrSafe_DB($this->opts['groups'])).")";
				} elseif (array_key_exists('group',$this->opts)) {
					$ret[]="RrMatchGroup = {$this->opts['group']}";
				}
				if (array_key_exists('rounds',$this->opts)) {
					$ret[]="RrMatchRound in (".implode(',', StrSafe_DB($this->opts['rounds'])).")";
				}
				if (array_key_exists('date',$this->opts)) {
					$ret[]="RrMatchScheduledDate=".StrSafe_DB($this->opts['date']);
				}
				if (array_key_exists('time',$this->opts)) {
					$ret[]="RrMatchScheduledTime=".StrSafe_DB($this->opts['time']);
				}
				if (array_key_exists('confirmed',$this->opts)) {
					$ret[]="RrMatchConfirmed=".intval($this->opts['confirmed']);
				}
				if (array_key_exists('schedule',$this->opts)) {
					$ret[]="concat(RrMatchScheduledDate,' ', RrMatchScheduledTime) =".StrSafe_DB($this->opts['schedule']);
				}
				break;
		}

		if($ret) {
			return implode(' AND ', $ret);
		}

		return 'true';

	}

	/**
	* calculate().
	* La classifica abs viene calcolata quando si calcola quella di classe e l'evento
	* prevede la div/cl della persona coinvolta
	* e quando si fanno gli spareggi per passare alle eliminatorie o alle finali.
	* Nel primo caso questo è il metodo da chiamare perchè calcolerà l'IndRank o l'IndD[1-8]Rank lavorando su tutto l'evento
	* (utilizza setRow()) altrimenti occorre usare setRow() direttamente.
	*
	* @override
	*
	* (non-PHPdoc)
	* @see ianseo/Common/Rank/Obj_Rank#calculate()
	*/
	public function calculate(){
		return true;
	}
	public function calculateGroup(){
		return true;
	}
	public function calculateLevel(){
		return true;
	}

	/**
	 * read()
	 *
	 * @override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#read()
	 */
	public function read(){
		$PFilter=$this->safeFilter('Participiants');
		$TourDates=[];
		foreach(getModuleParameter('FFTA', 'D1TourDates', [], $this->tournament) as $D) {
			if(($D['date'] ?? '')) {
				$TourDates[$D['date']]=$D['comp'];
			}
		}

		if(!$this->OnlyMatch) {
			// first step: the rank by Group and level
			$SQL="select Participants.*,
		            EvCode, EvEventName, EvTeamEvent, EvProgr, EvShootOff, RrLevMatchMode, RrLevBestRankMode,
		            coalesce(Entry, TeCountry) as Athlete, coalesce(EnCountry, TeCode) as Country,
		            if(SelSourceGroup is null, '', if(SelSourceGroup=0, 'q', 'Q')) as Qualified,
					ifnull(concat(DV2.DvMajVersion, '.', DV2.DvMinVersion) ,concat(DV1.DvMajVersion, '.', DV1.DvMinVersion)) as DocVersion,
					date_format(ifnull(DV2.DvPrintDateTime, DV1.DvPrintDateTime), '%e %b %Y %H:%i UTC') as DocVersionDate,
					ifnull(DV2.DvNotes, DV1.DvNotes) as DocNotes,
	                RrLevGroups*RrLevGroupArchers as QualifiedNo, RrLevName, RrLevSoSolved, RrLevTieBreakSystem, RrLevTieBreakSystem2, RrLevSO, RrLevArrows, RrLevEnds, RrLevGroupArchers, RrLevTieAllowed
				from ((
				    select 0 as LevelRank, RrPartIrmType, IrmType, RrPartDateTime, RrGrName, RrPartTeam, RrPartParticipant, RrPartSubTeam, RrPartEvent, RrPartTournament, RrPartLevel, RrPartGroup, RrPartPoints, RrPartTieBreaker, RrPartTieBreaker2, RrPartGroupRank, RrPartGroupRankBefSO, RrPartGroupTieBreak, RrPartGroupTbDecoded, RrPartGroupTbClosest, RrPartGroupTiesForCT, RrPartGroupTiesForSO
					from RoundRobinParticipants
				    inner join IrmTypes on IrmId=RrPartIrmType
				    inner join RoundRobinGroup on RrGrTournament=RrPartTournament and RrGrTeam=RrPartTeam and RrGrEvent=RrPartEvent and RrGrLevel=RrPartLevel and RrGrGroup=RrPartGroup			   	    
					where $PFilter and RrPartParticipant>0
					) UNION (
					select 1 as LevelRank, RrPartIrmType, IrmType, RrPartDateTime, ".StrSafe_DB(get_text('BestRanked', 'RoundRobin'))." as RrGrName, RrPartTeam, RrPartParticipant, RrPartSubTeam, RrPartEvent, RrPartTournament, RrPartLevel, 0, RrPartPoints, RrPartTieBreaker, RrPartTieBreaker2, RrPartLevelRank, RrPartLevelRankBefSO, RrPartLevelTieBreak, RrPartLevelTbDecoded, RrPartLevelTbClosest, RrPartLevelTiesForCT, RrPartLevelTiesForSO
					from RoundRobinParticipants
				    inner join IrmTypes on IrmId=RrPartIrmType
					where $PFilter and RrPartLevelRank>0 and RrPartParticipant>0
					)) Participants
				inner join Events on EvTournament=RrPartTournament and EvCode=RrPartEvent and EvTeamEvent=RrPartTeam and EvElimType=5
			    inner join RoundRobinLevel on RrLevTournament=RrPartTournament and RrLevTeam=RrPartTeam and RrLevEvent=RrPartEvent and RrLevLevel=RrPartLevel
				left join (
				    select EnId, IndEvent, trim(concat_ws(' ', upper(EnFirstName), EnName)) as Entry, if(CoCode=CoName, CoCode, concat_ws('-', CoCode, CoName)) as EnCountry 
					from Entries
					inner join Individuals on IndTournament=EnTournament and IndId=EnId
					inner join Events on EvCode=IndEvent and EvTournament=EnTournament and EvTeamEvent=0 
				    left join Countries on CoId=
                        case EvTeamCreationMode 
                            when 0 then EnCountry
                            when 1 then EnCountry2
                            when 2 then EnCountry3
                            else EnCountry
                        end
                        AND CoTournament=EnTournament
				    ) en on EnId=RrPartParticipant and RrPartTeam=0 and IndEvent=EvCode
				left join (
				    select CoId, CoName as TeCountry, CoCode as TeCode, TeSubTeam, TeEvent
				    from Teams
			        inner join Countries on TeCoId=CoId and TeTournament=CoTournament
				    where CoTournament={$this->tournament} and TeFinEvent=1
				    ) te on CoId=RrPartParticipant and TeSubTeam=RrPartSubTeam and RrPartTeam=1 and TeEvent=EvCode
				left join (
				    select RrPartTeam as SelTeam, RrPartEvent as SelEvent, RrPartSourceLevel as SelSourceLevel, RrPartSourceGroup as SelSourceGroup, RrPartSourceRank as SelSourceRank, RrPartParticipant as SelParticipant, RrPartSubTeam as SelSubTeam
				    from RoundRobinParticipants
				    where RrPartTournament={$this->tournament}
					) Selected on SelTeam=RrPartTeam and SelEvent=RrPartEvent and SelSourceLevel=RrPartLevel and SelSourceGroup=RrPartGroup and SelSourceRank=RrPartGroupRank and SelParticipant=RrPartParticipant and SelSubTeam=RrPartSubTeam
				LEFT JOIN DocumentVersions DV1 on EvTournament=DV1.DvTournament AND DV1.DvFile = 'ROBIN' and DV1.DvEvent=''
				LEFT JOIN DocumentVersions DV2 on EvTournament=DV2.DvTournament AND DV2.DvFile = 'ROBIN' and DV2.DvEvent=EvCode
				where $PFilter
				order by EvTeamEvent, EvProgr, RrPartLevel, LevelRank, RrPartGroup, RrPartGroupRank, Athlete
			";

			$this->data['meta']['title']=get_text('ResultsRobin','Tournament');
			$this->data['meta']['lastUpdate']='0000-00-00 00:00:00';
			$this->data['meta']['bye']=get_text('Bye');
			$this->data['meta']['tie']=get_text('Tie', 'RoundRobin');
			$this->data['sections']=array();

			$q=safe_r_sql($SQL);
			while($myRow=safe_fetch($q)) {
				if (empty($this->data['sections'][$myRow->EvCode])) {
					// Start of a new section
					$fields=array(
						'id'  => 'Id',
						'athlete' => get_text('Athlete'),
						'countryName' => get_text('Country'),
						'rank' => get_text('PositionShort'),
						'score' => get_text('TotalShort','Tournament'),
						'points' => get_text('Points','Tournament'),
						'completeScore' => get_text('TotalShort','Tournament'),
						'tiebreak' => get_text('TieArrows'),
						'tiebreakClosest' => get_text('ClosestShort', 'Tournament'),
						'tiebreakDecoded' => get_text('TieArrows'),
						'ct' => get_text('CoinTossShort','Tournament'),
						'so' => get_text('ShotOffShort','Tournament')
					);

					$this->data['sections'][$myRow->EvCode]=array(
						'meta' => array(
							'event' => $myRow->EvCode,
							'descr' => $myRow->EvEventName,
							'finished' => ($myRow->EvShootOff ? 1: 0),
							'fields' => $fields,
							'version' => $myRow->DocVersion,
							'versionDate' => $myRow->DocVersionDate,
							'versionNotes' => $myRow->DocNotes,
							'lastUpdate' => '0000-00-00 00:00:00',
							'matchMode' => $myRow->RrLevMatchMode,
							'hasShootOff' => '',
						),
						'levels'=>array(),
					);
				}
				if(empty($this->data['sections'][$myRow->EvCode]['levels'][$myRow->RrPartLevel])) {
					$this->data['sections'][$myRow->EvCode]['levels'][$myRow->RrPartLevel]=[
						'name' => $myRow->RrLevName,
						'qualifiedNo' => $myRow->QualifiedNo,
						'finished' => ($myRow->RrLevSoSolved ? 1: 0),
						'lastUpdate' => '0000-00-00 00:00:00',
						'hasShootOff' => '',
						'tiebreaker' => $myRow->RrLevTieBreakSystem ? get_text('TiebreakSystem-'.$myRow->RrLevTieBreakSystem, 'RoundRobin') : '',
						'tiebreaker2' => $myRow->RrLevTieBreakSystem2 ? get_text('TiebreakSystem-'.$myRow->RrLevTieBreakSystem2, 'RoundRobin') : '',
						'tb-1' => $myRow->RrLevTieBreakSystem ? get_text('TieBreak-1-Short', 'RoundRobin') : '',
						'tb-2' => $myRow->RrLevTieBreakSystem2 ? get_text('TieBreak-2-Short', 'RoundRobin') : '',
						'tiesAllowed' => $myRow->RrLevTieAllowed,
						'bestRankMode' => $myRow->RrLevBestRankMode,
						'soNumArrows' => $myRow->RrLevSO,
						'arrows' => $myRow->RrLevArrows,
						'ends' => $myRow->RrLevEnds,
						'archersInGroup' => $myRow->RrLevGroupArchers,
						'numMatches' => (int) ceil($myRow->RrLevGroupArchers/2),
						'ranks' => [],
						'matches' => [],
					];
				}

				if(empty($this->data['sections'][$myRow->EvCode]['levels'][$myRow->RrPartLevel]['ranks']['g'.$myRow->RrPartGroup])) {
					$this->data['sections'][$myRow->EvCode]['levels'][$myRow->RrPartLevel]['ranks']['g'.$myRow->RrPartGroup]=[
						'name' => $myRow->RrGrName,
						'items' => [],
					];
				}

				$this->data['sections'][$myRow->EvCode]['levels'][$myRow->RrPartLevel]['ranks']['g'.$myRow->RrPartGroup]['items'][]=[
					'id'  => $myRow->RrPartParticipant,
					'subteam'  => $myRow->RrPartSubTeam,
					// 'bib' => $myRow->EnCode,
					// 'localbib' => $myRow->LocalId,
					// 'tvname' => $myRow->EnOdfShortname,
					// 'birthdate' => $myRow->BirthDate,
					// 'target' => $myRow->TargetNo,
					'athlete' => $myRow->Athlete,
					// 'familyname' => $myRow->FamilyName,
					// 'familynameUpper' => $myRow->FamilyNameUpper,
					// 'givenname' => $myRow->GivenName,
					// 'nameOrder' => $myRow->EnNameOrder,
					// 'gender' => $myRow->EnSex,
					// 'countryId' => $myRow->CoId,
					// 'countryCode' => $myRow->CoCode,
					// 'contAssoc' => $myRow->CoCaCode,
					// 'memberAssoc' => $myRow->CoMaCode,
					// 'countryIocCode' => $myRow->EnIocCode,
					'irm' => $myRow->RrPartIrmType,
					'irmText' => $myRow->IrmType,
					'countryName' => $myRow->Country,
					'rank' => $myRow->RrPartGroupRank,
					'rankBefSO'=>$myRow->RrPartGroupRankBefSO,
					'score' => $myRow->RrPartPoints,
					'tieBreaker' => $myRow->RrPartTieBreaker,
					'tieBreaker2' => $myRow->RrPartTieBreaker2,
					'qualified' => $myRow->Qualified,
					'so' => $myRow->RrPartGroupTiesForSO,
					'ct' => $myRow->RrPartGroupTiesForCT,
					'tieArrows' => $myRow->RrPartGroupTieBreak,
					'tieDecoded' => $myRow->RrPartGroupTbDecoded,
					'tieClosest' => $myRow->RrPartGroupTbClosest,
				];

				if ($myRow->RrPartDateTime>$this->data['meta']['lastUpdate']) {
					$this->data['meta']['lastUpdate']=$myRow->RrPartDateTime;
				}
				if ($myRow->RrPartDateTime>$this->data['sections'][$myRow->EvCode]['meta']['lastUpdate']) {
					$this->data['sections'][$myRow->EvCode]['meta']['lastUpdate']=$myRow->RrPartDateTime;
				}
				if ($myRow->RrPartDateTime>$this->data['sections'][$myRow->EvCode]['levels'][$myRow->RrPartLevel]['lastUpdate']) {
					$this->data['sections'][$myRow->EvCode]['levels'][$myRow->RrPartLevel]['lastUpdate']=$myRow->RrPartDateTime;
				}

			}
		}

		if(!$this->OnlyRank) {
			// second step: the brackets
			$SQL=$this->getQuery();

			$q=safe_r_sql($SQL);
			$Place='';
			while($myRow=safe_fetch($q)) {
				if (empty($this->data['sections'][$myRow->EvCode])) {
					// Start of a new section
					$fields=array(
						'id'  => 'Id',
						'athlete' => get_text('Athlete'),
						'countryName' => get_text('Country'),
						'rank' => get_text('PositionShort'),
						'score' => get_text('TotalShort','Tournament'),
						'points' => get_text('Points','Tournament'),
						'completeScore' => get_text('TotalShort','Tournament'),
						'tiebreak' => get_text('TieArrows'),
						'tiebreakClosest' => get_text('ClosestShort', 'Tournament'),
						'tiebreakDecoded' => get_text('TieArrows'),
						'ct' => get_text('CoinTossShort','Tournament'),
						'so' => get_text('ShotOffShort','Tournament')
					);

					$this->data['sections'][$myRow->EvCode]=array(
						'meta' => array(
							'event' => $myRow->EvCode,
							'descr' => $myRow->EvEventName,
							'finished' => ($myRow->EvShootOff ? 1: 0),
							'fields' => $fields,
							'version' => $myRow->DocVersion,
							'versionDate' => $myRow->DocVersionDate,
							'versionNotes' => $myRow->DocNotes,
							'matchMode' => $myRow->RrLevMatchMode,
							'lastUpdate' => '0000-00-00 00:00:00',
							'hasShootOff' => '',
						),
						'levels'=>array(),
					);
				}
				if(empty($this->data['sections'][$myRow->EvCode]['levels'][$myRow->M1Level])) {
					$this->data['sections'][$myRow->EvCode]['levels'][$myRow->M1Level]=[
						'name' => $myRow->RrLevName,
						'qualifiedNo' => $myRow->QualifiedNo,
						'finished' => ($myRow->RrLevSoSolved ? 1: 0),
						'lastUpdate' => '0000-00-00 00:00:00',
						'hasShootOff' => '',
						'tiebreaker' => $myRow->RrLevTieBreakSystem ? get_text('TiebreakSystem-'.$myRow->RrLevTieBreakSystem, 'RoundRobin') : '',
						'tiebreaker2' => $myRow->RrLevTieBreakSystem2 ? get_text('TiebreakSystem-'.$myRow->RrLevTieBreakSystem2, 'RoundRobin') : '',
						'tb-1' => $myRow->RrLevTieBreakSystem ? get_text('TieBreak-1-Short', 'RoundRobin') : '',
						'tb-2' => $myRow->RrLevTieBreakSystem2 ? get_text('TieBreak-2-Short', 'RoundRobin') : '',
						'soNumArrows' => $myRow->RrLevSO,
						'arrows' => $myRow->RrLevArrows,
						'ends' => $myRow->RrLevEnds,
						'tiesAllowed' => $myRow->RrLevTieAllowed,
						'bestRankMode' => $myRow->RrLevBestRankMode,
						'archersInGroup' => $myRow->RrLevGroupArchers,
						'numMatches' => (int) ceil($myRow->RrLevGroupArchers/2),
						'ranks' => [],
						'matches' => [],
					];
				}

				if(empty($this->data['sections'][$myRow->EvCode]['levels'][$myRow->M1Level]['matches']['g'.$myRow->M1Group])) {
					$this->data['sections'][$myRow->EvCode]['levels'][$myRow->M1Level]['matches']['g'.$myRow->M1Group]=[
						'name' => $myRow->RrGrName,
						'rounds' => [],
					];
				}

				if(empty($this->data['sections'][$myRow->EvCode]['levels'][$myRow->M1Level]['matches']['g'.$myRow->M1Group]['rounds'][$myRow->M1Round])) {
					if($TourDates and !empty($TourDates[$myRow->M1ScheduledDate])) {
						$Place=$TourDates[$myRow->M1ScheduledDate];
					}
					$this->data['sections'][$myRow->EvCode]['levels'][$myRow->M1Level]['matches']['g'.$myRow->M1Group]['rounds'][$myRow->M1Round]=[
						'name' => get_text('RoundNum','RoundRobin', $myRow->M1Round),
						'place' => $Place,
						'items' => [],
					];
				}

				$this->data['sections'][$myRow->EvCode]['levels'][$myRow->M1Level]['matches']['g'.$myRow->M1Group]['rounds'][$myRow->M1Round]['items'][]=[
					// 'lineJudge' => $myRow->LineJudge,
					// 'targetJudge' => $myRow->TargetJudge,
					'liveFlag' => $myRow->LiveFlag,
					'scheduledDate' => $myRow->M1ScheduledDate,
					'scheduledTime' => substr($myRow->M1ScheduledTime, 0, 5),
					// 'scheduledKey' => $myRow->ScheduledKey,
					'lastUpdated' => $myRow->M1DateTime,
					'matchNo' => $myRow->M1MatchNo,
					// 'isValidMatch'=> ($myRow->GridPosition + $myRow->OppGridPosition),
					// 'coach' => $myRow->Coach,
					// 'bib' => $myRow->Bib,
					// 'localBib' => $myRow->LocalBib,
					// 'odfMatchName' => $myRow->OdfMatchName ? $myRow->OdfMatchName : '',
					// 'odfPath' => $myRow->OdfPreviousMatch && intval($myRow->OdfPreviousMatch)==0 ? $myRow->OdfPreviousMatch : get_text(($myRow->MatchNo==2 or $myRow->MatchNo==3) ? 'LoserMatchName' : 'WinnerMatchName', 'ODF', $myRow->OdfPreviousMatch ? $myRow->OdfPreviousMatch : $myRow->PreviousMatchTime),
					// 'birthDate' => $myRow->BirthDate,
					'itemId' => $myRow->M1Athlete,
					'itemSubTeam' => $myRow->M1SubTeam,
					'target' => ltrim($myRow->M1Target, '0'),
					'athlete' => $myRow->Athlete1,
					'countryCode' => $myRow->CoShort1,
					'countryName' => $myRow->Country1,
					// 'qualRank' => $myRow->ShowRankQual ? $myRow->QualRank : $myRow->IrmTextQual,
					// 'qualIrm' => $myRow->IrmQual,
					// 'qualIrmText' => $myRow->IrmTextQual,
					// 'qualScore'=> $myRow->QualScore,
					// 'qualNotes'=> $myRow->QualNotes,
					// 'finRank' => $myRow->FinRank,
					// 'showRank' => $myRow->ShowRankFin,
					// 'finIrm' => $myRow->IrmFin,
					// 'finIrmText' => $myRow->IrmTextFin,
					'irm' => $myRow->M1Irm,
					'irmText' => $myRow->M1IrmText,
					'winner' => $myRow->M1WinLose,
					'score'=> $myRow->M1Score,
					'setScore'=> $myRow->M1SetScore,
					'setPoints'=> $myRow->M1SetPoints,
					'setPointsByEnd'=> $myRow->M1SetPointsByEnd,
					'points'=> $myRow->M1RoundPoints,
					'tieBreaker'=> $myRow->M1TieBreaker,
					'tieBreaker2'=> $myRow->M1TieBreaker2,
					'tie'=> $myRow->M1Tie,
					'arrowstring'=> $myRow->M1Arrowstring,
					'tiebreak'=> $myRow->M1Tiebreak,
					'closest' => $myRow->M1TbClosest,
					'tiebreakDecoded'=> $myRow->M1TbDecoded,
					// 'arrowpositionAvailable'=>($myRow->ArrowPosition != ''),
					'status'=>$myRow->M1Status,
					'scoreConfirmed'=>$myRow->M1Confirmed,
					// 'record' => $this->ManageBitRecord($myRow->RecBitLevel, $myRow->CaCode, $myRow->MaCode, $myRow->EvIsPara),
					'shootFirst'=>$myRow->M1ShootFirst,
					// 'position'=> $myRow->QualRank ? $myRow->QualRank : ($myRow->Position>$myRow->EvNumQualified ? 0 : $myRow->Position),
					// 'saved'=> ($myRow->Position>0 and $myRow->Position<=SavedInPhase($myRow->EvFinalFirstPhase)),
					//
					'oppLastUpdated' => $myRow->M2DateTime,
					'oppMatchNo' => $myRow->M2MatchNo,
					// 'oppCoach' => $myRow->OppCoach,
					// 'oppBib' => $myRow->OppBib,
					// 'oppLocalBib' => $myRow->OppLocalBib,
					// 'oppOdfMatchName' => $myRow->OppOdfMatchName,
					// 'oppOdfPath' => $myRow->OppOdfPreviousMatch && intval($myRow->OppOdfPreviousMatch)==0 ? $myRow->OppOdfPreviousMatch : get_text(($myRow->OppMatchNo==2 or $myRow->OppMatchNo==3) ? 'LoserMatchName' : 'WinnerMatchName', 'ODF', $myRow->OppOdfPreviousMatch ? $myRow->OppOdfPreviousMatch : $myRow->OppPreviousMatchTime),
					// 'oppBirthDate' => $myRow->OppBirthDate,
					'oppItemId' => $myRow->M2Athlete,
					'oppTarget' => ltrim($myRow->M2Target,'0'),
					'oppAthlete' => $myRow->Athlete2,
					// 'oppFullName' => ($myRow->OppNameOrder ? $oppAthlete : $myRow->OppGivenName . ' ' . $myRow->OppFamilyNameUpper),
					// 'oppFamilyName' => $myRow->OppFamilyName,
					// 'oppFamilyNameUpper' => $myRow->OppFamilyNameUpper,
					// 'oppGivenName' => $myRow->OppGivenName,
					// 'oppNameOrder' => $myRow->OppNameOrder,
					// 'oppGender' => $myRow->OppGender,
					// 'oppCountryId' => $myRow->OppCountryId,
					'oppCountryCode' => $myRow->CoShort2,
					'oppCountryName' => $myRow->Country2,
					// 'oppContAssoc' => $myRow->OppCaCode,
					// 'oppMemberAssoc' => $myRow->OppMaCode,
					// 'oppCountryIocCode'=> $myRow->OppCountryIocCode,
					// 'oppQualRank' => $myRow->OppShowRankQual ? $myRow->OppQualRank : $myRow->OppIrmTextQual,
					// 'oppQualIrm' => $myRow->OppIrmQual,
					// 'oppQualIrmText' => $myRow->OppIrmTextQual,
					// 'oppQualScore'=> $myRow->OppQualScore,
					// 'oppQualNotes'=> $myRow->OppQualNotes,
					// 'oppFinRank' => $myRow->OppFinRank,
					// 'oppShowRank' => $myRow->OppShowRankFin,
					// 'oppFinIrm' => $myRow->OppIrmFin,
					// 'oppFinIrmText' => $myRow->OppIrmTextFin,
					'oppIrm' => $myRow->M2Irm,
					'oppIrmText' => $myRow->M2IrmText,
					'oppWinner' => $myRow->M2WinLose,
					'oppScore'=> $myRow->M2Score,
					'oppSetScore'=> $myRow->M2SetScore,
					'oppSetPoints'=> $myRow->M2SetPoints,
					'oppSetPointsByEnd'=> $myRow->M2SetPointsByEnd,
					// 'oppNotes'=> $myRow->OppNotes,
					'oppPoints'=> $myRow->M2RoundPoints,
					'oppTieBreaker'=> $myRow->M2TieBreaker,
					'oppTieBreaker2'=> $myRow->M2TieBreaker2,
					'oppTie'=> $myRow->M2Tie,
					'oppArrowstring'=> $myRow->M2Arrowstring,
					'oppTiebreak'=> $myRow->M2Tiebreak,
					'oppClosest' => $myRow->M2TbClosest,
					'oppTiebreakDecoded'=> $myRow->M2TbDecoded,
					// 'oppArrowpositionAvailable'=>($myRow->OppArrowPosition != ''),
					'oppStatus'=>$myRow->M2Status,
					'oppScoreConfirmed'=>$myRow->M2Confirmed,
					// 'oppRecord' => $this->ManageBitRecord($myRow->OppRecBitLevel, $myRow->OppCaCode, $myRow->OppMaCode, $myRow->EvIsPara),
					'oppShootFirst'=>$myRow->M2ShootFirst,
					// 'oppPosition'=> $myRow->OppQualRank ? $myRow->OppQualRank : ($myRow->OppPosition>$myRow->EvNumQualified ? 0 : $myRow->OppPosition),
					// 'oppSaved'=> ($myRow->OppPosition>0 and $myRow->OppPosition<=SavedInPhase($myRow->EvFinalFirstPhase)),
				];

				if ($myRow->M1DateTime>$this->data['meta']['lastUpdate']) {
					$this->data['meta']['lastUpdate']=$myRow->M1DateTime;
				}
				if ($myRow->M2DateTime>$this->data['meta']['lastUpdate']) {
					$this->data['meta']['lastUpdate']=$myRow->M2DateTime;
				}
				if ($myRow->M1DateTime>$this->data['sections'][$myRow->EvCode]['meta']['lastUpdate']) {
					$this->data['sections'][$myRow->EvCode]['meta']['lastUpdate']=$myRow->M1DateTime;
				}
				if ($myRow->M2DateTime>$this->data['sections'][$myRow->EvCode]['meta']['lastUpdate']) {
					$this->data['sections'][$myRow->EvCode]['meta']['lastUpdate']=$myRow->M2DateTime;
				}
				if ($myRow->M1DateTime>$this->data['sections'][$myRow->EvCode]['levels'][$myRow->M1Level]['lastUpdate']) {
					$this->data['sections'][$myRow->EvCode]['levels'][$myRow->M1Level]['lastUpdate']=$myRow->M1DateTime;
				}
				if ($myRow->M2DateTime>$this->data['sections'][$myRow->EvCode]['levels'][$myRow->M1Level]['lastUpdate']) {
					$this->data['sections'][$myRow->EvCode]['levels'][$myRow->M1Level]['lastUpdate']=$myRow->M2DateTime;
				}
			}
		}
	}

	/**
	 * @param $orderByTarget
	 * @return void
	 * Only returns the query for the scorecards, so the match grids!
	 */
	public function getQuery($orderByTarget=false) {
		$MFilter=$this->safeFilter('Matches');

		$SQL="select m1.*, m2.*,
       		EvCode, EvEventName, EvTeamEvent, EvProgr, EvShootOff, RrLevMatchMode, RrLevBestRankMode,
            coalesce(En1Entry, Te1Country) as Athlete1, coalesce(En1Country, Te1Code) as Country1, coalesce(En1CoShort, Te1Code) as CoShort1, coalesce(En1CoName, Te1Country) as CoName1,
            coalesce(En2Entry, Te2Country) as Athlete2, coalesce(En2Country, Te2Code) as Country2, coalesce(En2CoShort, Te2Code) as CoShort2, coalesce(En2CoName, Te2Country) as CoName2,
			coalesce(En1Rank, Te1Rank) as Rank1, coalesce(En2Rank, Te2Rank) as Rank2,
			coalesce(En1EntryShort, Te1Short) as AthleteShort1, coalesce(En2EntryShort, Te2Short) as AthleteShort2,
       		ifnull(concat(DV2.DvMajVersion, '.', DV2.DvMinVersion) ,concat(DV1.DvMajVersion, '.', DV1.DvMinVersion)) as DocVersion,
			date_format(ifnull(DV2.DvPrintDateTime, DV1.DvPrintDateTime), '%e %b %Y %H:%i UTC') as DocVersionDate,
			ifnull(DV2.DvNotes, DV1.DvNotes) as DocNotes,
            RrLevGroups*RrLevGroupArchers as QualifiedNo, RrLevName, RrLevSoSolved, RrLevTieBreakSystem, RrLevTieBreakSystem2, RrLevArrows, RrLevEnds, RrLevSO, RrLevGroupArchers, RrLevTieAllowed,
			RrGrName
		from (
		    select 
		        RrMatchSwapped as Swapped,
				RrMatchTournament as M1Tournament, 
				RrMatchEvent as M1Event, 
				RrMatchTeam as M1Team,
				RrMatchLevel as M1Level,
				RrMatchGroup as M1Group,
				RrMatchRound as M1Round,
				RrMatchMatchNo as M1MatchNo,
				RrMatchTarget as M1Target,
				RrMatchScheduledDate as M1ScheduledDate,
				RrMatchScheduledTime as M1ScheduledTime,
				RrMatchScheduledLength as M1ScheduledLength,
				RrMatchAthlete as M1Athlete,
				RrMatchSubTeam as M1SubTeam,
				RrMatchRank as M1Rank,
				RrMatchScore as M1Score,
				RrMatchSetScore as M1SetScore,
				RrMatchSetPoints as M1SetPoints,
				RrMatchSetPointsByEnd as M1SetPointsByEnd,
				RrMatchWinnerSet as M1WinnerSet,
				RrMatchTie as M1Tie,
				RrMatchArrowstring as M1Arrowstring,
				RrMatchTiebreak as M1Tiebreak,
				RrMatchTbClosest as M1TbClosest,
				RrMatchTbDecoded as M1TbDecoded,
				RrMatchArrowPosition as M1ArrowPosition,
				RrMatchTiePosition as M1TiePosition,
				RrMatchWinLose as M1WinLose,
				RrMatchFinalRank as M1FinalRank,
				RrMatchDateTime as M1DateTime,
				RrMatchSyncro as M1Syncro,
				RrMatchLive as LiveFlag,
				RrMatchStatus as M1Status,
				RrMatchShootFirst as M1ShootFirst,
				RrMatchVxF as M1VxF,
				RrMatchConfirmed as M1Confirmed,
				RrMatchNotes as M1Notes,
				RrMatchRecordBitmap as M1RecordBitmap,
				RrMatchIrmType as M1Irm,
				IrmType as M1IrmText,
				RrMatchCoach as M1Coach,
				RrMatchRoundPoints as M1RoundPoints,
				RrMatchTieBreaker as M1TieBreaker,
				RrMatchTieBreaker2 as M1TieBreaker2
		    from RoundRobinMatches
		    inner join IrmTypes on IrmId=RrMatchIrmType
		    where $MFilter and RrMatchMatchNo%2=0 ".(isset($this->opts['matchno']) ? 'and RrMatchMatchNo='.intval($this->opts['matchno']): '')."
			) m1
	    inner join (
		    select 
				RrMatchEvent as M2Event, 
				RrMatchTeam as M2Team,
				RrMatchLevel as M2Level,
				RrMatchGroup as M2Group,
				RrMatchRound as M2Round,
				RrMatchMatchNo as M2MatchNo,
				RrMatchTarget as M2Target,
				RrMatchAthlete as M2Athlete,
				RrMatchSubTeam as M2SubTeam,
				RrMatchRank as M2Rank,
				RrMatchScore as M2Score,
				RrMatchSetScore as M2SetScore,
				RrMatchSetPoints as M2SetPoints,
				RrMatchSetPointsByEnd as M2SetPointsByEnd,
				RrMatchWinnerSet as M2WinnerSet,
				RrMatchTie as M2Tie,
				RrMatchArrowstring as M2Arrowstring,
				RrMatchTiebreak as M2Tiebreak,
				RrMatchTbClosest as M2TbClosest,
				RrMatchTbDecoded as M2TbDecoded,
				RrMatchArrowPosition as M2ArrowPosition,
				RrMatchTiePosition as M2TiePosition,
				RrMatchWinLose as M2WinLose,
				RrMatchFinalRank as M2FinalRank,
				RrMatchDateTime as M2DateTime,
				RrMatchSyncro as M2Syncro,
				RrMatchStatus as M2Status,
				RrMatchShootFirst as M2ShootFirst,
				RrMatchVxF as M2VxF,
				RrMatchConfirmed as M2Confirmed,
				RrMatchNotes as M2Notes,
				RrMatchRecordBitmap as M2RecordBitmap,
				RrMatchIrmType as M2Irm,
				IrmType as M2IrmText,
				RrMatchCoach as M2Coach,
				RrMatchRoundPoints as M2RoundPoints,
				RrMatchTieBreaker as M2TieBreaker,
				RrMatchTieBreaker2 as M2TieBreaker2
		    from RoundRobinMatches
		    inner join IrmTypes on IrmId=RrMatchIrmType
		    where $MFilter
			) m2 on M2Event=M1Event and M2Team=M1Team and M2Level=M1Level and M2Group=M1Group and M2Round=M1Round and M2MatchNo=M1MatchNo+1
	    inner join RoundRobinGroup on RrGrTournament=M1Tournament and RrGrTeam=M1Team and RrGrEvent=M1Event and RrGrLevel=M1Level and RrGrGroup=M1Group
	    inner join RoundRobinLevel on RrLevTournament=M1Tournament and RrLevTeam=M1Team and RrLevEvent=M1Event and RrLevLevel=M1Level
		inner join Events on EvTournament=M1Tournament and EvCode=M1Event and EvTeamEvent=M1Team and EvElimType=5
		LEFT JOIN DocumentVersions DV1 on EvTournament=DV1.DvTournament AND DV1.DvFile = 'ROBIN' and DV1.DvEvent=''
		LEFT JOIN DocumentVersions DV2 on EvTournament=DV2.DvTournament AND DV2.DvFile = 'ROBIN' and DV2.DvEvent=EvCode
		left join (
		    select EnId as En1Id, IndEvent as En1Event, IndRank as En1Rank, trim(concat_ws(' ', upper(EnFirstName), EnName)) as En1Entry, trim(concat(upper(EnFirstName), ' ', left(EnName,1))) as En1EntryShort, concat_ws('-', CoCode, CoName) as En1Country, CoCode as En1CoShort, CoName as En1CoName
			from Entries
			inner join Individuals on IndId=EnId and IndTournament=EnTournament
		    inner join Countries on CoTournament=EnTournament and CoId=EnCountry
		    where EnTournament=$this->tournament
		    ) en1 on En1Id=M1Athlete and M1Team=0 and En1Event=M1Event
		left join (
		    select EnId as En2Id, IndEvent as En2Event, IndRank as En2Rank, trim(concat_ws(' ', upper(EnFirstName), EnName)) as En2Entry, trim(concat(upper(EnFirstName), ' ', left(EnName,1))) as En2EntryShort, concat_ws('-', CoCode, CoName) as En2Country, CoCode as En2CoShort, CoName as En2CoName 
			from Entries
			inner join Individuals on IndId=EnId and IndTournament=EnTournament
		    inner join Countries on CoTournament=EnTournament and CoId=EnCountry
		    where EnTournament=$this->tournament
		    ) en2 on En2Id=M2Athlete and M2Team=0 and En2Event=M1Event
		left join (
		    select CoId as Te1Id, CoName as Te1Country, CoCode as Te1Code, TeSubTeam as Te1SubTeam, TeEvent as Te1Event, TeRank as Te1Rank, CoCode as Te1Short
		    from Teams
	        inner join Countries on TeCoId=CoId and TeTournament=CoTournament
		    where CoTournament={$this->tournament} and TeFinEvent=1
		    ) te1 on Te1Id=M1Athlete and Te1SubTeam=M1SubTeam and M1Team=1 and Te1Event=M1Event
		left join (
		    select CoId as Te2Id, CoName as Te2Country, CoCode as Te2Code, TeSubTeam as Te2SubTeam, TeEvent as Te2Event, TeRank as Te2Rank, CoCode as Te2Short
		    from Teams
	        inner join Countries on TeCoId=CoId and TeTournament=CoTournament
		    where CoTournament={$this->tournament} and TeFinEvent=1
		    ) te2 on Te2Id=M2Athlete and Te2SubTeam=M2SubTeam and M2Team=1 and Te2Event=M1Event
		order by ".($orderByTarget ? 'greatest(M1Target,M2Target), ' : '')."EvTeamEvent, EvProgr, M1Level, M1Group, M1Round, greatest(M1Target,M2Target)
		";

		return $SQL;
	}
}
