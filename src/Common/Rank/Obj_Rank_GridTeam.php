<?php
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/Fun_PrintOuts.php');

/**
 * Obj_Rank_FinalTeam
 *
 * Implementa l'algoritmo di default per il recupero delle griglie finali individuali.
 * E' in sola lettura
 *
 *
 * A seconda della fase che sto trattando avrò porzioni di colonna da gestire differenti e calcoli differenti.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		events => array(<ev_1>,<ev_2>,...,<ev_n>)
 * 		tournament => #
 * )
 *
 * con:
 * 	 events: l'array con le coppie evento@fase di cui voglio la griglia.
 *  tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *
 * Estende Obj_Rank
 */
	class Obj_Rank_GridTeam extends Obj_Rank
	{
	/**
	 * safeFilter()
	 * Protegge con gli apici gli elementi di $this->opts['events']
	 *
	 * @return mixed: false se non c'è filtro oppure la stringa da inserire nella where delle query
	 */
		var $EnIdFound=array();
		var $TeamFound='';

		protected function safeFilter() {
			$ret=array();
			if (!empty($this->opts['events'])) {
				if(!is_array($this->opts['events'])) $this->opts['events']=array($this->opts['events']);

				$f=array();

				foreach ($this->opts['events'] as $e) {
				    $event=$e;
                    $phase=null;
                    if(strpos($e,'@') !== false) {
                        @list($event, $phase) = explode('@', $e);
                    }
					if($event and !is_null($phase)) $f[] = '(EvCode=' . StrSafe_DB($event) . ' AND GrPhase=' . $phase . ')';
					elseif($event) $f[] = '(EvCode=' . StrSafe_DB($event) . ')';
					elseif(!is_null($phase)) $f[] = '(GrPhase=' . $phase . ')';
				}

				if($f) $ret[]= '(' . implode(' OR ', $f) . ')';
			}
			if(!empty($this->opts['schedule'])) {
				$ret[]="CONCAT(fs1.FSScheduledDate,' ',fs1.FSScheduledTime)=" . StrSafe_DB($this->opts['schedule']) . "";
			}
			if(!empty($this->opts['date'])) {
				$ret[]="fs1.FSScheduledDate=" . StrSafe_DB($this->opts['date']) . "";
			}
			if($ret) return ' AND '.implode(' AND ', $ret);
			return '';
		}

		public function __construct($opts)
		{
			parent::__construct($opts);
		}

	/**
	 * calculate()
	 *
	 * Al primo errore termina con false!
	 *
	 * @Override
	 *
	 * (non-PHPdoc)
	 * @see ianseo/Common/Rank/Obj_Rank#calculate()
	 */
		public function calculate()
		{
			return true;
		}

		public function getQuery($OrderByTarget=false) {
			$filter=$this->safeFilter();

			$ExtraFilter=array();
			if(!empty($this->EnIdFound)) {
				$ExtraFilter[] = 'Event in ("'.implode('","', $this->EnIdFound).'") AND (Team='.StrSafe_DB($this->TeamFound).' or oppTeam='.StrSafe_DB($this->TeamFound).')';
			}
			if(!empty($this->opts['coid'])) {
				$ExtraFilter[] = "(Team=" . intval($this->opts['coid']) . " or OppTeam=" . intval($this->opts['coid']) . ") ";
			}
			if(isset($this->opts['matchno'])) {
				$ExtraFilter[] = "(MatchNo=" . intval($this->opts['matchno']) . ' or OppMatchNo =' . intval($this->opts['matchno']) . ')';
			}
			if(isset($this->opts['matchnoArray'])) {
				$ExtraFilter[] = "(MatchNo in (" . implode(',', $this->opts['matchnoArray']) . ')';
			}
			if(isset($this->opts['liveFlag'])) {
				$ExtraFilter[] = "LiveFlag=1";
			}
			if($ExtraFilter) {
				$ExtraFilter = 'WHERE ' . implode(' AND ', $ExtraFilter);
			} else {
				$ExtraFilter = '';
			}

			$SQL = "SELECT f1.*, f2.*, coalesce(OdfTrOdfCode,'') as OdfUnitCode,
       				coalesce(JudgeLine,'') as LineJudge, coalesce(JudgeLineCode,'') as LineCode, coalesce(JudgeLineCodeLocal,'') as LineCodeLocal, coalesce(JudgeLineFamName,'') as LineFamName, coalesce(JudgeLineGivName,'') as LineGivName, coalesce(JudgeLineCountry,'') as LineCountry, coalesce(JudgeLineGender,'') as LineGender, coalesce(OdfLineCode,'') as LineOdfCode,
       				coalesce(JudgeTarget,'') as TargetJudge, coalesce(JudgeTargetCode,'') as TargetCode, coalesce(JudgeTargetCodeLocal,'') as TargetCodeLocal, coalesce(JudgeTargetFamName,'') as TargetFamName, coalesce(JudgeTargetGivName,'') as TargetGivName, coalesce(JudgeTargetCountry,'') as TargetCountry, coalesce(JudgeTargetGender,'') as TargetGender, coalesce(OdfTargetCode,'') as TargetOdfCode,
       				coalesce(Coach1,'') as Coach, coalesce(Coach1Code,'') as CoachCode, coalesce(Coach1FamName,'') as CoachFamName, coalesce(Coach1GivName,'') as CoachGivName, coalesce(Coach1Country,'') as CoachCountry, coalesce(Coach1Gender,'') as CoachGender,
       				coalesce(Coach2,'') as OppCoach, coalesce(Coach2Code,'') as OppCoachCode, coalesce(Coach2FamName,'') as OppCoachFamName, coalesce(Coach2GivName,'') as OppCoachGivName, coalesce(Coach2Country,'') as OppCoachCountry, coalesce(Coach2Gender,'') as OppCoachGender,
					ifnull(concat(DV2.DvMajVersion, '.', DV2.DvMinVersion) ,concat(DV1.DvMajVersion, '.', DV1.DvMinVersion)) as DocVersion,
					date_format(ifnull(DV2.DvPrintDateTime, DV1.DvPrintDateTime), '%e %b %Y %H:%i UTC') as DocVersionDate,
					ifnull(DV2.DvNotes, DV1.DvNotes) as DocNotes from ("
				. "select"
				. " fs1.FsOdfMatchName OdfMatchName,"
				. " ifnull(fs2.FsOdfMatchName, concat('RR #',if(EvFinalFirstPhase in (12,24,48), GrPosition2, GrPosition))) as OdfPreviousMatch,"
				. " TfArrowPosition ArrowPosition, TfTiePosition TiePosition, TfShootingArchers as ShootingArchers,"
				. " EvCode Event,"
				. " EvOdfCode OdfCode,"
				. " EvEventName EventDescr,"
				. " EvFinalFirstPhase, EvNumQualified, EvOdfCode, "
				. " EvMaxTeamPerson,"
				. " EvFinalPrintHead,"
                . " EvCheckGolds, "
                . " EvCheckXNines, "
                . " EvGolds, "
                . " EvXNine, "
                . " EvGoldsChars, "
                . " EvXNineChars, "
				. " EvMatchMode,"
				. " EvWinnerFinalRank,"
				. " EvFinalFirstPhase=EvNumQualified as NoRealPhase,"
				. " EvProgr,"
				. " EvShootOff,"
				. " EvCodeParent,"
				. " EvMixedTeam,"
				. " GrPhase Phase,"
				. " @BitPhase:=if(GrPhase=0, 1, pow(2, ceil(log2(GrPhase))+1)),"
				. " @BitPhase & EvMatchArrowsNo!=0 as FinElimChooser,"
				. " GrPosition Position,"
				. " GrPosition2 Position2,"
				. " TfTournament Tournament,"
				. " TfTeam Team,"
				. " TfSubTeam SubTeam,"
				. " TfMatchNo MatchNo,"
				. " TfCoach CoachId,"
				. " TeRank QualRank,"
				. " i2.IrmType IrmTextQual,"
				. " i2.IrmShowRank ShowRankQual,"
				. " TeRankFinal FinRank,"
				. " i3.IrmType IrmTextFin,"
				. " i3.IrmShowRank ShowRankFin,"
				. " TfIrmType Irm,"
				. " i1.IrmType IrmText,"
				. " i1.IrmShowRank ShowRank,"
				. " TeScore QualScore, "
				. " TeNotes QualNotes, "
				. " TfWinLose Winner, "
				. " TfDateTime LastUpdated, "
				. " CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as CountryName,"
				. " CoCode as CountryCode,"
				. " CoMaCode as MaCode,"
				. " CoCaCode as CaCode,"
				. " TfScore AS Score,"
                . " TfGolds Golds,"
                . " TfXNines XNines,"
				. " TfSetScore as SetScore,"
				. " TfTie Tie,"
				. " TfTieBreak TieBreak,"
                . " TfTbClosest TieClosest,"
                . " TfTbDecoded TieDecoded,"
				. " TfStatus Status, "
				. " TfRecordBitmap  as RecBitLevel, EvIsPara, "
				. " TfConfirmed Confirmed, "
				. " TfSetPoints SetPoints, "
				. " TfSetPointsByEnd SetPointsByEnd, "
                . " fs1.FsLJudge as jLine, fs1.FsTJudge as jTarget, "
				. " TfArrowstring Arrowstring, TfLive LiveFlag,"
				. " if(@BitPhase & EvMatchMultipleMatches!=0 or @BitPhase & EvFinalAthTarget!=0, fs1.FsLetter, fs1.FsTarget) as Target,"
				. " TfNotes Notes, TfShootFirst as ShootFirst, "
				. " TarId, TarDescr, EvDistance as Distance, EvTargetSize as TargetSize, "
				. "	EvFinEnds, EvFinArrows, EvFinSO, EvElimEnds, EvElimArrows, EvElimSO, "
				. " concat(fs1.FSScheduledDate,' ',fs1.FSScheduledTime) AS ScheduledKey, "
				. " concat(fs2.FSScheduledDate,' ',fs2.FSScheduledTime) AS PreviousMatchTime, "
				. " DATE_FORMAT(fs1.FSScheduledDate,'" . get_text('DateFmtDB') . "') as ScheduledDate,"
				. " DATE_FORMAT(fs1.FSScheduledTime,'" . get_text('TimeFmt') . "') AS ScheduledTime, if(EvFinalFirstPhase%12=0, GrPosition2, GrPosition) as GridPosition  "
				. " FROM TeamFinals "
				. " INNER JOIN Events ON TfEvent=EvCode AND TfTournament=EvTournament AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 and EvTournament=$this->tournament "
				. " INNER JOIN Grids ON TfMatchNo=GrMatchNo "
				. " INNER JOIN Targets ON EvFinalTargetType=TarId "
				. " INNER JOIN IrmTypes i1 ON i1.IrmId=TfIrmType "
				. " LEFT JOIN Teams ON TfTeam=TeCoId AND TfSubTeam=TeSubTeam AND TfEvent=TeEvent AND TfTournament=TeTournament AND TeFinEvent=1 and TeTournament=$this->tournament "
				. " left JOIN IrmTypes i2 ON i2.IrmId=TeIrmType "
				. " left JOIN IrmTypes i3 ON i3.IrmId=TeIrmTypeFinal "
				. " LEFT JOIN Countries ON TfTeam=CoId AND TfTournament=CoTournament and CoTournament=$this->tournament "
				. " LEFT JOIN FinSchedule fs1 ON TfEvent=fs1.FSEvent AND fs1.FSMatchNo=TfMatchNo AND TfTournament=fs1.FSTournament AND fs1.FSTeamEvent='1' and fs1.FSTournament=$this->tournament "
				. " LEFT JOIN FinSchedule fs2 ON TfEvent=fs2.FSEvent AND fs2.FSMatchNo=case TfMatchNo when 0 then 4 when 1 then 6 when 2 then 4 when 3 then 6 else TfMatchNo*2 end AND TfTournament=fs2.FSTournament AND fs2.FSTeamEvent='1' and fs2.FSTournament=$this->tournament "
				. " WHERE TfMatchNo%2=0 AND TfTournament = " . $this->tournament . " " . $filter
				. ") f1 inner join ("
				. "select"
				. " fs1.FsOdfMatchName OppOdfMatchName,"
				. " ifnull(fs2.FsOdfMatchName, concat('RR #',if(EvFinalFirstPhase in (12,24,48), GrPosition2, GrPosition))) as OppOdfPreviousMatch,"
				. " TfArrowPosition OppArrowPosition, TfTiePosition OppTiePosition, TfShootingArchers as OppShootingArchers,"
				. " EvCode OppEvent,"
				. " GrPosition OppPosition,"
				. " GrPosition2 OppPosition2,"
				. " TfTournament OppTournament,"
				. " TfTeam OppTeam,"
				. " TfSubTeam OppSubTeam,"
				. " TfMatchNo OppMatchNo,"
				. " TfCoach OppCoachId,"
				. " TeRank OppQualRank,"
				. " i2.IrmType OppIrmTextQual,"
				. " i2.IrmShowRank OppShowRankQual,"
				. " TeRankFinal OppFinRank,"
				. " i3.IrmType OppIrmTextFin,"
				. " i3.IrmShowRank OppShowRankFin,"
				. " TfIrmType OppIrm,"
				. " i1.IrmType OppIrmText,"
				. " i1.IrmShowRank OppShowRank,"
				. " TeScore OppQualScore, "
				. " TeNotes OppQualNotes, "
				. " TfWinLose OppWinner, "
				. " TfDateTime OppLastUpdated, "
				. " CONCAT(CoName, IF(TfSubTeam>'1',CONCAT(' (',TfSubTeam,')'),'')) as OppCountryName,"
				. " CoCode as OppCountryCode,"
				. " CoMaCode as OppMaCode,"
				. " CoCaCode as OppCaCode,"
				. " TfScore AS OppScore,"
                . " TfGolds OppGolds,"
                . " TfXNines OppXNines,"
				. " TfSetScore as OppSetScore,"
				. " TfTie OppTie,"
				. " TfTieBreak OppTieBreak,"
                . " TfTbClosest OppTieClosest,"
                . " TfTbDecoded OppTieDecoded,"
				. " TfStatus OppStatus, "
				. " TfConfirmed OppConfirmed, "
				. " TfRecordBitmap  as OppRecBitLevel, "
				. " TfSetPoints OppSetPoints, "
				. " TfSetPointsByEnd OppSetPointsByEnd, "
				. " TfArrowstring OppArrowstring, "
				. " @BitPhase:=if(GrPhase=0, 1, pow(2, ceil(log2(GrPhase))+1)),"
				. " if(@BitPhase & EvMatchMultipleMatches!=0 or @BitPhase & EvFinalAthTarget!=0, fs1.FsLetter, fs1.FsTarget) as OppTarget, "
				. " concat(fs2.FSScheduledDate,' ',fs2.FSScheduledTime) AS OppPreviousMatchTime, "
				. " TfNotes OppNotes, TfShootFirst as OppShootFirst, if(EvFinalFirstPhase%12=0, GrPosition2, GrPosition) as OppGridPosition  "
				. " FROM TeamFinals "
				. " INNER JOIN Events ON TfEvent=EvCode AND TfTournament=EvTournament AND EvTeamEvent=1 AND EvFinalFirstPhase!=0 and EvTournament=$this->tournament "
				. " INNER JOIN Grids ON TfMatchNo=GrMatchNo "
				. " INNER JOIN IrmTypes i1 ON i1.IrmId=TfIrmType "
				. " LEFT JOIN Teams ON TfTeam=TeCoId AND TfSubTeam=TeSubTeam AND TfEvent=TeEvent AND TfTournament=TeTournament AND TeFinEvent=1 and TeTournament=$this->tournament "
				. " left JOIN IrmTypes i2 ON i2.IrmId=TeIrmType "
				. " left JOIN IrmTypes i3 ON i3.IrmId=TeIrmTypeFinal "
				. " LEFT JOIN Countries ON TfTeam=CoId AND TfTournament=CoTournament and CoTournament=$this->tournament "
				. " LEFT JOIN FinSchedule fs1 ON fs1.FSEvent=TfEvent AND fs1.FSMatchNo=TfMatchNo AND fs1.FSTournament=TfTournament AND fs1.FSTeamEvent='1' and fs1.FSTournament=$this->tournament "
				. " LEFT JOIN FinSchedule fs2 ON fs2.FSEvent=TfEvent AND fs2.FSMatchNo=case TfMatchNo when 0 then 4 when 1 then 6 when 2 then 4 when 3 then 6 else TfMatchNo*2 end AND fs2.FSTournament=TfTournament AND fs2.FSTeamEvent='1' and fs2.FSTournament=$this->tournament "
				. " WHERE TfMatchNo%2=1 AND TfTournament = " . $this->tournament . " " . $filter
				. ") f2 on Tournament=OppTournament and Event=OppEvent and MatchNo=OppMatchNo-1
				LEFT JOIN DocumentVersions DV1 on Tournament=DV1.DvTournament AND DV1.DvFile = 'B-TEAM' and DV1.DvEvent=''
				LEFT JOIN DocumentVersions DV2 on Tournament=DV2.DvTournament AND DV2.DvFile = 'B-TEAM' and DV2.DvEvent=Event 
                LEFT JOIN  (SELECT OdfTrOdfCode, OdfTrIanseo 
                    FROM OdfTranslations 
                    WHERE OdfTrTournament={$this->tournament} and OdfTrInternal='MATCH' and OdfTrType='CODE') OdfUnit ON OdfTrIanseo=concat(if((EvFinalFirstPhase, Phase) in ((48, 64), (48,32), (24, 32), (12, 16)), 1, 0),'_', f1.MatchNo)
                LEFT JOIN (
                    select TiId as JudgeLineId, TiCode as JudgeLineCode, IF(TiCodeLocal='',TiCode,TiCodeLocal) JudgeLineCodeLocal, CoCode as JudgeLineCountry, if(TiGender=0, 'M', 'F') as JudgeLineGender, TiName as JudgeLineFamName, TiGivenName as JudgeLineGivName, concat(ucase(TiName), ' ', TiGivenName) as JudgeLine, coalesce(OdfTrOdfCode, 'LNE_JU') as OdfLineCode 
                    from TournamentInvolved
                    inner join Countries on CoId=TiCountry
					left join OdfTranslations on OdfTrTournament=TiTournament and OdfTrInternal='FUNC' and OdfTrType='NAME' and OdfTrIanseo='LJU'
                    where TiTournament={$this->tournament}) jLine on f1.jLine=JudgeLineId  
                LEFT JOIN (
                    select TiId as JudgeTargetId, TiCode as JudgeTargetCode, IF(TiCodeLocal='',TiCode,TiCodeLocal) JudgeTargetCodeLocal, CoCode as JudgeTargetCountry, if(TiGender=0, 'M', 'F') as JudgeTargetGender, TiName as JudgeTargetFamName, TiGivenName as JudgeTargetGivName, concat(ucase(TiName), ' ', TiGivenName) as JudgeTarget, coalesce(OdfTrOdfCode, 'TGT_JU') as OdfTargetCode
                    from TournamentInvolved 
                    inner join Countries on CoId=TiCountry
					left join OdfTranslations on OdfTrTournament=TiTournament and OdfTrInternal='FUNC' and OdfTrType='NAME' and OdfTrIanseo='TJU'
                    where TiTournament={$this->tournament}) jTarget on f1.jTarget=JudgeTargetId 
                LEFT JOIN (
                    select EnId as Coach1Id, ifnull(EdExtra,EnCode) Coach1Code, CoCode as Coach1Country, if(EnSex=0, 'M', 'F') as Coach1Gender, EnFirstName as Coach1FamName, EnName as Coach1GivName, concat(ucase(EnFirstName), ' ', EnName) as Coach1
                    from Entries 
                    inner join Countries on CoId=EnCountry and CoTournament=EnTournament
                    LEFT JOIN ExtraData ON EdId=EnId AND EdType='Z' 
                    where EnTournament={$this->tournament}) Coach1 on Coach1Id=CoachId
                LEFT JOIN (
                    select EnId as Coach2Id, ifnull(EdExtra,EnCode) Coach2Code, CoCode as Coach2Country, if(EnSex=0, 'M', 'F') as Coach2Gender, EnFirstName as Coach2FamName, EnName as Coach2GivName, concat(ucase(EnFirstName), ' ', EnName) as Coach2
                    from Entries 
                    inner join Countries on CoId=EnCountry and CoTournament=EnTournament
                    LEFT JOIN ExtraData ON EdId=EnId AND EdType='Z' 
                    where EnTournament={$this->tournament}) Coach2 on Coach2Id=OppCoachId
				$ExtraFilter
                ORDER BY ".($OrderByTarget ? 'Target, ' : '')."EvProgr ASC, event, Phase DESC, MatchNo ASC ";
			return $SQL;
		}

		public function read()
		{
			//error_reporting(E_ALL);
		/*
		 *  prima passata per costruire la struttura del vettore.
		 *  Tiro fuori i nomi delle squadre
		 */
			$MyQueryNames  = "SELECT TfcId, TfcEvent, TfcCoId, TfcSubTeam, TfcOrder, CoCode, EnCode, ifnull(EdExtra, EnCode) LocalCode, EnSex, EnDob, EnNameOrder, ucase(EnFirstName) EnUpperName, EnFirstName, EnName, 
       				concat(ucase(EnFirstName), ' ', EnName) Athlete, CONCAT(TeRank,CHAR(64+TfcOrder)) AS BackNo
				FROM TeamFinComponent
				INNER JOIN Events ON TfcEvent=EvCode AND TfcTournament=EvTournament AND EvTeamEvent=1 AND EvFinalFirstPhase!=0
				INNER JOIN Entries ON TfcId=EnId AND TfcTournament=EnTournament
				INNER JOIN Countries ON CoId=EnCountry AND CoTournament=EnTournament
				INNER JOIN Teams ON TfcCoId=TeCoId AND TfcSubTeam=TeSubTeam AND TfcEvent=TeEvent AND TfcTournament=TeTournament AND TeFinEvent=1
				left join ExtraData on EdId=EnId and EdType='Z'
				WHERE TfcTournament = " . $this->tournament
				. " " . (empty($this->opts['events']) ? '' : CleanEvents($this->opts['events'], 'TfcEvent'))
				. " ORDER BY EvProgr, TfcEvent, TfcCoId, TfcSubTeam, EnSex desc, EnFirstName, TfcOrder ";

			$this->data['sections']=array();
			$q=safe_r_SQL($MyQueryNames);
			while($r=safe_fetch($q)) {
				$this->data['sections'][$r->TfcEvent]['athletes'][$r->TfcCoId][$r->TfcSubTeam][]=array(
					'athlete' => $r->Athlete,
					'backNo' => $r->BackNo,
					'id' => $r->TfcId,
					'code' => $r->EnCode,
					'localBib' => $r->LocalCode,
					'familyName' => $r->EnFirstName,
					'familyUpperName' => $r->EnUpperName,
					'givenName' => $r->EnName,
					'nameOrder' => $r->EnNameOrder,
					'fullName' => ($r->EnNameOrder ? $r->EnUpperName . ' ' . $r->EnName : $r->EnName . ' ' . $r->EnUpperName),
					'gender' => $r->EnSex,
					'birthDate' => $r->EnDob,
					'countryCode' => $r->CoCode,
					);
				if(!empty($this->opts['enid']) and $r->TfcId==$this->opts['enid']) {
					$this->EnIdFound[]=$r->TfcEvent;
					$this->TeamFound=$r->TfcCoId;
				}
			}

			if(!empty($this->opts['enid'])) {
				if( !$this->EnIdFound) return;
				foreach($this->data['sections'] as $ev => $data) if(!in_array($ev, $this->EnIdFound)) unset($this->data['sections'][$ev]);
			}

			$r=safe_r_sql($this->getQuery());

			$this->data['meta']['title']=get_text('BracketsSq');
            $this->data['meta']['saved']=get_text('Seeded8th');
			$this->data['meta']['lastUpdate']='0000-00-00 00:00:00';
			$this->data['meta']['notAwarded']=get_text('NotAwarded','ODF');
            $this->data['meta']['AverageArrowScore']=get_text('OrisScorecardsAverage', 'Tournament');
			$this->data['meta']['fields']=array(
				// qui ci sono le descrizioni dei campi
				'coach' => get_text('Coach', 'Tournament'),
				'lineJudge' => get_text('LineJudge', 'Tournament'),
				'targetJudge' => get_text('TargetJudge', 'Tournament'),
				'scheduledDate' => get_text('Date', 'Tournament'),
				'scheduledTime' => get_text('Time', 'Tournament'),
				'winner' => get_text('Winner'),
				'matchNo' => get_text('MatchNo'),
				'bye' => get_text('Bye'),
				'bib' => get_text('Code','Tournament'),
				'target' => get_text('Target'),
				'athlete' => get_text('Athlete'),
				'familyname' => get_text('FamilyName', 'Tournament'),
				'givenname' => get_text('Name', 'Tournament'),
				'gender' => get_text('Sex', 'Tournament'),
				'countryCode' => get_text('CountryCode'),
				'countryName' => get_text('Country'),
				'countryIocCode'=>'',
				'qualRank' => get_text('RankScoreShort'),
				'finRank' => get_text('FinalRank','Tournament'),
				'qualscore'=>get_text('TotalShort','Tournament'),
				'scoreLong'=>get_text('TotaleScore'),
				'score'=>get_text('TotalShort','Tournament'),
				'setScore'=>get_text('SetTotal','Tournament'),
			 	'setPoints'=>get_text('SetPoints','Tournament'),
				'tie'=>'S.O.',
				'arrowstring'=>get_text('Arrows','Tournament'),
			 	'tiebreak'=>get_text('TieArrows'),
                'closest'=>get_text('Close2Center', 'Tournament'),
                'closestShort'=>get_text('ClosestShort', 'Tournament'),
				'status'=>get_text('Status', 'Tournament'),
				'shootFirst'=>get_text('ShootsFirst', 'Tournament'),

				'oppMatchNo' => get_text('MatchNo'),
				'oppBib' => get_text('Code','Tournament'),
				'oppTarget' => get_text('Target'),
				'oppAthlete' => get_text('Athlete'),
				'oppFamilyname' => get_text('FamilyName', 'Tournament'),
				'oppGivenname' => get_text('Name', 'Tournament'),
				'oppGender' => get_text('Sex', 'Tournament'),
				'oppCountryCode' => get_text('CountryCode'),
				'oppCountryName' => get_text('Country'),
				'oppCountryIocCode'=>'',
				'oppQualRank' => get_text('RankScoreShort'),
				'oppFinRank' => get_text('FinalRank','Tournament'),
				'oppQualScore'=>get_text('TotalShort','Tournament'),
				'oppScore'=>get_text('TotalShort','Tournament'),
				'oppSetScore'=>get_text('SetTotal','Tournament'),
			 	'oppSetPoints'=>get_text('SetPoints','Tournament'),
				'oppTie'=>'S.O.',
				'oppArrowstring'=>get_text('Arrows','Tournament'),
			 	'oppTiebreak'=>get_text('TieArrows'),
                'oppClosest'=>get_text('Close2Center', 'Tournament'),
                'oppClosestShort'=>get_text('ClosestShort', 'Tournament'),
				'oppStatus'=>get_text('Status', 'Tournament'),
				'oppShootFirst'=>get_text('ShootsFirst', 'Tournament')

				);

			while($myRow=safe_fetch($r)) {
				if($myRow->LastUpdated>$this->data['meta']['lastUpdate']) $this->data['meta']['lastUpdate']=$myRow->LastUpdated;
				if($myRow->OppLastUpdated>$this->data['meta']['lastUpdate']) $this->data['meta']['lastUpdate']=$myRow->OppLastUpdated;
				if(!isset($this->data['sections'][$myRow->Event]['meta'])) {
					$tmp=GetMaxScores($myRow->Event, 0, 1, $this->tournament);

					$this->data['sections'][$myRow->Event]['meta']=array(
						'phase' => get_text('Phase'),
						'eventName' => get_text($myRow->EventDescr,'','',true),
						'firstPhase' => $myRow->EvFinalFirstPhase,
						'winnerFinalRank' => $myRow->EvWinnerFinalRank,
						'printHead' => get_text($myRow->EvFinalPrintHead,'','',true),
                        'checkGolds'=>$myRow->EvCheckGolds,
                        'checkXnines'=>$myRow->EvCheckXNines,
                        'golds'=>$myRow->EvGolds,
                        'xnines'=>$myRow->EvXNine,
                        'goldChars'=>$myRow->EvGoldsChars,
                        'xninesChars'=>$myRow->EvXNineChars,
						'maxTeamPerson'=>$myRow->EvMaxTeamPerson,
                        'parent'=>$myRow->EvCodeParent,
						'matchMode'=>$myRow->EvMatchMode,
						'order'=>$myRow->EvProgr,
						'shootOffSolved'=>$myRow->EvShootOff,
						'finEnds' => $myRow->EvFinEnds,
						'finArrows' => $myRow->EvFinArrows,
						'finSO' => $myRow->EvFinSO,
						'finMaxScore' => $myRow->EvFinArrows*10,
						'elimEnds' => $myRow->EvElimEnds,
						'elimArrows' => $myRow->EvElimArrows,
						'elimSO' => $myRow->EvElimSO,
						'elimMaxScore' => $myRow->EvElimArrows*10,
						'targetType' => $myRow->TarDescr,
						'targetTypeId' => $myRow->TarId,
						'targetTypeValues' => GetTarget($this->tournament, $myRow->TarDescr, true),
						'targetSize' => $myRow->TargetSize,
						'distance' => $myRow->Distance,
						'version' => $myRow->DocVersion,
						'versionDate' => $myRow->DocVersionDate,
						'versionNotes' => $myRow->DocNotes,
						'maxPoint' => $tmp['MaxPoint'],
						'minPoint' => $tmp['MinPoint'],
						'noRealPhase' => $myRow->Phase>=$myRow->EvFinalFirstPhase ? $myRow->NoRealPhase : 0,
						'numSaved' => ($num=SavedInPhase($myRow->EvFinalFirstPhase)) ? $num : 2*$myRow->EvFinalFirstPhase - $myRow->EvNumQualified,
						'mixedTeam' => $myRow->EvMixedTeam,
						'OrisCode' => 'C75C',
						);
                    $this->data['sections'][$myRow->Event]['meta']['endName'] = ($myRow->EvMatchMode==0 ? get_text('ScorecardLabelEnd','Tournament') : get_text('ScorecardLabelSet','Tournament'));
					$this->data['sections'][$myRow->Event]['meta']['phaseNames']=array(
						$myRow->EvFinalFirstPhase => get_text($myRow->EvFinalFirstPhase . "_Phase")
					);

					$this->data['sections'][$myRow->Event]['phases']=array();
					if(!empty($this->opts['records'])) {
						$this->data['sections'][$myRow->Event]['records'] = $this->getRecords($myRow->Event, true, true);
					}
				}

				if(!isset($this->data['sections'][$myRow->Event]['phases'][$myRow->Phase])) {
					$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['meta']=array(
						'phaseName' => get_text(namePhase($myRow->EvFinalFirstPhase, $myRow->Phase) . "_Phase"),
						'matchName' => get_text('MatchName-'.namePhase($myRow->EvFinalFirstPhase, $myRow->Phase), 'Tournament'),
						'FinElimChooser' => $myRow->FinElimChooser,
						);
					$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['items']=array();
					// $this->data['sections'][$myRow->Event]['meta']['phaseNames'][namePhase($myRow->EvFinalFirstPhase, $myRow->Phase)]=$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['meta']['phaseName'];
					$this->data['sections'][$myRow->Event]['meta']['phaseNames'][$myRow->Phase]=$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['meta']['phaseName'];
				}

				if(empty($myRow->OdfMatchName)) {
					$myRow->OdfMatchName='';
					$myRow->OdfPreviousMatch='';
					$myRow->PreviousMatchTime='';
					$myRow->OppOdfMatchName='';
					$myRow->OppOdfPreviousMatch='';
					$myRow->OppPreviousMatchTime='';
				}
				$item=array(
					// qui ci sono le descrizioni dei campi
					'lineJudge' => $myRow->LineJudge,
					'lineGivName' => $myRow->LineGivName,
					'lineFamName' => $myRow->LineFamName,
					'lineCode' => $myRow->LineCode,
                    'lineCodeLocal' => $myRow->LineCodeLocal,
					'lineOdfCode' => $myRow->LineOdfCode,
					'lineCountry' => $myRow->LineCountry,
					'lineGender' => $myRow->LineGender,
					'targetJudge' => $myRow->TargetJudge,
					'targetGivName' => $myRow->TargetGivName,
					'targetFamName' => $myRow->TargetFamName,
					'targetCode' => $myRow->TargetCode,
                    'targetCodeLocal' => $myRow->TargetCodeLocal,
					'targetOdfCode' => $myRow->TargetOdfCode,
					'targetCountry' => $myRow->TargetCountry,
					'targetGender' => $myRow->TargetGender,
					'liveFlag' => $myRow->LiveFlag,
					'scheduledDate' => $myRow->ScheduledDate,
					'scheduledTime' => $myRow->ScheduledTime,
					'scheduledKey' => $myRow->ScheduledKey,
					'lastUpdated' => $myRow->LastUpdated,
					'matchNo' => $myRow->MatchNo,
					'isValidMatch'=> ($myRow->GridPosition + $myRow->OppGridPosition),
                    'localBib' => str_pad(rtrim($myRow->OdfCode, '-'),15-strlen($myRow->CountryCode??''),'-',STR_PAD_RIGHT).($myRow->CountryCode??'').str_pad(($myRow->SubTeam??0)+1,2,'0',STR_PAD_LEFT),
					'odfMatchName' => $myRow->OdfMatchName ? $myRow->OdfMatchName : '',
                    'odfUnitcode' => $myRow->OdfUnitCode ? $myRow->EvOdfCode.$myRow->OdfUnitCode : '',
					'odfPath' => $myRow->OdfPreviousMatch && intval($myRow->OdfPreviousMatch)==0 ? $myRow->OdfPreviousMatch : get_text(($myRow->MatchNo==2 or $myRow->MatchNo==3) ? 'LoserMatchName' : 'WinnerMatchName', 'ODF', $myRow->OdfPreviousMatch ? $myRow->OdfPreviousMatch : $myRow->PreviousMatchTime),
					'target' => ltrim($myRow->Target ?? '','0'),
					'coach' => $myRow->Coach,
					'coachGivName' => $myRow->CoachGivName,
					'coachFamName' => $myRow->CoachFamName,
					'coachCode' => $myRow->CoachCode,
					'coachCountry' => $myRow->CoachCountry,
					'coachGender' => $myRow->CoachGender,
					'countryCode' => $myRow->CountryCode,
					'countryName' => $myRow->CountryName,
					'contAssoc' => $myRow->CaCode,
					'tvFamilyName' => $myRow->CountryName,
					'tvInitials' => '',
					'memberAssoc' => $myRow->MaCode,
					'qualRank' => $myRow->ShowRankQual ? $myRow->QualRank : $myRow->IrmTextQual,
					'qualScore'=> $myRow->QualScore,
					'qualNotes'=> $myRow->QualNotes,
					'finRank' => $myRow->FinRank,
					'showRank' => $myRow->ShowRankFin,
					'finIrmText' => $myRow->IrmTextFin,
					'irm' => $myRow->Irm,
					'irmText' => $myRow->IrmText,
					'winner' => $myRow->Winner,
					'score'=> $myRow->Score,
                    'golds'=> $myRow->Golds,
                    'xnines'=> $myRow->XNines,
					'setScore'=> $myRow->SetScore,
				 	'setPoints'=> $myRow->SetPoints,
				 	'setPointsByEnd'=> $myRow->SetPointsByEnd,
				 	'notes'=> $myRow->Notes,
				 	'arrowstring'=> $myRow->Arrowstring,
					'tie'=> $myRow->Tie,
				 	'tiebreak'=> trim($myRow->TieBreak),
                    'closest' => $myRow->TieClosest,
				 	'tiebreakDecoded'=> $myRow->TieDecoded,
					'arrowpositionAvailable'=>($myRow->ArrowPosition != ''),
                    'shootingarchersAvailable'=>($myRow->ShootingArchers != ''),
					'status'=>$myRow->Status,
					'scoreConfirmed'=>$myRow->Confirmed,
					'shootFirst'=>$myRow->ShootFirst,
					'record' => $this->ManageBitRecord($myRow->RecBitLevel, $myRow->CaCode, $myRow->MaCode, $myRow->EvIsPara),
				 	'position'=> $myRow->QualRank ? $myRow->QualRank : (useGrPostion2($myRow->EvFinalFirstPhase, $myRow->Phase) ? ($myRow->Position2 ? $myRow->Position2:'') : $myRow->Position),
                    'saved'=> ($myRow->Position>0 and $myRow->Position<=SavedInPhase($myRow->EvFinalFirstPhase)),
				 	'teamId'=> $myRow->Team,
				 	'subTeam'=> $myRow->SubTeam,
//
					'oppLastUpdated' => $myRow->OppLastUpdated,
					'oppMatchNo' => $myRow->OppMatchNo,
					'oppLocalBib' => str_pad(rtrim($myRow->OdfCode, '-'),15-strlen($myRow->OppCountryCode??''),'-',STR_PAD_RIGHT).($myRow->OppCountryCode??'').str_pad(($myRow->OppSubTeam??0)+1,2,'0',STR_PAD_LEFT),
					'oppOdfMatchName' => $myRow->OppOdfMatchName,
					'oppOdfPath' => $myRow->OppOdfPreviousMatch && intval($myRow->OppOdfPreviousMatch)==0 ? $myRow->OppOdfPreviousMatch : get_text(($myRow->MatchNo==2 or $myRow->MatchNo==3) ? 'LoserMatchName' : 'WinnerMatchName', 'ODF', $myRow->OppOdfPreviousMatch ? $myRow->OppOdfPreviousMatch : $myRow->OppPreviousMatchTime),
					'oppTarget' => ltrim($myRow->OppTarget ?? '', '0'),
					'oppCoach' => $myRow->OppCoach,
					'oppCoachGivName' => $myRow->OppCoachGivName,
					'oppCoachFamName' => $myRow->OppCoachFamName,
					'oppCoachCode' => $myRow->OppCoachCode,
					'oppCoachCountry' => $myRow->OppCoachCountry,
					'oppCoachGender' => $myRow->OppCoachGender,
					'oppCountryCode' => $myRow->OppCountryCode,
					'oppCountryName' => $myRow->OppCountryName,
					'oppContAssoc' => $myRow->OppCaCode,
                    'oppTvFamilyName' => $myRow->OppCountryName,
                    'oppTvInitials' => '',
					'oppMemberAssoc' => $myRow->OppMaCode,
					'oppQualRank' => $myRow->OppShowRankQual ? $myRow->OppQualRank : $myRow->OppIrmTextQual,
					'oppQualScore'=> $myRow->OppQualScore,
					'oppQualNotes'=> $myRow->OppQualNotes,
					'oppFinRank' => $myRow->OppFinRank,
					'oppShowRank' => $myRow->OppShowRankFin,
					'oppFinIrmText' => $myRow->OppIrmTextFin,
					'oppIrm' => $myRow->OppIrm,
					'oppIrmText' => $myRow->OppIrmText,
					'oppWinner' => $myRow->OppWinner,
					'oppScore'=> $myRow->OppScore,
                    'oppGolds'=> $myRow->OppGolds,
                    'oppXnines'=> $myRow->OppXNines,
					'oppSetScore'=> $myRow->OppSetScore,
				 	'oppSetPoints'=> $myRow->OppSetPoints,
				 	'oppSetPointsByEnd'=> $myRow->OppSetPointsByEnd,
				 	'oppNotes'=> $myRow->OppNotes,
				 	'oppArrowstring'=> $myRow->OppArrowstring,
					'oppTie'=> $myRow->OppTie,
				 	'oppTiebreak'=> trim($myRow->OppTieBreak),
                    'oppClosest' => $myRow->OppTieClosest,
				 	'oppTiebreakDecoded'=> $myRow->OppTieDecoded,
                    'oppArrowpositionAvailable'=>($myRow->OppArrowPosition != ''),
                    'oppShootingarchersAvailable'=>($myRow->OppShootingArchers != ''),
					'oppStatus'=>$myRow->OppStatus,
					'oppScoreConfirmed'=>$myRow->OppConfirmed,
					'oppShootFirst'=>$myRow->OppShootFirst,
					'oppRecord' => $this->ManageBitRecord($myRow->OppRecBitLevel, $myRow->OppCaCode, $myRow->OppMaCode, $myRow->EvIsPara),
				 	'oppPosition'=> $myRow->OppQualRank ? $myRow->OppQualRank : (useGrPostion2($myRow->EvFinalFirstPhase, $myRow->Phase) ? ($myRow->OppPosition2 ? $myRow->OppPosition2:'') : $myRow->OppPosition),
                    'oppSaved'=> ($myRow->OppPosition>0 and $myRow->OppPosition<=SavedInPhase($myRow->EvFinalFirstPhase)),
                    'oppTeamId'=> $myRow->OppTeam,
				 	'oppSubTeam'=> $myRow->OppSubTeam,
					);

                if(!empty($this->opts['extended'])) {
                    $item['arrowPosition']= ($myRow->ArrowPosition == '' ? array() : json_decode($myRow->ArrowPosition, true));
                    $item['tiePosition']= ($myRow->TiePosition != '' and $tmp=json_decode($myRow->TiePosition, true)) ? $tmp : array();
                    $item['shootingArchers']= ($myRow->ShootingArchers == '' ? array() : json_decode($myRow->ShootingArchers, true));
                    $item['oppArrowPosition']= ($myRow->OppArrowPosition == '' ? array() : json_decode($myRow->OppArrowPosition, true));
                    $item['oppTiePosition']= ($myRow->OppTiePosition != '' and $tmp=json_decode($myRow->OppTiePosition, true)) ? $tmp : array();
                    $item['oppShootingArchers']= ($myRow->OppShootingArchers == '' ? array() : json_decode($myRow->OppShootingArchers, true));
                }

                $this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['items'][] = $item;
			}
		}

		function getData() {
			if(!empty($this->opts['enid']) and !$this->EnIdFound) return;
			return parent::getData();
		}
	}