<?php
	require_once('Common/Fun_Phases.inc.php');
	require_once('Common/Lib/ArrTargets.inc.php');
	require_once('Common/Lib/CommonLib.php');

/**
 * Obj_Rank_FinalInd
 *
 * Implementa l'algoritmo di default per il recupero delle griglie finali individuali.
 * E' in sola lettura
 *
 * La tabella in cui scrive è Individuals e popola la RankFinal "a pezzi". Solo alla fine della gara
 * avremo tutta la colonna valorizzata.
 *
 * A seconda della fase che sto trattando avrò porzioni di colonna da gestire differenti e calcoli differenti.
 *
 * Per questa classe $opts ha la seguente forma:
 *
 * array(
 * 		events => array(<ev_1>@<calcPhase_1>,<ev_2>@<calcPhase_2>,...,<ev_n>@<calcPhase_n>)
 * 		tournament => #
 * )
 *
 * con:
 * 	 events: l'array con le coppie evento@fase di cui voglio la griglia.
 *  tournament: Se impostato è l'id del torneo su cui operare altrimenti prende quello in sessione.
 *
 * Estende Obj_Rank
 */
	class Obj_Rank_GridInd extends Obj_Rank
	{
	/**
	 * safeFilter()
	 * Protegge con gli apici gli elementi di $this->opts['events']
	 *
	 * @return mixed: false se non c'è filtro oppure la stringa da inserire nella where delle query
	 */
		protected function safeFilter() {
			$ret=array();
			if (!empty($this->opts['events'])) {
				if(!is_array($this->opts['events'])) $this->opts['events']=array($this->opts['events']);

				$f=array();

				foreach ($this->opts['events'] as $e) {
				    $event = $e;
				    $phase = null;
				    if(strpos($e,'@')!==false) {
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

		/**
		 * @param array $opts an array of options that can trigger the result recordset:
		 * <li><b>events:</b> an array of events to filter. The single event can have the following forms:
		 *     <ul>
		 * 			<li><i>event:</i> will get all the phases of this event</li>
		 * 			<li><i>event@phase:</i> will get this event at this phase</li>
		 * 			<li><i>@phase:</i> will get all events at this phase</li>
		 *     </ul></li>
		 * <li><b>schedule:</b> will return all events and phases related to that schedule</li>
		 * <li><b>enid:</b> returns all the events and phases of that archer</li>
		 * <li><b>coid:</b> returns all the matches in all events in all phases of archers from that country</li>
		 * <li><b>matchno:</b> returns that match in all events (must be the even one)</li>
		 * <li><b>liveFlag:</b> returns the matches that are flagged as live</li>
		 * <li><b>extended:</b> returns the matches extended info for spotting view</li>
		 */
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

			$ExtraFilter=array('if(EvElimType=4, EnId>0 or OppEnId>0 or GrPhase<=4, true)');
			if(!empty($this->opts['enid'])) {
				$ExtraFilter[] = "(EnId=" . intval($this->opts['enid']) . " or OppEnId=" . intval($this->opts['enid']) . ")";
			}
			if(!empty($this->opts['coid'])) {
				$ExtraFilter[] = "(CountryId=" . intval($this->opts['coid']) . " or OppCountryId=" . intval($this->opts['coid']) . ") ";
			}
			if(isset($this->opts['matchno'])) {
				$ExtraFilter[] = "(MatchNo=" . intval($this->opts['matchno']) . ' or OppMatchNo =' . intval($this->opts['matchno']) . ')';
			}
			if(isset($this->opts['matchnoArray'])) {
				$ExtraFilter[] = "MatchNo in (" . implode(',', $this->opts['matchnoArray']) . ') ';
			}
			if(isset($this->opts['liveFlag'])) {
				$ExtraFilter[] = "LiveFlag=1";
			}
            if(isset($this->opts['noElim'])) {
                $ExtraFilter[] = "Phase<=FullPhase";
            }
			if($ExtraFilter) {
				$ExtraFilter = 'WHERE ' . implode(' AND ', $ExtraFilter);
			} else {
				$ExtraFilter = '';
			}

		/*
		 *  prima passata per costruire la struttura del vettore.
		 *  Tiro fuori le qualifiche, le posizioni finali e le eliminatorie (se ci sono)
		 */
			$q="SELECT f1.*, f2.*, coalesce(OdfTrOdfCode,'') as OdfUnitCode, coalesce(ParentQualified,0) as ParentPhase,
       				coalesce(JudgeLine,'') as LineJudge, coalesce(JudgeLineCode,'') as LineCode,  coalesce(JudgeLineCodeLocal,'') as LineCodeLocal, coalesce(JudgeLineFamName,'') as LineFamName, coalesce(JudgeLineGivName,'') as LineGivName, coalesce(JudgeLineCountry,'') as LineCountry, coalesce(JudgeLineGender,'') as LineGender, coalesce(OdfLineCode,'') as LineOdfCode,
       				coalesce(JudgeTarget,'') as TargetJudge, coalesce(JudgeTargetCode,'') as TargetCode, coalesce(JudgeTargetCodeLocal,'') as TargetCodeLocal, coalesce(JudgeTargetFamName,'') as TargetFamName, coalesce(JudgeTargetGivName,'') as TargetGivName, coalesce(JudgeTargetCountry,'') as TargetCountry, coalesce(JudgeTargetGender,'') as TargetGender, coalesce(OdfTargetCode,'') as TargetOdfCode,
       				coalesce(Coach1,'') as Coach, coalesce(Coach1Code,'') as CoachCode, coalesce(Coach1FamName,'') as CoachFamName, coalesce(Coach1GivName,'') as CoachGivName, coalesce(Coach1Country,'') as CoachCountry, coalesce(Coach1Gender,'') as CoachGender,
       				coalesce(Coach2,'') as OppCoach, coalesce(Coach2Code,'') as OppCoachCode, coalesce(Coach2FamName,'') as OppCoachFamName, coalesce(Coach2GivName,'') as OppCoachGivName, coalesce(Coach2Country,'') as OppCoachCountry, coalesce(Coach2Gender,'') as OppCoachGender,
					ifnull(concat(DV2.DvMajVersion, '.', DV2.DvMinVersion) ,concat(DV1.DvMajVersion, '.', DV1.DvMinVersion)) as DocVersion,
					date_format(ifnull(DV2.DvPrintDateTime, DV1.DvPrintDateTime), '%e %b %Y %H:%i UTC') as DocVersionDate,
					ifnull(DV2.DvNotes, DV1.DvNotes) as DocNotes FROM "
				. "(select "
					. " fs1.FsOdfMatchName OdfMatchName,"
					. " ifnull(fs2.FsOdfMatchName, concat('RR #',if(EvFinalFirstPhase in (12,24,48), GrPosition2, GrPosition))) as OdfPreviousMatch,"
					.(empty($this->opts['extended']) ? '' : " RevLanguage1 AS Review1, RevLanguage2 AS Review2, UNIX_TIMESTAMP(IFNULL(RevDateTime,0)) As ReviewUpdate, ")
				    . " FinArrowPosition as ArrowPosition, FinTiePosition TiePosition,"
                    . " FinEvent Event,"
					. " GrPhase,"
					. " EvProgr,"
					. " EvEventName AS EventDescr,"
					. " EvMatchMode,"
					. " EvWinnerFinalRank, "
					. " EvFinalFirstPhase=EvNumQualified as NoRealPhase,"
					. " EvFinalFirstPhase, "
					. " EvFinalPrintHead, "
					. " EvCheckGolds, "
					. " EvCheckXNines, "
					. " EvGolds, "
					. " EvXNine, "
					. " EvGoldsChars, "
					. " EvXNineChars, "
					. " GrPhase Phase,"
					. " @BitPhase:=if(GrPhase=0, 1, pow(2, ceil(log2(GrPhase))+1)),"
					. " @BitPhase & EvMatchArrowsNo!=0 as FinElimChooser,"
					. " greatest(PhId, PhLevel) as FullPhase,"
					. " EvShootOff,"
                    . " EvCodeParent,"
                    . " EvElimType,"
                    . " EvNumQualified,"
                    . " EvOdfCode, "
					. " IF(EvFinalFirstPhase=48, GrPosition2, if(GrPosition>EvNumQualified, 0, GrPosition)) Position,"
					. " concat(fs1.FSScheduledDate,' ',fs1.FSScheduledTime) AS ScheduledKey, "
					. " concat(fs2.FSScheduledDate,' ',fs2.FSScheduledTime) AS PreviousMatchTime, "
					. " DATE_FORMAT(fs1.FSScheduledDate,'" . get_text('DateFmtDB') . "') as ScheduledDate,"
					. " DATE_FORMAT(fs1.FSScheduledTime,'" . get_text('TimeFmt') . "') AS ScheduledTime, "
					. " FinTournament Tournament,"
					. " FinDateTime LastUpdated,"
					. " FinMatchNo MatchNo,"
					. " FinCoach CoachId,"
					. " a.EnCode Bib,"
					. " ifnull(EdExtra,a.EnCode) LocalBib,"
					. " a.EnId EnId, a.EnNameOrder NameOrder, a.EnSex Gender, a.EnDob BirthDate, "
					. " if(@BitPhase & EvMatchMultipleMatches!=0 or @BitPhase & EvFinalAthTarget!=0, fs1.FsLetter, fs1.FsTarget) as Target,"
					. " TarId, TarDescr, EvDistance as Distance, EvTargetSize as TargetSize, "
					. " concat(upper(a.EnFirstName), ' ', a.EnName) Athlete,"
					. " a.EnFirstName FamilyName,"
					. " upper(a.EnFirstName) FamilyNameUpper,"
					. " a.EnName GivenName,"
					. " a.EnTvFamilyName TvFamilyName,"
					. " a.EnTvGivenName TvGivenName,"
					. " a.EnTvInitials TvInitials,"
					. " CoId CountryId,"
					. " CoCode CountryCode,"
					. " CoMaCode MaCode,"
					. " CoCaCode CaCode,"
					. " left(CoName, 20) ShortCountry,"
					. " CoName CountryName,"
					. " CoIocCode CountryIocCode,"
					. " IndRank QualRank,"
					. " IndRankFinal FinRank,"
					. " QuScore QualScore,"
					. " IndNotes QualNotes,"
					. "	EvFinEnds, EvFinArrows, EvFinSO, EvElimEnds, EvElimArrows, EvElimSO, "
					. " FinIrmType Irm,"
					. " i1.IrmType IrmText,"
					. " IndIrmType IrmQual,"
					. " i2.IrmType IrmTextQual,"
					. " i2.IrmShowRank ShowRankQual,"
					. " IndIrmTypeFinal IrmFin,"
					. " i3.IrmType IrmTextFin,"
					. " i3.IrmShowRank ShowRankFin,"
					. " FinWinLose Winner,"
					. " FinScore Score,"
					. " FinGolds Golds,"
					. " FinXNines XNines,"
					. " FinSetScore SetScore,"
					. " FinSetPoints SetPoints,"
					. " FinSetPointsByEnd SetPointsByEnd,"
					. " FinTie AS Tie,"
					. " FinArrowstring ArrowString,"
					. " FinTiebreak TieBreak,"
                    . " FinTbClosest TieClosest,"
                    . " FinTbDecoded TieDecoded,"
					. " FinStatus Status, "
					. " FinConfirmed Confirmed, "
					. " FinRecordBitmap  as RecBitLevel, EvIsPara, "
                    . " fs1.FsLJudge as jLine, fs1.FsTJudge as jTarget, "
					. " FinLive LiveFlag, FinNotes Notes, FinShootFirst as ShootFirst, if(EvFinalFirstPhase%12=0, GrPosition2, GrPosition) as GridPosition "
					. "FROM Finals "
					. "INNER JOIN Grids ON FinMatchNo=GrMatchNo "
					. "INNER JOIN Events ON FinEvent=EvCode AND FinTournament=EvTournament AND EvTeamEvent=0 "
					. "inner join Phases on PhId=EvFinalFirstPhase and (PhIndTeam & 1)=1 "
					. "INNER JOIN Targets ON EvFinalTargetType=TarId "
					. "INNER JOIN IrmTypes i1 ON i1.IrmId=FinIrmType "
					. "LEFT JOIN Individuals ON FinAthlete=IndId AND FinEvent=IndEvent AND FinTournament=IndTournament "
					. "left JOIN IrmTypes i2 ON i2.IrmId=IndIrmType "
					. "left JOIN IrmTypes i3 ON i3.IrmId=IndIrmTypeFinal "
					. "LEFT JOIN Entries a ON FinAthlete=a.EnId AND FinTournament=a.EnTournament "
					. "LEFT JOIN ExtraData ON EdId=a.EnId AND EdType='Z' "
					. "LEFT JOIN Qualifications ON QuId=a.EnId "
					. "LEFT JOIN Countries ON CoId=
                        case EvTeamCreationMode 
                            when 0 then a.EnCountry
                            when 1 then a.EnCountry2
                            when 2 then a.EnCountry3
                            else a.EnCountry
                        end
                        AND a.EnTournament=CoTournament "
					. "LEFT JOIN FinSchedule fs1 ON fs1.FSEvent=FinEvent AND fs1.FSMatchNo=FinMatchNo AND fs1.FSTournament=FinTournament AND fs1.FSTeamEvent='0' "
					. "LEFT JOIN FinSchedule fs2 ON fs2.FSEvent=FinEvent AND fs2.FSMatchNo=case FinMatchNo when 0 then 4 when 1 then 6 when 2 then 4 when 3 then 6 else FinMatchNo*2 end AND fs2.FSTournament=FinTournament AND fs2.FSTeamEvent='0' "
					. (empty($this->opts['extended']) ? '' : "LEFT JOIN Reviews ON FinEvent=RevEvent AND FinMatchNo=RevMatchNo AND FinTournament=RevTournament AND RevTeamEvent=0 ")
					. "WHERE FinMatchNo%2=0 "
					. " AND FinTournament = " . $this->tournament . " " . $filter
					. ") f1 "
				. "INNER JOIN (select "
					.(empty($this->opts['extended']) ? '' : " RevLanguage1 AS OppReview1, RevLanguage2 AS OppReview2, UNIX_TIMESTAMP(IFNULL(RevDateTime,0)) As OppReviewUpdate, ")
					. " fs1.FsOdfMatchName as OppOdfMatchName,"
					. " ifnull(fs2.FsOdfMatchName, concat('RR #',if(EvFinalFirstPhase in (12,24,48), GrPosition2, GrPosition))) as OppOdfPreviousMatch,"
                    . " FinArrowPosition OppArrowPosition, FinTiePosition OppTiePosition,"
                    . " FinEvent OppEvent,"
					. " FinTournament OppTournament,"
					. " FinDateTime OppLastUpdated,"
					. " FinMatchNo OppMatchNo,"
					. " FinCoach OppCoachId,"
					. " a.EnCode OppBib,"
					. " ifnull(EdExtra,a.EnCode) OppLocalBib,"
					. " a.EnId OppEnId, a.EnNameOrder OppNameOrder, a.EnSex as OppGender, a.EnDob OppBirthDate,"
					. " @BitPhase:=if(GrPhase=0, 1, pow(2, ceil(log2(GrPhase))+1)),"
					. " if(@BitPhase & EvMatchMultipleMatches!=0 or @BitPhase & EvFinalAthTarget!=0, fs1.FsLetter, fs1.FsTarget) as OppTarget,"
					. " concat(fs2.FSScheduledDate,' ',fs2.FSScheduledTime) AS OppPreviousMatchTime, "
					. " concat(upper(a.EnFirstName), ' ', a.EnName) OppAthlete,"
					. " a.EnFirstName OppFamilyName,"
					. " upper(a.EnFirstName) OppFamilyNameUpper,"
					. " IF(EvFinalFirstPhase=48, GrPosition2, if(GrPosition>EvNumQualified, 0, GrPosition)) OppPosition,"
					. " a.EnName OppGivenName,"
                    . " a.EnTvFamilyName OppTvFamilyName,"
                    . " a.EnTvGivenName OppTvGivenName,"
                    . " a.EnTvInitials OppTvInitials,"
					. " CoId OppCountryId,"
					. " CoCode OppCountryCode,"
					. " CoMaCode OppMaCode,"
					. " CoCaCode OppCaCode,"
					. " left(CoName, 20) OppShortCountry,"
					. " CoName OppCountryName,"
					. " CoIocCode OppCountryIocCode,"
					. " IndRank OppQualRank,"
					. " IndRankFinal OppFinRank,"
					. " QuScore OppQualScore,"
					. " IndNotes OppQualNotes,"
					. " FinIrmType OppIrm,"
					. " i1.IrmType OppIrmText,"
					. " IndIrmType OppIrmQual,"
					. " i2.IrmType OppIrmTextQual,"
					. " i2.IrmShowRank OppShowRankQual,"
					. " IndIrmTypeFinal OppIrmFin,"
					. " i3.IrmType OppIrmTextFin,"
					. " i3.IrmShowRank OppShowRankFin,"
					. " FinWinLose OppWinner,"
					. " FinScore OppScore,"
                    . " FinGolds OppGolds,"
                    . " FinXNines OppXNines,"
					. " FinSetScore OppSetScore,"
					. " FinSetPoints OppSetPoints,"
					. " FinSetPointsByEnd OppSetPointsByEnd,"
					. " FinTie AS OppTie,"
					. " FinArrowstring OppArrowString,"
					. " FinTiebreak OppTieBreak, "
                    . " FinTbClosest OppTieClosest, "
                    . " FinTbDecoded OppTieDecoded, "
					. " FinConfirmed OppConfirmed, "
					. " FinRecordBitmap  as OppRecBitLevel, "
					. " FinStatus OppStatus, FinNotes OppNotes, FinShootFirst as OppShootFirst, if(EvFinalFirstPhase%12=0, GrPosition2, GrPosition) as OppGridPosition  "
					. "FROM "
					. " Finals "
					. "INNER JOIN Grids ON FinMatchNo=GrMatchNo "
					. "INNER JOIN Events ON FinEvent=EvCode AND FinTournament=EvTournament AND EvTeamEvent=0 "
					. "INNER JOIN IrmTypes i1 ON i1.IrmId=FinIrmType "
					. "LEFT JOIN Individuals ON FinAthlete=IndId AND FinEvent=IndEvent AND FinTournament=IndTournament "
					. "left JOIN IrmTypes i2 ON i2.IrmId=IndIrmType "
					. "left JOIN IrmTypes i3 ON i3.IrmId=IndIrmTypeFinal "
					. "LEFT JOIN Entries a ON FinAthlete=a.EnId AND FinTournament=a.EnTournament "
					. "LEFT JOIN ExtraData ON EdId=a.EnId AND EdType='Z' "
					. "LEFT JOIN Qualifications ON QuId=a.EnId "
					. "LEFT JOIN Countries ON CoId=
                        case EvTeamCreationMode 
                            when 0 then a.EnCountry
                            when 1 then a.EnCountry2
                            when 2 then a.EnCountry3
                            else a.EnCountry
                        end
                        AND a.EnTournament=CoTournament "
					. "LEFT JOIN FinSchedule fs1 ON fs1.FSEvent=FinEvent AND fs1.FSMatchNo=FinMatchNo AND fs1.FSTournament=FinTournament AND fs1.FSTeamEvent='0' "
					. "LEFT JOIN FinSchedule fs2 ON fs2.FSEvent=FinEvent AND fs2.FSMatchNo=case FinMatchNo when 0 then 4 when 1 then 6 when 2 then 4 when 3 then 6 else FinMatchNo*2 end AND fs2.FSTournament=FinTournament AND fs2.FSTeamEvent='0' "
					. (empty($this->opts['extended']) ? '' : "LEFT JOIN Reviews ON FinEvent=RevEvent AND FinMatchNo=RevMatchNo AND FinTournament=RevTournament AND RevTeamEvent=0 ")
					. "WHERE FinMatchNo%2=1 "
					. " AND FinTournament = " . $this->tournament . " " . $filter
					. ") f2 on Tournament=OppTournament and Event=OppEvent and MatchNo=OppMatchNo-1
					LEFT JOIN DocumentVersions DV1 on Tournament=DV1.DvTournament AND DV1.DvFile = 'B-IND' and DV1.DvEvent=''
					LEFT JOIN DocumentVersions DV2 on Tournament=DV2.DvTournament AND DV2.DvFile = 'B-IND' and DV2.DvEvent=Event 
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
                left join (select EvCodeParent as ParentCode, ceil(EvNumQualified/2) as ParentQualified from Events where EvTournament={$this->tournament} and EvTeamEvent=0 and EvCodeParentWinnerBranch=1) subEvent on ParentCode=Event
                
                $ExtraFilter
                ORDER BY ".($OrderByTarget ? 'Target, ' : '')."EvProgr ASC, Event, Phase DESC, MatchNo ASC ";

			return $q;
		}

		public function read() {
			$PoolMatchesShort=getPoolMatchesShort();
			$PoolMatchesShortWA=getPoolMatchesShortWA();
			$PoolMatches=getPoolMatchesWinners();
			$PoolMatchesWA=getPoolMatchesWinnersWA();
			$PoolMatchesPhases=getPoolMatchesPhases();
			$PoolMatchesPhasesWA=getPoolMatchesPhasesWA();

			$r=safe_r_sql($this->getQuery());

			$this->data['meta']['title']=get_text('BracketsInd');
			$this->data['meta']['saved']=get_text('Seeded16th');
			$this->data['meta']['notAwarded']=get_text('NotAwarded','ODF');
			$this->data['meta']['lastUpdate']='0000-00-00 00:00:00';
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
                'fullName' => get_text('Athlete'),
				'familyname' => get_text('FamilyName', 'Tournament'),
				'givenname' => get_text('Name', 'Tournament'),
				'gender' => get_text('Sex', 'Tournament'),
				'countryId' => '',
				'countryCode' => '',
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
                'oppFullName' => get_text('Athlete'),
				'oppFamilyname' => get_text('FamilyName', 'Tournament'),
				'oppGivenname' => get_text('Name', 'Tournament'),
				'oppGender' => get_text('Sex', 'Tournament'),
				'oppCountryId' => '',
				'oppCountryCode' => '',
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
			$this->data['sections']=array();

			while($myRow=safe_fetch($r)) {
				if($myRow->LastUpdated>$this->data['meta']['lastUpdate']) $this->data['meta']['lastUpdate']=$myRow->LastUpdated;
				if($myRow->OppLastUpdated>$this->data['meta']['lastUpdate']) $this->data['meta']['lastUpdate']=$myRow->OppLastUpdated;
				if(!isset($this->data['sections'][$myRow->Event])) {

					$tmp=GetMaxScores($myRow->Event, 0, 0, $this->tournament);

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
						'elimType' => $myRow->EvElimType,
						'targetType' => $myRow->TarDescr,
						'targetTypeId' => $myRow->TarId,
						'targetTypeValues' => GetTarget($this->tournament, $myRow->TarDescr, true),
						'targetSize' => $myRow->TargetSize,
						'distance' => $myRow->Distance,
						'version' => $myRow->DocVersion,
						'versionDate' => $myRow->DocVersionDate,
						'versionNotes' => $myRow->DocNotes,
						'records' => array(),
						'maxPoint' => $tmp['MaxPoint'],
						'minPoint' => $tmp['MinPoint'],
						'noRealPhase' => $myRow->Phase>=$myRow->EvFinalFirstPhase ? $myRow->NoRealPhase : 0,
						'numSaved' => ($num=SavedInPhase($myRow->EvFinalFirstPhase)) ? $num : 2*$myRow->EvFinalFirstPhase - $myRow->EvNumQualified,
                        'winnerChildStartPhase' => $myRow->ParentPhase,
						);
                    $this->data['sections'][$myRow->Event]['meta']['endName'] = ($myRow->EvMatchMode==0 ? get_text('ScorecardLabelEnd','Tournament') : get_text('ScorecardLabelSet','Tournament'));
					$this->data['sections'][$myRow->Event]['meta']['phaseNames']=array(
						$myRow->EvFinalFirstPhase => get_text($myRow->EvFinalFirstPhase . "_Phase")
					);
					if(!empty($this->opts['records'])) {
						$this->data['sections'][$myRow->Event]['records'] = $this->getRecords($myRow->Event, false, true);
					}
					$this->data['sections'][$myRow->Event]['phases']=array();
				}


				if(!isset($this->data['sections'][$myRow->Event]['phases'][$myRow->Phase])) {
					$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]=array('meta' => array(),'items' => array());
					$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['meta']['phaseName'] = get_text(namePhase($myRow->EvFinalFirstPhase, $myRow->Phase) . "_Phase");
					$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['meta']['matchName'] = get_text('MatchName-'.namePhase($myRow->EvFinalFirstPhase, $myRow->Phase), 'Tournament');
					if($myRow->EvElimType==3 and isset($PoolMatchesShort[$myRow->MatchNo])) {
						$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['meta']['phaseName'] = $PoolMatchesPhases[$myRow->Phase];
						$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['meta']['matchName'] = $PoolMatchesShort[$myRow->MatchNo];
					}
					if($myRow->EvElimType==4 and isset($PoolMatchesShortWA[$myRow->MatchNo])) {
						$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['meta']['phaseName'] = $PoolMatchesPhasesWA[$myRow->Phase];
						$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['meta']['matchName'] = $PoolMatchesShortWA[$myRow->MatchNo];
					}
					$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['meta']['FinElimChooser'] = $myRow->FinElimChooser;
					$this->data['sections'][$myRow->Event]['meta']['phaseNames'][$myRow->Phase]=$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['meta']['phaseName'];
				}

				$athlete=($myRow->Athlete ?? '');
				if(!trim($athlete)) {
					if($myRow->EvElimType==3 and isset($PoolMatches[$myRow->MatchNo])) {
						$athlete=$PoolMatches[$myRow->MatchNo];
					} elseif($myRow->EvElimType==4 and isset($PoolMatchesWA[$myRow->MatchNo])) {
						$athlete=$PoolMatchesWA[$myRow->MatchNo];
					}
				}
				$oppAthlete=($myRow->OppAthlete ?? '');
				if(!trim($oppAthlete)) {
					if($myRow->EvElimType==3 and isset($PoolMatches[$myRow->OppMatchNo])) {
						$oppAthlete=$PoolMatches[$myRow->OppMatchNo];
					} elseif($myRow->EvElimType==4 and isset($PoolMatchesWA[$myRow->OppMatchNo])) {
						$oppAthlete=$PoolMatchesWA[$myRow->OppMatchNo];
					}
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
					'coach' => $myRow->Coach,
					'coachGivName' => $myRow->CoachGivName,
					'coachFamName' => $myRow->CoachFamName,
					'coachCode' => $myRow->CoachCode,
					'coachCountry' => $myRow->CoachCountry,
					'coachGender' => $myRow->CoachGender,
					'bib' => $myRow->Bib,
					'localBib' => $myRow->LocalBib,
					'odfMatchName' => $myRow->OdfMatchName ? $myRow->OdfMatchName : '',
                    'odfUnitcode' => $myRow->OdfUnitCode ? $myRow->EvOdfCode.$myRow->OdfUnitCode : '',
					'odfPath' => $myRow->OdfPreviousMatch && intval($myRow->OdfPreviousMatch)==0 ? $myRow->OdfPreviousMatch : get_text(($myRow->MatchNo==2 or $myRow->MatchNo==3) ? 'LoserMatchName' : 'WinnerMatchName', 'ODF', $myRow->OdfPreviousMatch ? $myRow->OdfPreviousMatch : $myRow->PreviousMatchTime),
					'birthDate' => $myRow->BirthDate,
					'id' => $myRow->EnId,
					'target' => ltrim($myRow->Target ?? '', '0'),
					'athlete' => $athlete,
                    'fullName' => ($myRow->NameOrder ? $athlete : $myRow->GivenName . ' ' . $myRow->FamilyNameUpper),
					'familyName' => $myRow->FamilyName,
					'familyNameUpper' => $myRow->FamilyNameUpper,
					'givenName' => $myRow->GivenName,
                    'tvFamilyName' => $myRow->TvFamilyName,
                    'tvGivenName' => $myRow->TvGivenName,
                    'tvInitials' => $myRow->TvInitials,
					'nameOrder' => $myRow->NameOrder,
					'gender' => $myRow->Gender,
					'countryId' => $myRow->CountryId,
					'countryCode' => $myRow->CountryCode,
					'countryName' => $myRow->CountryName,
					'contAssoc' => $myRow->CaCode,
					'memberAssoc' => $myRow->MaCode,
					'countryIocCode'=> $myRow->CountryIocCode,
					'qualRank' => $myRow->ShowRankQual ? $myRow->QualRank : $myRow->IrmTextQual,
					'qualIrm' => $myRow->IrmQual,
					'qualIrmText' => $myRow->IrmTextQual,
					'qualScore'=> $myRow->QualScore,
					'qualNotes'=> $myRow->QualNotes,
					'finRank' => $myRow->FinRank,
					'showRank' => $myRow->ShowRankFin,
					'finIrm' => $myRow->IrmFin,
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
					'tie'=> $myRow->Tie,
					'arrowstring'=> $myRow->ArrowString,
				 	'tiebreak'=> $myRow->TieBreak,
				 	'closest' => $myRow->TieClosest,
				 	'tiebreakDecoded'=> $myRow->TieDecoded,
                    'arrowpositionAvailable'=>($myRow->ArrowPosition != ''),
					'status'=>$myRow->Status,
					'scoreConfirmed'=>$myRow->Confirmed,
					'record' => $this->ManageBitRecord($myRow->RecBitLevel, $myRow->CaCode, $myRow->MaCode, $myRow->EvIsPara),
					'shootFirst'=>$myRow->ShootFirst,
				 	'position'=> $myRow->QualRank ? $myRow->QualRank : ($myRow->Position>$myRow->EvNumQualified ? 0 : $myRow->Position),
				 	'saved'=> ($myRow->Position>0 and $myRow->Position<=SavedInPhase($myRow->EvFinalFirstPhase)),
//
					'oppLastUpdated' => $myRow->OppLastUpdated,
					'oppMatchNo' => $myRow->OppMatchNo,
					'oppCoach' => $myRow->OppCoach,
					'oppCoachGivName' => $myRow->OppCoachGivName,
					'oppCoachFamName' => $myRow->OppCoachFamName,
					'oppCoachCode' => $myRow->OppCoachCode,
					'oppCoachCountry' => $myRow->OppCoachCountry,
					'oppCoachGender' => $myRow->OppCoachGender,
					'oppBib' => $myRow->OppBib,
					'oppLocalBib' => $myRow->OppLocalBib,
					'oppOdfMatchName' => $myRow->OppOdfMatchName,
					'oppOdfPath' => $myRow->OppOdfPreviousMatch && intval($myRow->OppOdfPreviousMatch)==0 ? $myRow->OppOdfPreviousMatch : get_text(($myRow->OppMatchNo==2 or $myRow->OppMatchNo==3) ? 'LoserMatchName' : 'WinnerMatchName', 'ODF', $myRow->OppOdfPreviousMatch ? $myRow->OppOdfPreviousMatch : $myRow->OppPreviousMatchTime),
					'oppBirthDate' => $myRow->OppBirthDate,
					'oppId' => $myRow->OppEnId,
					'oppTarget' => ltrim($myRow->OppTarget ?? '','0'),
					'oppAthlete' => $oppAthlete,
                    'oppFullName' => ($myRow->OppNameOrder ? $oppAthlete : $myRow->OppGivenName . ' ' . $myRow->OppFamilyNameUpper),
					'oppFamilyName' => $myRow->OppFamilyName,
					'oppFamilyNameUpper' => $myRow->OppFamilyNameUpper,
					'oppGivenName' => $myRow->OppGivenName,
					'oppTvFamilyName' => $myRow->OppTvFamilyName,
					'oppTvGivenName' => $myRow->OppTvGivenName,
					'oppTvInitials' => $myRow->OppTvInitials,
					'oppNameOrder' => $myRow->OppNameOrder,
					'oppGender' => $myRow->OppGender,
					'oppCountryId' => $myRow->OppCountryId,
					'oppCountryCode' => $myRow->OppCountryCode,
					'oppCountryName' => $myRow->OppCountryName,
					'oppContAssoc' => $myRow->OppCaCode,
					'oppMemberAssoc' => $myRow->OppMaCode,
					'oppCountryIocCode'=> $myRow->OppCountryIocCode,
					'oppQualRank' => $myRow->OppShowRankQual ? $myRow->OppQualRank : $myRow->OppIrmTextQual,
					'oppQualIrm' => $myRow->OppIrmQual,
					'oppQualIrmText' => $myRow->OppIrmTextQual,
					'oppQualScore'=> $myRow->OppQualScore,
					'oppQualNotes'=> $myRow->OppQualNotes,
					'oppFinRank' => $myRow->OppFinRank,
					'oppShowRank' => $myRow->OppShowRankFin,
					'oppFinIrm' => $myRow->OppIrmFin,
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
					'oppTie'=> $myRow->OppTie,
					'oppArrowstring'=> $myRow->OppArrowString,
				 	'oppTiebreak'=> $myRow->OppTieBreak,
                    'oppClosest' => $myRow->OppTieClosest,
                    'oppTiebreakDecoded'=> $myRow->OppTieDecoded,
                    'oppArrowpositionAvailable'=>($myRow->OppArrowPosition != ''),
					'oppStatus'=>$myRow->OppStatus,
					'oppScoreConfirmed'=>$myRow->OppConfirmed,
					'oppRecord' => $this->ManageBitRecord($myRow->OppRecBitLevel, $myRow->OppCaCode, $myRow->OppMaCode, $myRow->EvIsPara),
					'oppShootFirst'=>$myRow->OppShootFirst,
				 	'oppPosition'=> $myRow->OppQualRank ? $myRow->OppQualRank : ($myRow->OppPosition>$myRow->EvNumQualified ? 0 : $myRow->OppPosition),
				 	'oppSaved'=> ($myRow->OppPosition>0 and $myRow->OppPosition<=SavedInPhase($myRow->EvFinalFirstPhase)),
					);

                if(!empty($this->opts['extended'])) {
                    $item['arrowPosition']= ($myRow->ArrowPosition == '' ? array() : json_decode($myRow->ArrowPosition, true));
                    $item['tiePosition']= ($myRow->TiePosition != '' and $tmp=json_decode($myRow->TiePosition, true)) ? $tmp : array();
                    $item['oppArrowPosition']= ($myRow->OppArrowPosition == '' ? array() : json_decode($myRow->OppArrowPosition, true));
                    $item['oppTiePosition']= ($myRow->OppTiePosition != '' and $tmp=json_decode($myRow->OppTiePosition, true)) ? $tmp : array();
					$item['review1']=$myRow->Review1;
				 	$item['review2']=$myRow->Review2;
					$item['oppReview1']=$myRow->OppReview1;
				 	$item['oppReview2']=$myRow->OppReview2;
					$item['reviewUpdate'] = $myRow->ReviewUpdate;
				}

				$this->data['sections'][$myRow->Event]['phases'][$myRow->Phase]['items'][]=$item;

				$curEvent='';
				$curPhase='';
				$section=null;

			}
		}
	}